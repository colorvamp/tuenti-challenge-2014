#!/usr/bin/php
<?php
	/* Challenge 12 - Taxi Driver
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */

	$i = file('php://stdin');
	$cases = trim(array_shift($i));
	$totalCases = $cases;

	while($cases--){
		$l = trim(array_shift($i));
		list($w,$h) = explode(' ',$l);

		$t = $h;
		$startX = $startY = false;
		$GLOBALS['city'] = array();
		while($h--){
			$l = trim(array_shift($i));
			if(($pos = strpos($l,'S')) !== false){$startX = $pos;$startY = $t-$h-1;}
			$l = str_split($l);
			$GLOBALS['city'][] = $l;
		}

		$GLOBALS['found'] = false;
		$GLOBALS['movs'] = false;
		$GLOBALS['movskeys'] = false;
		$GLOBALS['sort'] = array();
		move($startX,$startY,'t',0,array());
		move($startX,$startY,'r',0,array());
		move($startX,$startY,'l',0,array());
		move($startX,$startY,'b',0,array());
		//echo 'end'.PHP_EOL;
		//echo $GLOBALS['found'].PHP_EOL;
		//echo count($GLOBALS['movs']).PHP_EOL;
		//print_r($GLOBALS['movs']);
		if(!$GLOBALS['found']){$GLOBALS['found'] = 'ERROR';}
		echo 'Case #'.($totalCases-$cases).': '.$GLOBALS['found'].PHP_EOL;
	}

	function move($x,$y,$d,$mov,$path){
		if($GLOBALS['found'] && ($GLOBALS['found'] < $mov)){return false;}
		if(!isset($GLOBALS['city'][$y][$x])){return false;}
		if($GLOBALS['city'][$y][$x] == '#'){return false;}
		if($GLOBALS['city'][$y][$x] == 'S' && $mov){return false;}
		if($GLOBALS['city'][$y][$x] == 'X'){
			if($GLOBALS['found'] === false || $GLOBALS['found'] > $mov){
				$GLOBALS['found'] = $mov;
				$GLOBALS['movs'] = $path;
				return true;
			}
			return false;
		}

		$pos = $x.'-'.$y.'-'.$d;
		if(isset($GLOBALS['sort'][$pos])){
			if(count($path) >= count($GLOBALS['sort'][$pos])){
				return false;
			}
		}
		$GLOBALS['sort'][$pos] = $path;
		$path[] = $pos;
		$mov++;

		if($d == 'l'){
			move($x+1,$y,'l',$mov,$path);
			move($x,$y+1,'b',$mov,$path);
		}
		if($d == 'b'){
			move($x,$y+1,'b',$mov,$path);
			move($x-1,$y,'r',$mov,$path);
		}
		if($d == 'r'){
			move($x-1,$y,'r',$mov,$path);
			move($x,$y-1,'t',$mov,$path);
		}
		if($d == 't'){
			move($x,$y-1,'t',$mov,$path);
			move($x+1,$y,'l',$mov,$path);
		}
	}
?>
