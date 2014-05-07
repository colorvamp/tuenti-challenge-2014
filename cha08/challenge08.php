#!/usr/bin/php
<?php
	/* Challenge 8 - Tuenti Restructuration
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 */

$input = 'Javier, Ivan, Andrew
Ignacio, , Einar
Goran, Marcio, Vincent

Vincent, Goran, Ignacio
Marcio, , Ivan
Einar, Andrew, Javier';
$input = explode(PHP_EOL.PHP_EOL,str_replace(' ','',$input));

	$input[0] = explode(',',str_replace(PHP_EOL,',',$input[0]));
	$data = array($input[0][0],$input[0][1],$input[0][2],$input[0][5],$input[0][8],$input[0][7],$input[0][6],$input[0][3]);
	$input[1] = explode(',',str_replace(PHP_EOL,',',$input[1]));
	$target = array($input[1][0],$input[1][1],$input[1][2],$input[1][5],$input[1][8],$input[1][7],$input[1][6],$input[1][3]);

	//$data = array('Javier','Andrew','Vincent','Bartek','Ignacio','Goran','Einar','Marcio');
	//$target = array('Andrew','Javier','Vincent','Bartek','Ignacio','Goran','Einar','Marcio');

	$target = array_flip($target);
	$tmp = $data;foreach($tmp as $k=>$v){$tmp[$k] = $target[$v];}
print_r($target);
print_r($tmp);
exit;

$GLOBALS['count'] = 0;
$tmp = bubbleSort($tmp);
//print_r($tmp);
echo $GLOBALS['count'];


	function bubbleSort($arr){
		$size = count($arr);
		for($i=0;$i<$size;$i++){
			for($j=0;$j<$size-1-$i;$j++){
				if($arr[$j+1] < $arr[$j]){
					swap($arr,$j,$j+1);
					$GLOBALS['count']++;
				}
			}
		}
		return $arr;
	}

	function swap(&$arr,$a,$b){
		$tmp = $arr[$a];
		$arr[$a] = $arr[$b];
		$arr[$b] = $tmp;
	}
exit;
?>
