#!/usr/bin/php
<?php
	/* Challenge 10 - Random Password
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */

	include_once('inc.html.php');
	$url = 'http://random.contest.tuenti.net/';
	/* This was the pid num */
	$pid = 1336;

	/* INI-data input */
	$i = file('php://stdin');
	$input = trim(array_shift($i));
	/* END-data input */

	$data = html_petition($url);
	$r = preg_match('/Date: ([a-zA-Z0-9 :,]+)/',$data['pageHeader'],$m);
	if(!$r){echo 'date error';exit;}
	$dateString = $m[1];

	$t = strtotime($dateString);
	srand(mktime(date('H',$t),date('i',$t),0)*$pid);
	$rand = rand();
	$data = html_petition($url.'?input='.$input.'&password='.$rand);
	echo $data['pageContent'].PHP_EOL;exit;

	exit;
	/* INI-guessing pid */
	$url = 'http://localhost/b/index2.php?input=a93018a023';
	$url = 'http://random.contest.tuenti.net/?input=a93018a023';
	$data = html_petition($url);
	$r = preg_match('/Date: ([a-zA-Z0-9 :,]+)/',$data['pageHeader'],$m);
	if(!$r){echo 'date error';exit;}
	$dateString = $m[1];

	//for($i = 10952;$i < 10992;$i++){//My local testing
	//for($i = 200;$i < 4000;$i++){
	for($i = 1300;$i < 4000;$i++){
		echo 'pid: '.$i.PHP_EOL;
		$t = strtotime($dateString);
		srand(mktime(date('H',$t),date('i',$t),0)*$i);
		$rand = rand();
		$data = html_petition($url.'&password='.$rand);
		/* INI-Actualizamos la fecha */
		$r = preg_match('/Date: ([a-zA-Z0-9 :,]+)/',$data['pageHeader'],$m);
		if(!$r){echo 'date error';exit;}
		$dateString = $m[1];
		/* END-Actualizamos la fecha */

		if(strpos($data['pageContent'],'wrong!') === false){
			print_r($data);exit;
		}
	}
	/* END-guessing pid */
?>
