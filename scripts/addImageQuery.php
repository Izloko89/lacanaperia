<?php session_start();
header("content-type: application/json");
include("datos.php");
$clave = (!($_GET['clave'] == null)) ? $_GET['clave']: 'NULL';
$nombre = (!($_GET['nombre'] == null)) ? $_GET['nombre'] : 'NULL';
$desc = (!($_GET['desc'] == null)) ? $_GET['desc'] : 'NULL';
$unidad = (!($_GET['unidad'] == null)) ? $_GET['unidad'] : 0;	  		
$image = (!($_GET['image'] == null)) ? $_GET['image'] : NULL ;
$emp = (!($_GET['emp'] == null)) ? $_GET['emp'] : 0 ;
$area = (!($_GET['area'] ==null)) ? $_GET['area'] : 0;
$familia = (!($_GET['familia'] == null)) ? $_GET['familia'] : 'NULL' ;
$subfamilia = (!($_GET['subfamilia'] == null)) ? $_GET['subfamilia'] : 'NULL' ;
$precio1 = (!($_GET['precio1'] == null)) ? $_GET['precio1'] : 0 ;
$precio2 = (!($_GET['precio2'] == null)) ? $_GET['precio2'] : 0 ;
$precio3 = (!($_GET['precio3'] == null)) ? $_GET['precio3'] :  0 ;
$precio4 = (!($_GET['precio4'] ==null )) ? $_GET['precio4'] :  0 ;
$compra = (!($_GET['compra'] == null)) ? $_GET['compra'] : 0 ;

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$bd->query("INSERT INTO  articulos(id_empresa,area,familia,subfamilia,clave,nombre,descripcion,unidades,activo,image)
	 	VALUES
	 	($emp,'$area','$familia','$subfamilia',$clave,'$nombre','$desc','$unidad',1,'$image'); ");	
	
	$sql = "SELECT MAX(id_articulo) as id from articulos";
	$query = $bd->query($sql);
	$res = $query->fetch(PDO::FETCH_ASSOC);
	$art = ($res["id"] == null) ? 1 : $res["id"];
	$bd->query("INSERT INTO listado_precios(id_empresa,id_articulo,compra,precio1,precio2,precio3,precio4) VALUES ($emp,$art,$compra,$precio1,$precio2,$precio3,$precio4);");

	$data["continuar"]=true;
}catch(PDOException $err){
	$data["continuar"]=false;
	echo $option=$err->getMessage();
}

echo json_encode($data);
?>