<?php session_start();
header("Content-type: application/json");
$empresaid=$_SESSION["id_empresa"];
include("datos.php");



				
$nombre = $_POST['nombre'];
$puesto = $_POST['puesto'];
$pcompra= $_POST['pcompra'];
$pventa= $_POST['pventa'];
$clave = $_POST['clave']; 
$direccion = $_POST['direccion'];
$colonia = $_POST['colonia'];
$ciudad = $_POST['ciudad'];
$estado = $_POST['estado'];
$cp = $_POST['cp'];
$telefono = $_POST['telefono'];
$celular = $_POST['celular'];
$email = $_POST['email'];

$l= $_POST['l'];
$m= $_POST['m'];
$mi= $_POST['mi'];
$j= $_POST['j'];
$v= $_POST['v'];
$s= $_POST['s'];
$d= $_POST['d'];


	$bd=new PDO($dsnw, $userw, $passw, $optPDO);


try{	
	$bd->query("update empleados set
	nombre = '$nombre',
	puesto = '$puesto',
	pcompra= $pcompra,
	pventa= $pventa,
	l= $l,
	m= $m,
	mi= $mi,
	j= $j,
	v= $v,
	s= $s,
	d= $d
	
	
	 where id_empleado = $clave;");

	$bd->query("update empleados_contacto set
	puesto = '$puesto',
	direccion = '$direccion',
	colonia = '$colonia',
	ciudad = '$ciudad',
	estado = '$estado',
	cp = '$cp',
	telefono = '$telefono',
	celular = '$celular',
	email = '$email'
	where id_empleado = $clave;");
	
	
	
	
	$r["continuar"]=true;
}catch(PDOException $err)
		{
			$r["continuar"]=false;
			$r["info"]="Error: ".$err->getMessage();
		}
		
	echo json_encode($r);
?>