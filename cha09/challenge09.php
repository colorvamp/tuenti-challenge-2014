#!/usr/bin/php
<?php
	/* Challenge 9 - Bendito Caos
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */

	/* INI-data input */
	$i = file('php://stdin');
	//file_put_contents('data',implode('',$i));
	$num = array_shift($i);
	while($cityName = trim(array_shift($i))){
		$speed = trim(array_shift($i));
		list($normal,$dirt) = explode(' ',$speed);
		$roads = trim(array_shift($i));
		list($intersections,$lines) = explode(' ',$roads);
		$data = array();while($lines--){$data[] = trim(array_shift($i));}
		calculate($cityName,$normal,$dirt,$data);
	}
	/* END-data input */

	function calculate($cityName,$normal,$dirt,$data){
		$GLOBALS['road'] = array('normal'=>$normal,'dirt'=>$dirt);
		$GLOBALS['tree'] = array();

		foreach($data as $l){
			$u = explode(' ',$l);
			if($u[1] === $cityName){continue;}
			if($u[0] === 'AwesomeVille'){continue;}
			$GLOBALS['tree'][$u[0]][$u[1]] = array('t'=>$u[2],'l'=>$u[3]);
		}

		//print_r($GLOBALS['tree']);exit;
		$total = 0;
		foreach($GLOBALS['tree'][$cityName] as $p=>$v){
			$stream = ($GLOBALS['road'][$v['t']]*1000)*$v['l'];
			$r = findPath($p,$stream,array($cityName=>1));
			$total += $r;
		}
		echo $cityName.' '.($total/5).PHP_EOL;
	}

	function findPath($n,$stream,$visited){
		if($n === 'AwesomeVille'){return $stream;}
		if(!isset($GLOBALS['tree'][$n])){return 0;}
		if(isset($visited[$n])){return 0;}
		$visited[$n] = 1;
		$initStream = $stream;
		$r = 0;
		foreach($GLOBALS['tree'][$n] as $p=>$v){
			if($stream < 1){continue;}
			$maxstream = ($GLOBALS['road'][$v['t']]*1000)*$v['l'];
			if(isset($visited[$p])){continue;}
			$pstream = ($stream < $maxstream) ? $stream : $maxstream;
			//echo count($visited).PHP_EOL;
			//echo $n.' '.$p.' : '.$stream.' '.$maxstream.' '.$r.PHP_EOL;
			//if($stream < 0){exit;}
			$r += findPath($p,$pstream,$visited);
			$stream -= $r;
		}

		if($r > $initStream){return $initStream;}
		return $r;
	}
?>
