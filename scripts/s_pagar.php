<?php session_start();
header("content-type: application/json");
include("datos.php");
$eve=$_SESSION["id_empresa"]."_".$_POST["eve"];
$monto=$_POST["monto"];
$fecha=$_POST["fecha"];
$cliente=$_POST["cliente"];
if(isset($_POST['banco'])){

    if(empty($_POST['banco'])){
      $banco = '0';
    }      
    else $banco = $_POST['banco'];
}

try{
	$sql="INSERT INTO eventos_pagos (id_evento,id_cliente,plazo,fecha,cantidad,id_banco)
	VALUES ('$eve',$cliente,'%consec%','$fecha','$monto', $banco);";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	
	$bd->query($sql);
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

echo json_encode($r);
?>