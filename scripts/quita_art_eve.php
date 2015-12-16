<?php //script para eliminar articulos desde la tabla de articulos
include("datos.php");
header("Content-type: application/json");
$id_item=$_POST["id_item"];
try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$sql="DELETE FROM eventos_articulos WHERE id_item=$id_item;";
	
	$bd->query($sql);
	
	//quitar el articulo de las entradas y salidas usando el id_item
	$sql="DELETE FROM almacen_entradas WHERE id_item=$id_item;";
	$bd->query($sql);
	$sql="DELETE FROM almacen_salidas WHERE id_item=$id_item;";
	$bd->query($sql);
	
	$r["continuar"]=true;
	$r["info"]="Articulo eliminado satisfactoriamente";
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error encontrado: ".$err->getMessage();
}

echo json_encode($r);
?>