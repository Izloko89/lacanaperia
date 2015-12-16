<?php
	
	include_once("datos.php");
	try{
		$bd = new PDO($dsnw, $userw, $passw, $optPDO);
		foreach($_FILES as $image){
			$name = '../img/imagenCliente/'. $image["name"];
			move_uploaded_file($image["tmp_name"], $name);
			$sql = "insert into imagenCliente(id_cliente, path) values(" . $_POST["cliente"] . ", '$name')";
			$bd->query($sql);
		}
		echo true;
	}
	catch(PDOException $err) 
	{
		echo $err->getMessage();
	}
?>