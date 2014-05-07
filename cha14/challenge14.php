#!/usr/bin/php
<?php
	//ini_set('memory_limit','512M');
	/* Challenge 14 - Train Empire
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 * IMPORTANT: https://www.youtube.com/watch?v=hHkKJfcBXcw
	 */

	$i = file('php://stdin');
	$num = trim(array_shift($i));
	while($num--){
		//$i = explode(PHP_EOL,$input);
		$l = trim(array_shift($i));
		list($cstations,$ctrains,$fuel) = explode(',',$l);

		/* INI-data processing */
		$GLOBALS['map'] = array();
		$GLOBALS['stations'] = array();
		$GLOBALS['trains'] = array();
		$GLOBALS['points'] = array();
		$GLOBALS['cases'] = array();
		$GLOBALS['sqrt2'] = sqrt(2);
		while($cstations--){
			$l = trim(array_shift($i));
			$l = explode(' ',$l);
			list($x,$y) = explode(',',$l[1]);
			$GLOBALS['map'][$y][$x] = $l[0];
			$GLOBALS['stations'][$l[0]] = array('x'=>$x,'y'=>$y,'target'=>$l[2],'points'=>$l[3],'routes'=>array());
			$GLOBALS['cases'][] = array('s'=>$l[0],'e'=>$l[2],'p'=>$l[3]);
		}

		while($ctrains--){
			$l = trim(array_shift($i));
			$l = explode(' ',$l);
			$station = array_shift($l);
			$GLOBALS['trains'][$station] = true;
			foreach($l as $route){
				$route = explode('-',$route);
				$GLOBALS['stations'][$station]['routes'][$route[0]][] = $route[1];
				$GLOBALS['stations'][$station]['routes'][$route[1]][] = $route[0];
			}

			$GLOBALS['stations'][$station]['points'] = array();
			foreach($GLOBALS['cases'] as $case){
				$s = $case['s'];/* Start */
				$e = $case['e'];/* End */
				$p = $case['p'];/* Points */
				/* v Not a case for this route */
				if(!isset($GLOBALS['stations'][$station]['routes'][$s]) || !isset($GLOBALS['stations'][$station]['routes'][$e])){continue;}
				$GLOBALS['stationPath'] = false;
				findStationPath($station,$s,$e);
				if($GLOBALS['stationPath']){$GLOBALS['stations'][$station]['points'][implode('->',$GLOBALS['stationPath'])] = $p;}
			}
		}

		$score = 0;
		foreach($GLOBALS['trains'] as $train=>$dummy){
			$score += processTrain($train,$fuel);
		}
		echo $score.PHP_EOL;
	}

	function processTrain($train = 'A',$fuel){
		$routes = $GLOBALS['stations'][$train]['routes'];
		$max = array();
		foreach($routes[$train] as $station){
			moveTrain($train,$train,$station,$fuel,array($train),$routes,$max);
		}
		/* INI-update stations */
		$stations = array();
		getPathScore($train,$max['path']);
		/* END-update stations */
		return isset($max['score']) ? $max['score'] : 0;
	}

	function moveTrain($station,$from,$to,$fuel,$path,$routes,&$max){
		$sA = $GLOBALS['stations'][$from];
		$sB = $GLOBALS['stations'][$to];
		if($sA['x'] != $sB['x'] && $sA['y'] != $sB['y']){$fuel -= $GLOBALS['sqrt2'];}
		else{$fuel--;}

		if($fuel < 0 || !$routes[$to]){
			$score = getPathScore($station,$path);
			if(!$max || $max['score'] < $score){$max = array('score'=>$score,'path'=>$path);}
			return;
		}

		$path[] = $to;

		foreach($routes[$to] as $route){
			moveTrain($station,$to,$route,$fuel,$path,$routes,$max);
		}
	}

	function getPathScore($station,$path){
		$str = implode('->',$path);
		$GLOBALS['tmpPath'] = array();
		$GLOBALS['tmpPoints'] = 0;
		foreach($GLOBALS['stations'][$station]['points'] as $case=>$dummy){
			getPathScoreProcess($str,$case,$GLOBALS['stations'][$station]['points'],array());
		}
		return $GLOBALS['tmpPoints'];
	}

	function getPathScoreProcess($str,$case,$cases,$visited,$points = 0){
		if(!strlen($str) || ($p = strpos($str,$case)) === false || isset($visited[$case])){
			if($GLOBALS['tmpPoints'] < $points){$GLOBALS['tmpPoints'] = $points;$GLOBALS['tmpPath'] = $visited;}
			return false;
		}

		$points += $cases[$case];
		$visited[$case] = 1;
		$str = substr($str,$p+strlen($case)-1);
		unset($cases[$case]);
		foreach($cases as $c=>$dummy){
			getPathScoreProcess($str,$c,$cases,$visited,$points);
		}

		if($GLOBALS['tmpPoints'] < $points){$GLOBALS['tmpPoints'] = $points;$GLOBALS['tmpPath'] = $visited;}
	}
	function findStationPath($station,$n,$e,$path = array(),$visited = array()){
		if(isset($visited[$n])){return false;}
		$path[] = $n;
		if($n == $e){$GLOBALS['stationPath'] = $path;return;}
		$visited[$n] = 1;
		if(!isset($GLOBALS['stations'][$station]['routes'][$n])){return false;}
		foreach($GLOBALS['stations'][$station]['routes'][$n] as $next){
			findStationPath($station,$next,$e,$path,$visited);
		}
	}
?>
