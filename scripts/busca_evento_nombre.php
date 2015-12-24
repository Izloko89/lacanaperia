<?php session_start();
include("datos.php");
header("Content-type: application/json");
$term=$_GET["term"];
try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	//sacar los campos para acerlo más autoámtico
	$campos=array();
	
	$res=$bd->query("DESCRIBE eventos;");
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $a=>$c){
		$campos[$a]=$c["Field"];
	}
		$res=$bd->query("SELECT * FROM eventos WHERE  nombre LIKE '%$term%';");
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $i=>$v){
		$CLAVE=$v["clave"];
		$r[$i]["label"]=$v["nombre"];
		foreach($campos as $campo){
			$r[$i][$campo]=$v[$campo];
		}
	}
	
	
}catch(PDOException $err){
	echo json_encode($err);
}
echo json_encode($r);
?>