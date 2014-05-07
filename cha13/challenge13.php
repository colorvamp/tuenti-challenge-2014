#!/usr/bin/php
<?php
	/* Challenge 13 - Tuenti Timing Auth
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 * https://www.youtube.com/watch?v=NS6GIssprCU
	 */

	$i = file('php://stdin');
	$input = trim(array_shift($i));

	include_once('inc.html.patched.php');
	$url = 'http://54.83.207.90:4242/index.py?input='.$input.'&debug=1';
	$chars = str_split('0123456789abcdefghijklmnopqrstuvwxyz');

	//$key = '25fe20d680';
	$key = '';
	$i = 15;while($i--){
		$com = array();
		foreach($chars as $k=>$c){
			$data = html_petition($url,array('post'=>array('input'=>$input,'key'=>$key.$c)));
			if(!strpos($data['pageContent'],'Oh, god, you got it wrong!')){$key .= $c;break 2;}
			$r = preg_match('/Total run: (?<time>[0-9\.e\-]+)/',$data['pageContent'],$m);
			$m['time'] = floatval(number_format($m['time'],15));
			$com[$k] = $m['time'];
			//echo $key.$c.' : '.$m['time'].PHP_EOL;
		}
		$o = max($com);
		$o = array_search($o,$com);
		$key .= $chars[$o];
	}

	echo $key.PHP_EOL;
