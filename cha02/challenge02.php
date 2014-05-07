#!/usr/bin/php
<?php
	/* Challenge 2 - F1 - Bird's-eye Circuit
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */

	$i = file('php://stdin');
	$i = array_shift($i);//*/
	//file_put_contents('input1',$i);
	//$i = '------\-/-/-\-----#-------\--/----------------\--\----\---/---';//*/

	$chars = str_split($i);
	$circuit = array();
	$direction = 'left';
	$x = 0;
	$y = 0;
	$startX = $offsetX = 0;
	$startY = $offsetY = 0;
	$last = false;
	foreach($chars as $char){
		switch($char){
			case '#':
				$circuit[$y][$x] = $char;
				$direction = 'left';
				break;
			case '-':
				if($direction == 'top' || $direction == 'bottom'){$circuit[$y][$x] = '|';break;}
				$circuit[$y][$x] = $char;
				break;
			case '\\':
				$circuit[$y][$x] = $char;
				$rep = 0;
				$direction = str_replace('left','bottom',$direction,$rep);if($rep){break;}
				$direction = str_replace('right','top',$direction,$rep);if($rep){break;}
				$direction = str_replace('top','right',$direction,$rep);if($rep){break;}
				$direction = str_replace('bottom','left',$direction,$rep);if($rep){break;}
				break;
			case '/':
				$circuit[$y][$x] = $char;
				$rep = 0;
				$direction = str_replace('bottom','right',$direction,$rep);if($rep){break;}
				$direction = str_replace('right','bottom',$direction,$rep);if($rep){break;}
				$direction = str_replace('top','left',$direction,$rep);if($rep){break;}
				$direction = str_replace('left','top',$direction,$rep);if($rep){break;}
				break;
		}

		switch($direction){
			case 'left':$x++;break;
			case 'right':$x--;break;
			case 'top':$y--;break;
			case 'bottom':$y++;break;
		}
		if($x < $startX){$startX = $x;}
		if($y < $startY){$startY = $y;}
		if($x > $offsetX){$offsetX = $x;}
		if($y > $offsetY){$offsetY = $y;}
		$last = $char;
	}

	ksort($circuit);
	foreach($circuit as $y){
		ksort($y);
		/* Fill the gap */
		$min = min(array_keys($y));
		$max = max(array_keys($y));
		$i = $startX;while($i < $min){$i++;echo ' ';}
		$l = $min;
		foreach($y as $x=>$d){
			while($x > $l+1){$l++;echo ' ';}
			$l = $x;
			echo $d;
		}
		/* The spaces behind */
		while($max < $offsetX){$max++;echo ' ';}
		echo PHP_EOL;
	}
?>
