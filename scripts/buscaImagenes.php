<?php
	$id = $_POST["id"];
	include_once("datos.php");
	try{
		$bd = new PDO($dsnw, $userw, $passw, $optPDO);
		$sql = "select * from imagenCliente where id_cliente = $id";
		$res = $bd->query($sql);
		$res = $res->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($res);
	}
	catch(PDOException $err) 
	{
		echo $err->getMessage();
	}
?>