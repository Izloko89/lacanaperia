<?php 
	session_start();
	include("../scripts/datos.php");
	$emp = $_SESSION["id_empresa"];
	$name = $_POST["term"];
	$descripcion = $_POST["term1"];
	if(isset($name))
	{
		$sql = "";
		try{
			$bd=new PDO($dsnw,$userw,$passw,$optPDO);
			$sql ="insert into conceptos(id_empresa, nombre, descripcion) values($emp, '$name', '$descripcion')";
			$bd->query($sql);
			$r["continuar"] = true;
		}
		catch(PDOException $err)
		{
			unset($r);
		}
		echo json_encode($r);
	}
?>