<?php 
	session_start();
	include("../scripts/datos.php");
	$emp = $_SESSION["id_empresa"];
	$name = $_POST["term"];
	//$titulo = $_POST["term1"];
	$textarea = $_POST["term2"];
	if(isset($name))
	{
		$sql = "";
		try{

      				$descripcion.= '<p>' . preg_replace("~[\r\n]+~", '</p><p>', $textarea) . '</p>';
//      				$descripcion .=  "<p>".$str."</p>";
      			
			$bd=new PDO($dsnw,$userw,$passw,$optPDO);
			$sql ="insert into conceptos(id_empresa, nombre, titulo, descripcion) values($emp, '$name', NULL, '$descripcion')";
			$bd->query($sql);
			$r["continuar"] = true;
		}
		catch(PDOException $err)
		{
			unset($r);
		}
		echo json_encode($r);
	}
?>