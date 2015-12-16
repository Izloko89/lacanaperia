<?php 
include("datos.php");
	unset($r);
	$idItem = $_POST["id_item"];	
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	try
	{
		$sqlAfs="DELETE FROM areas WHERE id_area = $idItem";
		$res=$bd->query($sqlAfs);
		echo true;
	}
		catch(PDOException $err){
		echo false;
	}
?>