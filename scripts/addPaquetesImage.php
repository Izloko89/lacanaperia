<?php session_start();
header("content-type: application/json");
include("datos.php");
$clave = (!($_GET['clave'] == null)) ? $_GET['clave']: 'NULL';
$nombre = (!($_GET['nombre'] == null)) ? $_GET['nombre'] : 'NULL';
$desc = (!($_GET['desc'] == null)) ? $_GET['desc'] : 'NULL';
$unidad = (!($_GET['unidad'] == null)) ? $_GET['unidad'] : 0;	  		
$image = (!($_GET['image'] == null)) ? $_GET['image'] : NULL ;
$emp = (!($_GET['emp'] == null)) ? $_GET['emp'] : 0 ;
$precio1 = (!($_GET['precio1'] == null)) ? $_GET['precio1'] : 0 ;
$precio2 = (!($_GET['precio2'] == null)) ? $_GET['precio2'] : 0 ;
$precio3 = (!($_GET['precio3'] == null)) ? $_GET['precio3'] :  0 ;
$precio4 = (!($_GET['precio4'] ==null )) ? $_GET['precio4'] :  0 ;
$compra = (!($_GET['compra'] == null)) ? $_GET['compra'] : 0 ;

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$bd->query("INSERT INTO  paquetes(id_empresa,clave,nombre,descripcion,unidades,image)
	 	VALUES
	 	($emp,$clave,'$nombre','$desc','$unidad','$image'); ");	
	
	$sql = "SELECT MAX(id_paquete) as id from paquetes";
	$query = $bd->query($sql);
	$res = $query->fetch(PDO::FETCH_ASSOC);
	$art = ($res["id"] == null) ? 1 : $res["id"];
	$bd->query("INSERT INTO listado_precios(id_empresa,id_paquete,compra,precio1,precio2,precio3,precio4) VALUES ($emp,$art,$compra,$precio1,$precio2,$precio3,$precio4);");

	$data["continuar"]=true;
}catch(PDOException $err){
	$data["continuar"]=false;
	echo $option=$err->getMessage();
}

echo json_encode($data);
?>