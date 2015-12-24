<?php session_start();
header("Content-type: application/json");
$empresaid=$_SESSION["id_empresa"];
include("datos.php");



				
$nombre = $_POST['term'];
$textarea = $_POST["term2"];
$descripcion="";
$descripcion.= '<p>' . preg_replace("~[\r\n]+~", '</p><p>', $textarea) . '</p>';

try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$bd->query("update conceptos set nombre='$nombre', descripcion='$descripcion' where nombre = '$nombre';");
	
	
	
	
	$res["continuar"] = true;
}catch(PDOException $err)
		{
			$res["continuar"]=false;
			$res["info"]="Error: ".$err->getMessage();
		}
		
	echo json_encode($res);
?>