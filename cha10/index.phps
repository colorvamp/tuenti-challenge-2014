<?php
srand(mktime(date("H"), date("i"), 0) * posix_getppid());
$u = rand();
echo 
header("Date: ".gmdate("D, d M Y H:i:s")." GMT");
echo $u==$_GET['password'] ? json_decode(file_get_contents('../keys.json'), true)[$_GET['input']] : "wrong!";
