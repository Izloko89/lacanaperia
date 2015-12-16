<?php session_start();
header("Content-type: application/json");
$empresaid=$_SESSION["id_empresa"];
include("datos.php");


$l= $_POST['l'];
$m= $_POST['m'];
$mi= $_POST['mi'];
$j= $_POST['j'];
$v= $_POST['v'];
$s= $_POST['s'];
$d= $_POST['d'];
				
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
$rfcf = $_POST['rfcf'];
$direccionf = $_POST['direccionf'];
$coloniaf = $_POST['coloniaf'];
$ciudadf = $_POST['ciudadf'];
$estadof = $_POST['estadof'];
$cpf = $_POST['cpf'];

	$bd=new PDO($dsnw, $userw, $passw, $optPDO);


try{	
		$sql= "insert into empleados (nombre,puesto,pcompra,pventa,l,m,mi,j,v,s,d) values ('$nombre','$puesto',$pcompra,$pventa,$l,$m,$mi,$j,$v,$s,$d)";
			$bd->query($sql);
	
	
		
	
	$sql="SELECT MAX(id_empleado) as id FROM empleados";
	$res=$bd->query($sql);
	$res=$res->fetchAll(PDO::FETCH_ASSOC);
	$adidi=$res[0]["id"];
	if(!isset($adidi)){
		$adidi=1;
	}
	$sql = "insert into empleados_contacto (id_empleado,puesto,direccion,colonia,ciudad,estado,cp,telefono,celular,email)values
	($adidi,'$puesto','$direccion','$colonia','$ciudad','$estado','$cp','$telefono','$celular','$email')";
	$bd->query($sql);
	
	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Tiene que llenar los campos";
	//echo $err->getMessage();
}
echo json_encode($r);
?>