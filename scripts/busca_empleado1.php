<?php session_start();
header("Content-type: application/json");

$term=$_GET["term"];
include("datos.php");

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	//sacar los campos para acerlo más autoámtico
	
	/* para busqueda numerica
	$sqlArt="SELECT 
		*
	FROM articulos
	INNER JOIN listado_precios ON articulos.id_articulo=listado_precios.id_articulo
	WHERE articulos.id_empresa=$empresaid AND articulos.clave=$term;";*/
	
	$sqlArt="SELECT 
		empleados.id_empleado,
		empleados.nombre,
		empleados.puesto,
		empleados.pcompra,
		empleados.pventa,
		empleados.l,
		empleados.m,
		empleados.mi,
		empleados.j,
		empleados.v,
		empleados.s,
		empleados.d,
		empleados_contacto.direccion,
		empleados_contacto.colonia,
		empleados_contacto.ciudad,
		empleados_contacto.estado,
		empleados_contacto.cp,
		empleados_contacto.telefono,
		empleados_contacto.celular,
		empleados_contacto.email
		
		
		
		
	FROM empleados
	LEFT JOIN empleados_contacto ON empleados.id_empleado=empleados_contacto.id_empleado
	
	WHERE  empleados.id_empleado='$term';";
	
	$i=0;
	$res=$bd->query($sqlArt);
	$r=array("nombre"=>"No existe empleado con esta clave");
	if($res->rowCount()>0){
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$r=$res[0];
	}
}catch(PDOException $err){
	$r=$err->getMessage();
}

echo json_encode($r);
?>