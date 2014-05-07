#!/usr/bin/php
<?php
	/* Challenge 5 - Tribblemaker
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 * http://en.wikipedia.org/wiki/Conway%27s_Game_of_Life
	 */

	$GLOBALS['collection'] = array();
	$GLOBALS['data'] = array();
	/*$GLOBALS['data'] = array(
		array(1,0,0,0,0,0,0,1),
		array(0,0,0,0,0,0,0,0),
		array(0,0,0,1,0,0,0,0),
		array(0,0,0,1,0,0,0,0),
		array(0,0,0,1,0,0,0,0),
		array(0,0,0,0,0,0,0,0),
		array(0,0,0,0,0,0,0,0),
		array(1,0,0,0,0,0,0,1)
	);*/

	/* INI-data input */
	$i = file('php://stdin');
	$map = function($n){return $n == 'X' ? 1 : 0;};
	while($l = array_shift($i)){
		$GLOBALS['data'][] = array_map($map,str_split($l));
	}
	/* END-data input */

	$GLOBALS['data2'] = $GLOBALS['data'];
	$GLOBALS['collection'][] = $GLOBALS['data'];
	$i = 1;while(!($l = findLoop()) && $l === false && ($i++)<101){nextGeneration();}
	echo $l,' ',count($GLOBALS['collection'])-$l-1;
exit;

	//paint(0);
	//paint(1);
	//paint(2);
	//paint(3);

	function findLoop(){
		$comp = end($GLOBALS['collection']);
		$k = count($GLOBALS['collection'])-1;
		$found = false;
		for($i = 0;$i < $k;$i++){
			if($GLOBALS['collection'][$i] == $comp){$found = $i;break;}
		}
		return $found;
	}

	function nextGeneration(){
		for($y = 0;$y<8;$y++){
			for($x = 0;$x<8;$x++){
				$n = getNeighbours($x,$y);
				if($n < 2){$GLOBALS['data2'][$y][$x] = 0;continue;}
				if($n > 3){$GLOBALS['data2'][$y][$x] = 0;continue;}
				if($n == 3){$GLOBALS['data2'][$y][$x] = 1;continue;}
				if($n > 2 && $n < 4){continue;}
			}
		}
		$GLOBALS['collection'][] = $GLOBALS['data'] = $GLOBALS['data2'];
	}

	function paint($num){
		for($y = 0;$y<8;$y++){
			for($x = 0;$x<8;$x++){
				echo $GLOBALS['collection'][$num][$y][$x] ? 'X' : '-';
			}
			echo PHP_EOL;
		}
	}

	function getNeighbours($x,$y){
		$c = 0;
		if(isset($GLOBALS['data'][$y-1][$x-1]) && $GLOBALS['data'][$y-1][$x-1]){$c++;}
		if(isset($GLOBALS['data'][$y][$x-1]) && $GLOBALS['data'][$y][$x-1]){$c++;}
		if(isset($GLOBALS['data'][$y+1][$x-1]) && $GLOBALS['data'][$y+1][$x-1]){$c++;}

		if(isset($GLOBALS['data'][$y-1][$x]) && $GLOBALS['data'][$y-1][$x]){$c++;}
		/*if(isset($GLOBALS['data'][$y][$x]) && $GLOBALS['data'][$y][$x]){$c++;}*/
		if(isset($GLOBALS['data'][$y+1][$x]) && $GLOBALS['data'][$y+1][$x]){$c++;}

		if(isset($GLOBALS['data'][$y-1][$x+1]) && $GLOBALS['data'][$y-1][$x+1]){$c++;}
		if(isset($GLOBALS['data'][$y][$x+1]) && $GLOBALS['data'][$y][$x+1]){$c++;}
		if(isset($GLOBALS['data'][$y+1][$x+1]) && $GLOBALS['data'][$y+1][$x+1]){$c++;}

		return $c;
	}
?>
