<?php session_start();
header("Content-type: application/json");
include("datos.php");

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	//sacar los campos para acerlo más autoámtico
		
	$res=$bd->query("SELECT id_concepto, nombre FROM conceptos ;");
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $i=>$v){
		$r[$i]["nombre"]=$v["nombre"];
		$r[$i]["id"]=$v["id_concepto"];
	}
	
}catch(PDOException $err){
	echo $err->getMessage();
}

echo json_encode($r);
?>