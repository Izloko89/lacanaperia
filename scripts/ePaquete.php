<?php 
include("datos.php");

$bd=new PDO($dsnw,$userw,$passw,$optPDO);
$id_art=$_POST["id_item"];


try{
	$sql= "delete from paquetes where id_paquete = $id_art";
			$bd->query($sql);
			
			$sql= "delete from paquetes_articulos where id_paquete = $id_art";
			$bd->query($sql);
			$r["continuar"] = true;
		}
		catch(PDOException $err)
		{
			$r["continuar"]=false;
			$r["info"]="Error: ".$err->getMessage();
		}
		
	echo json_encode($r);
?>