<?php session_start();
header("content-type: application/json");
include("datos.php");

$id_usuario=$_POST["id_usuario"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT * FROM usuarios_contacto WHERE id_usuario=$id_usuario;";
	
	$res=$bd->query($sql);
	if($res->rowCount()>0){
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		foreach($res[0] as $id=>$d){
			$row[$id]=$d;
		}
		$r["continuar"]=true;
		$r["datos"]=$row;
	}else{
		$r["continuar"]=false;
		// $r["info"]="No hay permisos asignados a este usuario todavía";
	}
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}

$bd=NULL;
echo json_encode($r);
?>