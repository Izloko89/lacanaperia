<?php session_start();
header("content-type: application/json");
include("datos.php");
$id_eve=$_GET["id_evento"];

try{

	$bd=new PDO($dsnw,$userw,$passw,$optPDO);

	$sql = "select estatus from eventos  where id_evento= '$id_eve'";
	$res2 = $bd->query($sql);
	$res2 = $res2->fetchAll(PDO::FETCH_ASSOC);
	$estatus = $res2[0]["estatus"];	
	if($estatus==2){
		$r["estatus"]=true;
		$bd=NULL;
		echo json_encode($r);
		exit;
	}
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="UPDATE eventos SET estatus=2 WHERE id_evento=$id_eve;";
	$bd->query($sql);

	$sql = "select * from eventos_articulos where id_evento = $id_eve";
	$res = $bd->query($sql);
	$res = $res->fetchAll(PDO::FETCH_ASSOC);
	if(!isset($res[0]["id_articulo"]) || trim($res[0]["id_articulo"])==='' ){
		$sql = "select ar.id_articulo as id_articulo,(ea.cantidad * pa.cantidad) as cantidad,lp.compra as precio  from eventos_articulos as ea 
				inner join paquetes_articulos pa on ea.id_paquete = pa.id_paquete 
				inner join articulos ar on pa.id_articulo = ar.id_articulo
				inner join listado_precios lp on ar.id_articulo = lp.id_articulo
				where ea.id_evento = $id_eve ";
		$res = $bd->query($sql);	
		$res = $res->fetchAll(PDO::FETCH_ASSOC);
	}
	foreach($res As $v)
	{
		$art = $v["id_articulo"];
		$cant = $v["cantidad"];
		$precio = $v["precio"];
		$sql = "select fechamontaje, fechadesmont from eventos where id_evento = $id_eve";
		$res = $bd->query($sql);
		$res = $res->fetchAll(PDO::FETCH_ASSOC);
		$fechamontaje = $res[0]["fechamontaje"];
		$fechadesmont = $res[0]["fechadesmont"];
		$sql = "select 'no disponible' as ndisponible, eventos.id_evento, cantidad from eventos
				INNER JOIN eventos_articulos ON eventos_articulos.id_evento =  eventos.id_evento
				where fechamontaje BETWEEN '$fechamontaje' AND '$fechadesmont' AND estatus = 2 AND eventos_articulos.id_articulo = $art AND eventos.id_evento != $id_eve";
		$res = $bd->query($sql);
		$res = $res->fetchAll(PDO::FETCH_ASSOC);
		$sql = "select cantidad from almacen where id_articulo = $art";
		$res1 = $bd->query($sql);
		$res1 = $res1->fetchAll(PDO::FETCH_ASSOC);
		$ocupadas = 0;
		if(isset($res[0]["ndisponible"]))
		{
			foreach($res as $d => $v)
			{
				$ocupados[$d] = $v;
				$ocupadas += $v["cantidad"];
			}
		}
		$costo = (int)$precio / $cant;
		$final = $res1[0]["cantidad"] - $ocupadas;
		if($final < 0){
			$final = 0;
		}
		$final -= $cant;
		$asd = $costo;
		$asd = explode(",", $asd);
		$costo = $asd[0];
		if($final <= 0)
		{
			$final *= -1;
			$precio1 = $final * $costo;
			if(!$final==0){
			$sql = "insert into compras(id_empresa, id_evento)
					values(1, $id_eve)";
			$bd->query($sql);
			$sql = "select MAX(id_compra) as id from compras ";
			$res1 = $bd->query($sql);
			$res1 = $res1->fetchAll(PDO::FETCH_ASSOC);
			$id = $res1[0]["id"];
			$sql = "insert into compras_articulos(id_compra, id_empresa, id_articulo, cantidad, precio)
					values($id, 1, $art, $final, $precio)";
			$bd->query($sql);
			}
		}
	//	$sql = "insert into almacen_salidas(id_empresa, id_evento, id_articulo, cantidad, fechamontaje)
	//			values(1, $id_eve, $art, $cant, CURDATE());";
	//	$bd->query($sql);
		$sql = "select cantidad from almacen_inventario  where id_articulo = $art ";
		$res2 = $bd->query($sql);
		$res2 = $res2->fetchAll(PDO::FETCH_ASSOC);
		$cantidadenbd = $res2[0]["cantidad"];
		$resta = $cantidadenbd - $cant;
		if($resta >= 0){
			$sql = "update almacen_inventario set cantidad='$resta' where id_articulo= '$art' ";
		}else{
			$sql = "update almacen_inventario set cantidad='0' where id_articulo= '$art' ";

		}
		
		$bd->query($sql);
	}


	$r["continuar"]=true;
}catch(PDOException $err){
	$r["continuar"]=false;
	$r["info"]="Error: ".$err->getMessage();
}
$bd=NULL;
echo json_encode($r);
?>