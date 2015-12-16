<?php session_start();
include("datos.php");
include("func_form.php");
$eve=$_SESSION["id_empresa"]."_".$_POST["eve"];

try{
	$rId = 0;
	$sql="SELECT * FROM eventos_pagos WHERE id_evento='$eve' order By fecha;";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$res=$bd->query($sql);
	
	$tabla="<table class=table><tr><th>No Pago</th><th>Fecha</th><th>Cantidad</th></tr>";
	$id=1;
	$total=0;
	$cant = 0;
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
		$rId = $d["id_pago"];
		$tabla.='<tr>';
		$tabla.="<td>".($cant == 0?"Anticipo":"Pago $cant").'</td>';
		$tabla.="<td>".varFechaExtensa($d["fecha"]).'</td>';
		$tabla.='<td>'.$d["cantidad"] .'</td>';
		$tabla.="<td><form action=scripts/pago_pdf.php target=_blank> <input type=submit  value=Imprimir /><input type=hidden name=idPagoPdf id=idPagoPdf value=$rId><input type=hidden name=idEve id=idEve value=$eve></form></td>";
		$tabla.='</tr>';
		$id++;
		$total+=$d["cantidad"];
		$cant++;
	}
	$tabla.='<tr><td></td><td style="text-align:right;">Total=</td><td>'.$total.'</td></tr>';
	$tabla.="</table>";
	echo $tabla;
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
}
?>