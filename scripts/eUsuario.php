<?php 
include("datos.php");
	unset($r);
	$idItem = $_POST["id_item"];	
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	try
	{
		$sqlAfs="DELETE FROM usuarios WHERE id_usuario = $idItem";
		$res=$bd->query($sqlAfs);
		
		$sqlAfs="DELETE FROM usuarios_contacto WHERE id_usuario = $idItem";
		$res=$bd->query($sqlAfs);
		
		$sqlAfs="DELETE FROM usuario_permisos WHERE id_usuario = $idItem";
		$res=$bd->query($sqlAfs);
		echo true;
	}
		catch(PDOException $err){
		echo false;
	}
?>