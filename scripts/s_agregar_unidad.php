<?php session_start();
header("content-type: application/json");
include("datos.php");
$nombre=$_POST["nombre"];
$descripcion=$_POST["descripcion"];


try{
	$sql="";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	
	$bd->query("INSERT INTO unidades (nombre,descripcion) VALUES ('$nombre','$descripcion');");
	
	$r["continuar"]=true;
	$r["info"]="Unidad añadida exitosamente";
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

echo json_encode($r);
?>