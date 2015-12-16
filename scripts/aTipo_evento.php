<?php session_start();
header("content-type: application/json");
include("datos.php");
$nombre=$_POST["nombre"];



try{
	$sql="";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	
	$bd->query("INSERT INTO tipo_evento (id_empresa,nombre) VALUES (1,'$nombre');");
	
	$r["continuar"]=true;
	$r["info"]="Tipo de evento añadido exitosamente";
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

echo json_encode($r);
?>