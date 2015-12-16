<?php session_start();
header("content-type: application/json");
include("datos.php");
$usuario=$_POST["usuario"];
$nombre=$_POST["nombre"];
$password=$_POST["password"];

$clave=$_POST["clave"];
$direccion=$_POST["direccion"];
$colonia=$_POST["colonia"];
$ciudad=$_POST["ciudad"];
$estado=$_POST["estado"];
$cp=$_POST["cp"];
$telefono=$_POST["telefono"];
$celular=$_POST["celular"];
$email=$_POST["email"];

$cotizacion=$_POST["cotizacion"];
$evento=$_POST["evento"];
$almacen=$_POST["almacen"];
$compras=$_POST["compras"];
$bancos=$_POST["bancos"];
$modulos=$_POST["modulos"];

		
				



try{
	$sql="";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT usuario from usuarios where usuario='$usuario'";
	$query=$bd->query($sql);
	$res = $query->fetch(PDO::FETCH_ASSOC);
	$userDB=$res["usuario"];
	if(!empty($userDB)){
		$r["info"]="Usuario Repetido";
		echo json_encode($r);
		exit;
	}
	$bd->query("INSERT INTO usuarios (nombre,usuario,password,clave,id_empresa,categoria,activo) VALUES ('$nombre','$usuario','$password','$clave',1,'Administrador',1);");
	
	//$aidi = $bd->query(select mysql_insert_id());
	


$sql="SELECT MAX( id_usuario ) AS aidi FROM usuarios";
			$res = $bd->query($sql);
			$res=$res->fetchAll(PDO::FETCH_ASSOC);
			$aidi=$res[0]["aidi"];

			
//$row=mysql_fetch_array($res);
//$aidi=$row["aidi"]; // En esta línea es el error.
			
			

			$sql="INSERT INTO usuarios_contacto
			(
			id_usuario,
			clave,
			direccion,
			colonia,
			ciudad,
			estado,
			cp,
			telefono,
			celular,
			email
			) 
			VALUES
			(
			$aidi,
			'$clave',
			'$direccion',
			'$colonia',
			'$ciudad',
			'$estado',
			'$cp',
			'$telefono',
			'$celular',
			'$email'
			);";
			$bd->query($sql);
			
	//$bd->query("INSERT INTO usuarios_contacto (id_usuario,clave,direccion,colonia,ciudad,estado,cp,telefono,celular,email) VALUES
	//($aidi,'$clave','$direccion','$colonia','$ciudad','$estado','$cp','$telefono','$celular','$email');");
	
	$sql = "INSERT INTO usuario_permisos
	(
	id_usuario,
	cotizacion,
	evento,
	almacen,
	compras,
	bancos,
	modulos
	)
	VALUES
	(
	$aidi,
	$cotizacion,
	$evento,
	$almacen,
	$compras,
	$bancos,
	$modulos
	);";
	
	$bd->query($sql);
	
	$r["continuar"]=true;
	$r["info"]="Usuario añadido exitosamente";
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["demo"]= $sql;
	$r["info"]="Error: ".$err->getMessage();
}

echo json_encode($r);
?>