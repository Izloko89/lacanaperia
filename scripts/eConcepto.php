<?php 
include("datos.php");
	unset($r);
	$idConcepto = $_POST["id_item"];	
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	try
	{
		$sqlAfs="DELETE FROM conceptos WHERE id_concepto = $idConcepto";
		$res=$bd->query($sqlAfs);
		echo true;
	}
		catch(PDOException $err){
		echo false;
	}
?>