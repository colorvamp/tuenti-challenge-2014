#!/usr/bin/php
<?php
	/* Challenge 15 - Take a corner
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */

	$i = file('php://stdin');
	//$i = explode(PHP_EOL,$input);
	$num = trim(array_shift($i));
	while($num--){
		$l = trim(array_shift($i));
		$l = explode(' ',$l);
		$desk = array();
		$GLOBALS['player'] = str_replace(array('White','Black'),array('O','X'),$l[0]);
		$GLOBALS['enemy'] = $GLOBALS['player'] == 'O' ? 'X' : 'O';
		$GLOBALS['topMovements'] = $l[2];
		$y = 0;while($y < 8){
			$l = trim(array_shift($i));
			$c = str_split($l);
			$desk[$y] = $c;
			$y++;
		}

		$GLOBALS['best'] = false;
		$movs = getAvailable($desk,$GLOBALS['player']);
		$max = false;$candidate = false;
		$chars = movs2Chars($movs);
		foreach($movs as $k=>$mov){
			$desk2 = setCell($desk,$mov['x'],$mov['y'],$GLOBALS['player']);
			$d = minimax($desk2,$GLOBALS['enemy']);
			if($max === false || $d > $max){$max = $d;$candidate = $chars[$k];}
			//echo $chars[$k].' '.$d.PHP_EOL;
		}
		if(!$candidate || $max < 50){echo 'Impossible'.PHP_EOL;continue;}
		echo $candidate.PHP_EOL;
	}


	function minimax($desk,$p,$depth = 0){
		# if maxed out, return the node heuristic value
		if($depth > $GLOBALS['topMovements']){return getScore($desk,$p);}
		$e = $p == 'O' ? 'X' : 'O';

		$movs = getAvailable($desk,$p);
		if(!$movs){return getScore($desk,$p);}

		# see if there's a winner
		if($p == $GLOBALS['player'] && (isset($movs['0.0']) || isset($movs['0.7']) || isset($movs['7.0']) || isset($movs['7.7']))){
			return 50+getScore($desk,$p);
		}

		# figure who the other player is:
		//if player == PLAYER_1: other_player = PLAYER_2
		//else:                  other_player = PLAYER_1

		# init alpha with this starting node's value.  The current player
		# will raise this value; the other player will lower it.
		if($p == $GLOBALS['player']){
			$alpha = -100;
		}else{
			$alpha = +100;
		}

		# for all those moves, make a speculative copy of this board
		foreach($movs as $mov){
			$desk2 = $desk;
			$desk2 = setCell($desk2,$mov['x'],$mov['y'],$p);

			# and recurse down to find the alpha value of the subtree!
			$subalpha = minimax($desk2,$e,$depth+1);

			# If we're the current player, we want the maximum valued
			# node from all the child nodes. If we're the opponent
			# we want the minimum:
			if($p == $GLOBALS['player']){
				# if we're at the root, remember the best move
				if($depth == 0 and $alpha <= $subalpha){$GLOBALS['best'] = $mov;}
				$alpha = max($alpha,$subalpha);  # push alpha up (maximize)
			}else{
				$alpha = min($alpha,$subalpha);  # push alpha down (minimize)
			}
		}
		return $alpha;
	}

	function paintDesk($desk){
		foreach($desk as $y=>$l){
			foreach($l as $x=>$v){
				echo $v;
			}
			echo PHP_EOL;
		}
	}
	function getScore($desk,$p){
		$e = $p == 'O' ? 'X' : 'O';
		$movs = getAvailable($desk,$e);
		$score = 0;
		foreach($desk as $y=>$l){
			foreach($l as $x=>$v){
				if($v == $p){$score++;continue;}
				if($v == '.'){$score += 0.5;continue;}
			}
		}
		/* Oponent's movility */
		$score -= count($movs);
		return $score;
	}
	function movs2Chars($movs){
		$a = range('a','h');
		$n = range(1,8);
		foreach($movs as $k=>$mov){
			$movs[$k] = $a[$mov['x']].$n[$mov['y']];
		}
		return $movs;
	}
	function getMaxDamage($desk,$movs,$p){
		$candidates = array();
		foreach($movs as $mov){
			$c = array();
			setCell($desk,$mov['x'],$mov['y'],$p,$c);
			$c = count($c);
			$candidates[$c][] = $mov;
		}
		krsort($candidates);
		return count($candidates) ? array_shift($candidates) : array($candidates);
	}
	function getAvailable($desk,$p){
		$movs = array();
		foreach($desk as $y=>$l){foreach($l as $x=>$v){
			if($v !== '.'){continue;}
			$c = getCellMovements($desk,$x,$y,$p);
			if($c){$movs[$x.'.'.$y] = array('x'=>$x,'y'=>$y);}
		}}
		return $movs;
	}
	function setCell($desk,$x,$y,$p,&$cells = array()){
		$cells = array(array('y'=>$y,'x'=>$x));

		/* TOP-RIGHT */
		$paint = false;$cand = array();
		$paint = false;$i = 1;while(isset($desk[$y-$i][$x-$i])){if($desk[$y-$i][$x-$i] == '.'){break;}if($desk[$y-$i][$x-$i] == $p){$paint = true;break;}$cand[] = array('y'=>$y-$i,'x'=>$x-$i);$i++;}
		if($paint){$cells = array_merge($cells,$cand);}
		/* BOTTOM-RIGHT */
		$paint = false;$cand = array();
		$i = 1;while(isset($desk[$y+$i][$x-$i])){if($desk[$y+$i][$x-$i] == '.'){break;}if($desk[$y+$i][$x-$i] == $p){$paint = true;break;}$cand[] = array('y'=>$y+$i,'x'=>$x-$i);$i++;}
		if($paint){$cells = array_merge($cells,$cand);}
		/* TOP-LEFT */
		$paint = false;$cand = array();
		$i = 1;while(isset($desk[$y-$i][$x+$i])){if($desk[$y-$i][$x+$i] == '.'){break;}if($desk[$y-$i][$x+$i] == $p){$paint = true;break;}$cand[] = array('y'=>$y-$i,'x'=>$x+$i);$i++;}
		if($paint){$cells = array_merge($cells,$cand);}
		/* BOTTOM-LEFT */
		$paint = false;$cand = array();
		$i = 1;while(isset($desk[$y+$i][$x+$i])){if($desk[$y+$i][$x+$i] == '.'){break;}if($desk[$y+$i][$x+$i] == $p){$paint = true;break;}$cand[] = array('y'=>$y+$i,'x'=>$x+$i);$i++;}
		if($paint){$cells = array_merge($cells,$cand);}

		/* RIGHT */
		$paint = false;$cand = array();
		$i = 1;while(isset($desk[$y][$x-$i])){if($desk[$y][$x-$i] == '.'){break;}if($desk[$y][$x-$i] == $p){$paint = true;break;}$cand[] = array('y'=>$y,'x'=>$x-$i);$i++;}
		if($paint){$cells = array_merge($cells,$cand);}
		/* LEFT */
		$paint = false;$cand = array();
		$i = 1;while(isset($desk[$y][$x+$i])){if($desk[$y][$x+$i] == '.'){break;}if($desk[$y][$x+$i] == $p){$paint = true;break;}$cand[] = array('y'=>$y,'x'=>$x+$i);$i++;}
		if($paint){$cells = array_merge($cells,$cand);}
		/* TOP */
		$paint = false;$cand = array();
		$i = 1;while(isset($desk[$y-$i][$x])){if($desk[$y-$i][$x] == '.'){break;}if($desk[$y-$i][$x] == $p){$paint = true;break;}$cand[] = array('y'=>$y-$i,'x'=>$x);$i++;}
		if($paint){$cells = array_merge($cells,$cand);}
		/* BOTTOM */
		$paint = false;$cand = array();
		$i = 1;while(isset($desk[$y+$i][$x])){if($desk[$y+$i][$x] == '.'){break;}if($desk[$y+$i][$x] == $p){$paint = true;break;}$cand[] = array('y'=>$y+$i,'x'=>$x);$i++;}
		if($paint){$cells = array_merge($cells,$cand);}

		foreach($cells as $cell){
			/* Skip corners */
			if($cell['y'] == 0 && $cell['x'] == 0){continue;}
			if($cell['y'] == 0 && $cell['x'] == 7){continue;}
			if($cell['y'] == 7 && $cell['x'] == 0){continue;}
			if($cell['y'] == 7 && $cell['x'] == 7){continue;}
			$desk[$cell['y']][$cell['x']] = $p;
		}
		return $desk;
	}
	function getCellMovements($desk,$x,$y,$p){
		$movs = 0;
		/* TOP-RIGHT */		$i = 1;while(isset($desk[$y-$i][$x-$i])){if($desk[$y-$i][$x-$i] == '.'){break;}if($desk[$y-$i][$x-$i] == $p){$movs += $i-1;break;}$i++;}
		/* BOTTOM-RIGHT */	$i = 1;while(isset($desk[$y+$i][$x-$i])){if($desk[$y+$i][$x-$i] == '.'){break;}if($desk[$y+$i][$x-$i] == $p){$movs += $i-1;break;}$i++;}
		/* TOP-LEFT */		$i = 1;while(isset($desk[$y-$i][$x+$i])){if($desk[$y-$i][$x+$i] == '.'){break;}if($desk[$y-$i][$x+$i] == $p){$movs += $i-1;break;}$i++;}
		/* BOTTOM-LEFT */	$i = 1;while(isset($desk[$y+$i][$x+$i])){if($desk[$y+$i][$x+$i] == '.'){break;}if($desk[$y+$i][$x+$i] == $p){$movs += $i-1;break;}$i++;}

		/* RIGHT */		$i = 1;while(isset($desk[$y][$x-$i])){if($desk[$y][$x-$i] == '.'){break;}if($desk[$y][$x-$i] == $p){$movs += $i-1;break;}$i++;}
		/* LEFT */		$i = 1;while(isset($desk[$y][$x+$i])){if($desk[$y][$x+$i] == '.'){break;}if($desk[$y][$x+$i] == $p){$movs += $i-1;break;}$i++;}
		/* TOP */		$i = 1;while(isset($desk[$y-$i][$x])){if($desk[$y-$i][$x] == '.'){break;}if($desk[$y-$i][$x] == $p){$movs += $i-1;break;}$i++;}
		/* BOTTOM */		$i = 1;while(isset($desk[$y+$i][$x])){if($desk[$y+$i][$x] == '.'){break;}if($desk[$y+$i][$x] == $p){$movs += $i-1;break;}$i++;}
		return $movs;
	}
?>
