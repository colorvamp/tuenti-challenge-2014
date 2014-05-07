#!/usr/bin/php
<?php
	/* Challenge 7 - Yes we scan
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 * 
	 */

	$lines = file('php://stdin');
	$terroristA = trim(array_shift($lines));
	$terroristB = trim(array_shift($lines));

$path = '/media/BB/contacts/';
if(!file_exists($path)){mkdir($path);}
if(file_exists($path.$terroristA)){shell_exec('rm -rf '.$path.$terroristA);}mkdir($path.$terroristA);
if(file_exists($path.$terroristB)){shell_exec('rm -rf '.$path.$terroristB);}mkdir($path.$terroristB);

// This function creates the relationship trees starting in one contact
function getContacts($phoneCalls,$number,$path,$line){
	$files = 0;
	if(isset($phoneCalls[$number])){
		foreach($phoneCalls[$number] as $k=>$v){
			$k .= '';
			if(file_exists($path.'/'.$k[0].'/'.$k)){continue;}
			if(!file_exists($path.'/'.$k[0])){mkdir($path.'/'.$k[0],0777,1);}
			touch($path.'/'.$k[0].'/'.$k);
			$files++;
			$files += getContacts($phoneCalls,$k,$path,$line);
		}
	}
	return $files;
}


$lineNumber = $sourcePrevCount = $targetPrevCount = $sourceFilesCount = $targetFilesCount = 0;
$phoneCalls = $sourceContacts = $targetContacts = array();
$source = $target = $lineResult = false;

// Time to process the file line by line, we create two trees starting in each terrorist, 
// when the two trees are connected the Terrorist A is connected to Terrorist B
$fp = fopen('phone_call1.log','r');
while($line = fgets($fp)){
	list($person1,$person2) = explode(' ',trim($line));

	$phoneCalls[$person1][$person2] = $lineNumber;
	$phoneCalls[$person2][$person1] = $lineNumber;

	if($terroristA == $person1 || file_exists($path.$terroristA.'/'.$person1[0].'/'.$person1)){
		$sourceFilesCount += getContacts($phoneCalls,$person1,$path.$terroristA,$lineNumber);
		$source = true;
	}
	if($terroristA == $person2 || file_exists($path.$terroristA.'/'.$person2[0].'/'.$person2)){
		$sourceFilesCount += getContacts($phoneCalls,$person2,$path.$terroristA,$lineNumber);
		$source = true;
	}
	if($terroristB == $person1 || file_exists($path.$terroristB.'/'.$person1[0].'/'.$person1)){
		$targetFilesCount += getContacts($phoneCalls,$person1,$path.$terroristB,$lineNumber);
		$target = true;
	}
	if($terroristB == $person2 || file_exists($path.$terroristB.'/'.$person2[0].'/'.$person2)){
		$targetFilesCount += getContacts($phoneCalls,$person2,$path.$terroristB,$lineNumber);
		$target = true;
	}

	if($source && $target && ($sourcePrevCount < $sourceFilesCount || $targetPrevCount < $targetFilesCount)){
		$sourcePrevCount = $sourceFilesCount;
		$targetPrevCount = $targetFilesCount;

		if($sourceFilesCount <= $targetFilesCount){
			$sourceContacts = glob($path.$terroristA.'/*/*');
			foreach($sourceContacts as $f){
				$f = (string)substr($f,strrpos($f,'/')+1);
				if(file_exists($path.$terroristB.'/'.$f[0].'/'.$f)){
					$lineResult = $lineNumber;
					break 2;
				}
			}

		}
		if($sourceFilesCount > $targetFilesCount){
			$targetContacts = glob($path.$terroristB.'/*/*');
			foreach($targetContacts as $f){
				$f = (string)substr($f,strrpos($f,'/')+1);
				if(file_exists($path.$terroristA.'/'.$f[0].'/'.$f)){
					$lineResult = $lineNumber;
					break 2;
				}
			}
		}
	}
	$lineNumber++;
	echo $lineNumber.PHP_EOL;
}
fclose($fp);

	function personExists($person,$target){
		
	}

if($lineResult){
	echo 'Connected at ',$lineResult,PHP_EOL;
}else{
	echo 'Not connected',PHP_EOL;
}
?>
