#!/usr/bin/php
<?php
	/* Challenge 11 - Pheasant
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */

	//precompute(array_reverse(array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50','51','52','53','54','55','56','57','58','59','60','61','62','63','64','65','66','67','68','69','70','71','72','73','74','75','76','77','78','79','80','81','82','83','84','85','86','87','88','89','90','91','92','93','94','95','96','97','98','99')));
	//precompute(array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50','51','52','53','54','55','56','57','58','59','60','61','62','63','64','65','66','67','68','69','70','71','72','73','74','75','76','77','78','79','80','81','82','83','84','85','86','87','88','89','90','91','92','93','94','95','96','97','98','99'));
	//precompute(array('54','53','52','51','50'));
	//precompute(array('59','58','57','56','55'));
	//precompute(array('47'));

	/* INI-data input */
	$i = file('php://stdin');
	//file_put_contents('data1',implode(PHP_EOL,$i));
	while($input = array_shift($i)){
		//$input = '7; 17023,hNMqffpeMSqUqNfbvSDImDRQmtSbU; 57970,ZWIiWjrkhhlEJcSnOuCAnXQqexSxC; 88916,aMLtuoZOwrydHyXBJCexjkzeBGKvF; 94293,CItCenDzXLglVHJqZSrkdGWatYnrg; 21533,mtGlWjHkfkthdDXzRCfsjtVssCNDO; 89112,fXMAPXYluCCUHpefOrNTvIOUtyrJQ; 97879,rfUvNLbFhqeqglWWYztSgRXVoHlcn';
		//$input = '7; 97879,rfUvNLbFhqeqglWWYztSgRXVoHlcn';
		$data = explode('; ',trim($input));
		$num = array_shift($data);
		$thread = array();
		while($h = array_shift($data)){
			$h = explode(',',$h);
			$d = bruteForce($h[0],$h[1]);
			$r = preg_match_all('/[0-9]+ ([0-9]+) ([0-9]+)/',$d,$lines);
			foreach($lines[0] as $k=>$v){
				//$thread[$lines[1][$k]] = $lines[2][$k];
				$thread[] = $lines[2][$k];
			}
		}
		rsort($thread);
		$thread = array_slice($thread,0,$num,true);
		echo implode(' ',$thread).PHP_EOL;
	}
	exit;

	//$input = bruteForce('17023','hNMqffpeMSqUqNfbvSDImDRQmtSbU');
	//var_dump($input);

	function precompute($targets = array('89','88','87','86','85')){
		/* This way you can distribute workloads between cores/computers/friend's pcs/servers and precompute passwords */
		$submit = file_get_contents('data1');
		$r = preg_match_all('/([0-9]+),([a-zA-Z]+)/',$submit,$m);

		$usersBySufix = array();
		foreach($m[1] as $k=>$v){
			$sufix = substr($v,-2);
			$usersBySufix[$sufix][] = array('user'=>$v,'keyString'=>$m[2][$k]);
		}
		$m = null;
		ksort($usersBySufix);

		foreach($targets as $target){
			//$target = '36';
			if(!isset($usersBySufix[$target])){continue;}
			$total = count($usersBySufix[$target]);
			$done = 0;
			echo 'PROCESSING: '.$target.PHP_EOL;
			foreach($usersBySufix[$target] as $node){
				cli_pbar($done,$total);
				$key = bruteForce($node['user'],$node['keyString']);
				$done++;
			}
			cli_pbar($total,$total);
		}
	}

	function bruteForce($user,$keyString){
		$sufix = substr($user,-2);
		$feed = './feeds/encrypted/'.$sufix.'/'.$user.'.feed';
		$feed = file_get_contents($feed);

		$cacheFile = './cache/'.$user;
		if(file_exists($cacheFile)){
			$key = file_get_contents($cacheFile);
			return mcrypt_decrypt('rijndael-128',$key,$feed,'ecb');
		}

		$file = './feeds/last_times/'.$sufix.'/'.$user.'.timestamp';
		$timestamp = file_get_contents($file);
		$validation = $user.' '.$timestamp;

		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$total = strlen($chars);
		for($i=0;$i<$total;$i++){for($j=0;$j<$total;$j++){for($k=0;$k<$total;$k++){
			$key = $keyString.$chars[$i].$chars[$j].$chars[$k];
			$r = mcrypt_decrypt('rijndael-128',$key,$feed,'ecb');
			if(strpos($r,$validation) === false){continue;}
			file_put_contents($cacheFile,$key);
			return $r;
		}}}
	}

	/*for($x=1;$x<=100;$x++){cli_pbar($x,100);usleep(100000);}*/
	function cli_pbar($done,$total,$size=30){
		static $startTime;
		/* Si superamos los límites, algo ha ido mal */
		if($done > $total){return false;}
		if(!$startTime){$startTime = time();}
		$now = time();
		$perc = floatval($done/$total);
		$bar = floor($perc*$size);
		$status_bar = "\r[";
		$status_bar .= str_repeat('=',$bar);
		if($bar<$size){$status_bar .= '>'.str_repeat(' ',$size-$bar);}
		else{$status_bar .= '=';}
		$disp = number_format($perc*100,0);
		$status_bar .= '] '.$disp.'% '.$done.'/'.$total;
		$rate = ($done) ? ($now-$startTime)/$done : 0;
		$left = $total-$done;
		$eta = round($rate*$left,2);
		$elapsed = $now-$startTime;
		$status_bar.= ' remain: '.number_format($eta).' sec. elap: '.number_format($elapsed).' sec.';
		echo $status_bar.' ';flush();
		/* Cuando terminamos, pintamos una nueva line y reseteamos el tiempo */
		if($done == $total){$startTime = false;echo PHP_EOL;}
	}
?>
