#!/usr/bin/php
<?php
	/* Challenge 1 - Anonymous Poll
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */

	$i = file('php://stdin');
	/*$i = explode(PHP_EOL,'5
	M,21,Human Resources Management,3
	F,20,Systems Engineering,2
	M,20,Manufacturing Engineering,3
	M,18,Electrical Engineering,4
	F,25,Construction Engineering,4');//*/
	array_shift($i);

	/* The file is pretty small, but for things that I think could grow up in memory,
	 * I prefer to not load the file entirely in memory but iterate every single line,
	 * maybe slow but much less memory hungry. */
	$fp = fopen('students','r');
	$k = 0;while($line = array_shift($i)){$k++;
		fseek($fp,0);
		$found = false;
		$output =  'Case #'.$k.': ';
		while($student = fgets($fp)){
			if(strpos($student,$line)){
				$found = true;
				$s = explode(',',$student);
				$output .= $s[0].',';
			}
		}
		if(!$found){
			$output .= 'NONE,';
		}
		$output = substr($output,0,-1);
		if(strpos($output,',')){
			$space = strpos($output,' ',6);
			$persons = explode(',',substr($output,$space+1));
			sort($persons);
			$output = substr($output,0,$space+1).implode(',',$persons);
		}
		echo $output,PHP_EOL;
	}
	fclose($fp);
?>
