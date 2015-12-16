<?php
	$cant = $_POST["cant"];
	$art = $_POST["art"];
	
	include_once("datos.php");
	try{
		$bd = new PDO($dsnw, $userw, $passw, $optPDO);
		$sql = "select * from almacen where id_articulo = $art";
		$res = $bd->query($sql);
		$res = $res->fetchAll(PDO::FETCH_ASSOC);
		$cant1 = $res[0]["cantidad"] - $cant;
		$cant1 = ($cant1 < 0? 0 : $cant1);
		$r["cant1"] = $res[0]["cantidad"];
		$sql1 = "update almacen set cantidad = '$cant1' where id_articulo = $art";
		$bd->query($sql1);
		$sql = "select * from almacen_inventario where id_articulo = $art";
		$res = $bd->query($sql);
		$res = $res->fetchAll(PDO::FETCH_ASSOC);
		$cant2 = $res[0]["cantidad"] - $cant;
		$cant2 = ($cant2 < 0? 0 : $cant2);
		$sql2 = "update almacen_inventario set cantidad = '$cant2' where id_articulo = $art";
		$bd->query($sql2);
		$r["cant"] = $cant;
		$r["cant2"] = $res[0]["cantidad"];
		$r["sql1"] = $sql1;
		$r["sql2"] = $sql2;
		$r["continuar"] = true;
		echo json_encode($r);
	}
	catch(PDOException $err) 
	{
		$r["continuar"] = false;
		$r["info"] = $err->getMessage();
		echo json_encode($r);
	}
?>