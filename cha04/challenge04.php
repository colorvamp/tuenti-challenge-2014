#!/usr/bin/php
<?php
	/* Challenge 4 - Shape shifters
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */

	/* INI-data input */
	$i = file('php://stdin');
	$init = trim(array_shift($i));
	$end  = trim(array_shift($i));

	$GLOBALS['pos'] = array();
	while($p = array_shift($i)){
		$GLOBALS['pos'][] = trim($p);
	}
	/* END-data input */
	$GLOBALS['changes'] = strlen($init)-1;
	$GLOBALS['min'] = count($GLOBALS['pos'])+1;
	$GLOBALS['path'] = array();
	$thread = array('path'=>array($init),'count'=>1,'current'=>$init,'end'=>$end);

	/* INI-Dijkstra */
	$keys = array_flip(array_keys($GLOBALS['pos']));
	foreach($keys as $k=>$dummy){
		$c = compare($init,$GLOBALS['pos'][$k]);
		if($c != $GLOBALS['changes']){continue;}
		move($k,$keys,$thread);
	}
	/* END-Dijkstra */

	echo implode('->',$GLOBALS['path']).PHP_EOL;

	function move($pos,$keys,$thread){
		if(empty($keys)){return;}

		$step = $GLOBALS['pos'][$pos];
		$thread['path'][] = $step;
		$thread['count']++;
		$thread['current'] = $step;
		unset($keys[$pos]);

		/* Check if we have */
		if(compare($step,$thread['end']) == $GLOBALS['changes']){
			if($thread['count'] < $GLOBALS['min']){
				$thread['path'][] = $thread['end'];
				$GLOBALS['min'] = $thread['count'];
				$GLOBALS['path'] = $thread['path'];
			}
			return;
		}

		/* First we filter all posible valid steps */
		foreach($keys as $k=>$dummy){
			$c = compare($step,$GLOBALS['pos'][$k]);
			if($c != $GLOBALS['changes']){continue;}
			move($k,$keys,$thread);
		}
	}

	function compare($str1,$str2){
		$l = strlen($str1);
		$c = 0;
		for($i = 0;$i < $l;$i++){if($str1[$i] == $str2[$i]){$c++;}}
		return $c;
	}
?>
