<?php session_start();
header("content-type: application/json");
include("datos.php");
include("funciones.php");
$emp=$_SESSION["id_empresa"];
$eve=$_POST["id_evento"];
try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	foreach($_POST["items"] as $art){
		$id_articulo=$art["art"];
		$cant=$art["ent"];
		$bd->query("UPDATE almacen_salidas SET salio=1 WHERE id_evento=$eve and id_articulo = $id_articulo;");
		$bd->query("UPDATE almacen_entradas SET cantidad=$cant  WHERE id_evento=$eve and id_articulo=$id_articulo;");
		}
		
	
	
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

echo json_encode($r);
?>