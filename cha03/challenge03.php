#!/usr/bin/php
<?php
	/* Challenge 3 - The Gambler’s Club - Monkey Island 2
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 * I got this problem cause i recognised a few square roots, and then http://es.wikipedia.org/wiki/Terna_pitag%C3%B3rica */

	$i = file('php://stdin');
	$l = array_shift($i);//*/

	while($l = array_shift($i)){
		list($x,$y) = explode(' ',$l);
		echo round(sqrt(pow($x,2)+pow($y,2)),2).PHP_EOL;
	}

	if(0){
		/* To extract the example matrix */
		include_once('inc.html.php');
		$matrix = array();
		for($i=0;$i < 31;$i++){
			for($j=0;$j < 31;$j++){
				$data = html_petition('http://gamblers.contest.tuenti.net/index.php',array('post'=>array('x'=>$i,'y'=>$j)));
				$r = preg_match('/<input[^>]*name=.result. value=.(?<value>[0-9\.]+).[^>]*>/',$data['pageContent'],$m);
				$matrix[$i][$j] = $m['value'];
				echo $i.'.'.$j.PHP_EOL;
			}
		}

		print_r($matrix);
		file_put_contents('data',json_encode($matrix));
	}
?>
