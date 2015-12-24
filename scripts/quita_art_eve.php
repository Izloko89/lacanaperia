<?php //script para eliminar articulos desde la tabla de articulos
session_start();
include("datos.php");
header("Content-type: application/json");
$id_item=$_POST["id_item"];
$emp=$_SESSION["id_empresa"];
$eve=$_POST["id_evento"]; //evento
$precio=$_POST["precio"];
try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);

	$id_emp_eve=$emp."_".$eve;
	$sql="SELECT total FROM eventos_total where id_evento = '$id_emp_eve'; ";
	$rs=$bd->query($sql);
	$rs=$rs->fetch(PDO::FETCH_ASSOC);
	$total_eve=$rs["total"];
	$total_eve = $total_eve-$precio; 
 	$sql="UPDATE eventos_total SET total = $total_eve   WHERE id_evento = '$id_emp_eve';";
 	$bd->query($sql);


	$sql="DELETE FROM eventos_articulos WHERE id_item=$id_item;";
	
	$bd->query($sql);
	
	//quitar el articulo de las entradas y salidas usando el id_item
	// $sql="DELETE FROM almacen_entradas WHERE id_item=$id_item;";
	// $bd->query($sql);
	// $sql="DELETE FROM almacen_salidas WHERE id_item=$id_item;";
	// $bd->query($sql);
	
	$r["continuar"]=true;
	$r["info"]="Articulo eliminado satisfactoriamente";
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error encontrado: ".$err->getMessage();
}

echo json_encode($r);
?>