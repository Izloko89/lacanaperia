<?php
if($_SERVER['SERVER_ADDR']=="65.99.205.96"){
	//if($_SERVER['SERVER_ADDR']=="65.99.225.171"){
	//para DSN PDO en eventos.desarrolloclientes
$dsnw="mysql:host=localhost; dbname=gumpy; charset=utf8;";
	$userw="bari";
	$passw="bari231";
	$optPDO=array(PDO::ATTR_EMULATE_PREPARES=>false, PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION);
}elseif($_SERVER['SERVER_ADDR']=="65.99.205.96"){
	//}elseif($_SERVER['SERVER_ADDR']=="65.99.205.189"){
	//para DSN PDO en eventos.enthalpy
	$dsnw="mysql:host=localhost; dbname=gumpy; charset=utf8;";
	$userw="bari";
	$passw="bari231";
	$optPDO=array(PDO::ATTR_EMULATE_PREPARES=>false, PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION);
}else{
	//para DSN PDO en localhost
	$dsnw="mysql:host=localhost; dbname=gumpy; charset=utf8;";
	$userw="bari";
	$passw="bari231";
	$optPDO=array(PDO::ATTR_EMULATE_PREPARES=>false, PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION);
}
//datos de servidor
@define("HOST",$_SERVER['HTTP_HOST']);
@define("LIGA","HTTP://".$_SERVER['HTTP_HOST']."/");
?>