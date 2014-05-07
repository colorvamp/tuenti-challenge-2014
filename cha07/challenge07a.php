#!/usr/bin/php
<?php
	/* Challenge 7 - Yes we scan
	 * by Marcos Fernández (sombra2eternity@gmail.com)
	 * 
	 */
	/* INI-data input */
	//$i = file('php://stdin');
	//file_put_contents('data',implode(PHP_EOL,$i));
	/* END-data input */

	$GLOBALS['tables']['contacts'] = array('_person1_'=>'INTEGER','_person2_'=>'INTEGER','line'=>'INTEGER');
	$GLOBALS['tables']['contactsRels'] = array('_person_'=>'INTEGER');
	include_once('inc.sqlite3.php');
	$GLOBALS['contacts'] = array();
	$GLOBALS['test'] = 1;
	$GLOBALS['dbFile'] = '/media/BB/contacts111'.$GLOBALS['test'].'.db';

	if($GLOBALS['test'] == 0){$source = 0;$target = 4;}
	if($GLOBALS['test'] == 1){$source = 1193;$target = 2388;}
	if($GLOBALS['test'] == 2){$source = 0;$target = 999;}

	if(file_exists($GLOBALS['dbFile'])){unlink($GLOBALS['dbFile']);}
	if(file_exists($GLOBALS['dbFile'].'-shm')){unlink($GLOBALS['dbFile'].'-shm');}
	if(file_exists($GLOBALS['dbFile'].'-wal')){unlink($GLOBALS['dbFile'].'-wal');}
	$GLOBALS['lgFile'] = 'phone_call'.$GLOBALS['test'].'.log';


	$fp = fopen($GLOBALS['lgFile'],'r');
	$sourceExists = false;
	$targetExists = false;
	$i = 0;
	$db = sqlite3_open($GLOBALS['dbFile']);
	$r = sqlite3_exec('BEGIN;',$db);
	while($line = fgets($fp)){
		$a = explode(' ',trim($line));
		/* INI-Insert time */
		$r = sqlite3_insertIntoTable('contacts',array('_person1_'=>$a[0],'_person2_'=>$a[1],'line'=>$i),$db);
		$r = sqlite3_insertIntoTable('contacts',array('_person1_'=>$a[1],'_person2_'=>$a[0],'line'=>$i),$db);
		/* END-Insert time */
		echo ($i++).PHP_EOL;

		if(!$sourceExists && ($a[1] == $source || $a[0] == $source)){$sourceExists = true;}
		if(!$targetExists && ($a[1] == $target || $a[0] == $target)){$targetExists = true;}
		if(!$sourceExists && !$targetExists){continue;}
		if($a[0] == $source){$r = sqlite3_insertIntoTable('contactsRels'.$source,array('_person_'=>$a[1]),$db,'contactsRels');}
		if($a[1] == $source){$r = sqlite3_insertIntoTable('contactsRels'.$source,array('_person_'=>$a[0]),$db,'contactsRels');}
		if($a[0] == $target){$r = sqlite3_insertIntoTable('contactsRels'.$target,array('_person_'=>$a[1]),$db,'contactsRels');}
		if($a[1] == $target){$r = sqlite3_insertIntoTable('contactsRels'.$target,array('_person_'=>$a[0]),$db,'contactsRels');}

		$sourceA0 = dbIsContactSimple($source,$a[0],$db);
		$sourceA1 = dbIsContactSimple($source,$a[1],$db);
		/* If it is a contact of source debemos añadirlos a su bolsa */
		if($sourceA0 && !$sourceA1){
			$contacts = dbGetContactsRecursive($a[1],$db,$target,$i);
			foreach($contacts as $contact){$r = sqlite3_insertIntoTable('contactsRels'.$source,array('_person_'=>$contact),$db,'contactsRels');}
		}
		if(!$sourceA0 && $sourceA1){
			$contacts = dbGetContactsRecursive($a[0],$db,$target,$i);
			foreach($contacts as $contact){$r = sqlite3_insertIntoTable('contactsRels'.$source,array('_person_'=>$contact),$db,'contactsRels');}
		}

		$targetA0 = dbIsContactSimple($target,$a[0],$db);
		$targetA1 = dbIsContactSimple($target,$a[1],$db);
		if($targetA0 && !$targetA1){
			$contacts = dbGetContactsRecursive($a[1],$db,$source,$i);
			foreach($contacts as $contact){$r = sqlite3_insertIntoTable('contactsRels'.$target,array('_person_'=>$contact),$db,'contactsRels');}
		}
		if(!$targetA0 && $targetA1){
			$contacts = dbGetContactsRecursive($a[0],$db,$source,$i);
			foreach($contacts as $contact){$r = sqlite3_insertIntoTable('contactsRels'.$target,array('_person_'=>$contact),$db,'contactsRels');}
		}
		if(!$sourceA0 || !$sourceA1 || !$targetA0 || $targetA1){
			$o = dbIsContactSimple($source,$target,$db);
			$p = dbIsContactSimple($target,$source,$db);
			if($o || $p){echo 'Connected at '.$i.PHP_EOL;exit;}
		}
	}
	sqlite3_close($db,true);
	fclose($fp);

	function dbIsContactSimple($source,$target,$db){return sqlite3_getSingle('contactsRels'.$source,'(person = ('.$target.'))',array('indexBy'=>'person','db'=>$db,'db.file'=>$GLOBALS['dbFile']));}
	function dbIsContactComplex($source,$target,$db){return sqlite3_getSingle('contactsRels'.$source,'(person = ('.$target.'))',array('indexBy'=>'person','db'=>$db,'db.file'=>$GLOBALS['dbFile']));}
	function dbGetContactsRecursive($source,$db,$target,$i){
		return dbGetContactsRecursive1($source,$db,$target,$i);
		$source = array($source);
		$contacts = dbGetContactsComplex($source,$db);
		$source = array_unique(array_merge($source,array_keys($contacts)));
		$u = 1;$c = 1;$oldc = count($source);
		while($c != $oldc){
			$oldc = $c;
			$contacts = dbGetContactsComplex($source,$db);
			$source = array_unique(array_merge($source,array_keys($contacts)));
			if(in_array($target,$source)){return array_keys($contacts);}
			$c = count($source);
if($u > 50){
if($u == 79){file_put_contents('aa',print_r($contacts,1));}
echo $u.PHP_EOL;
}
			$u++;
		}
if($i > 150019){
print_r($contacts);
exit;
}
		return array_keys($contacts);
	}
	function dbGetContactsRecursive1($source,$db,$target,$i){
		$new = $source = array($source);
		$c = 1;$oldc = count($source);$u = 1;
		do{
			$oldc = $c;
			$contacts = dbGetContactsComplex($new,$db);
			$keys = array_keys($contacts);
			$new = array_diff($keys,$source);
			if(!$new){break;}
			$source = array_unique(array_merge($source,$keys));
			if(in_array($target,$source)){return $source;}
			$c = count($source);
			if($u > 50){
if($u == 80){
print_r($source);
exit;
}
				/* Big merge */
				echo $u.PHP_EOL;
			}
			$u++;
		}while($c != $oldc);
		return $source;
	}
	function dbGetContactsComplex($id = array(),$db){return sqlite3_getWhere('contacts','(person1 IN ('.implode(',',$id).'))',array('selectString'=>'person2 as person','indexBy'=>'person','db'=>$db,'db.file'=>$GLOBALS['dbFile']));}
	function dbGetContactsSimple($id,$db){return sqlite3_getWhere('contacts','(person1 = '.$id.')',array('selectString'=>'person2 as person','indexBy'=>'person','db'=>$db,'db.file'=>$GLOBALS['dbFile']));}
?>
