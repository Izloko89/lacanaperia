<?php session_start();
include("datos.php");
include("func_form.php");
$eve=$_POST["id_cliente"];

try{
	$sql=	"SELECT DISTINCT id_evento
			FROM eventos_pagos
			WHERE id_cliente =$eve";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$eventos=$bd->query($sql);
	
	$string_return = "";
	
	foreach($eventos->fetchAll(PDO::FETCH_ASSOC) as $evento){
		$id_evento=$evento["id_evento"];
		
		$rest = substr($id_evento, -1);
		
		$sql=	"SELECT DISTINCT eventos_pagos.fecha, eventos_pagos.cantidad, eventos_pagos.id_pago
				FROM eventos_pagos
				WHERE eventos_pagos.id_cliente = $eve AND eventos_pagos.id_evento = '$id_evento'";
				
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		
		$res=$bd->query($sql);
		
		$sql=	"SELECT DISTINCT nombre
				FROM eventos
				WHERE id_evento = $rest";
				
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		
		$result=$bd->query($sql);
		
		$result=$result->fetchAll(PDO::FETCH_ASSOC);
		$nombre_evento=$result[0]["nombre"];
		
		$tabla="<center><table class=table><tr><th>$nombre_evento</th></tr>";
		$tabla.="<td><tr><th>No Pago</th><th>Fecha</th><th>Cantidad</th></tr>";
		$id=1;
		$total=0;
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
			$rId = $d["id_pago"];
			$tabla.='<tr>';
			$tabla.="<td>".$id.'</td>';
			$tabla.="<td>".varFechaExtensa($d["fecha"]).'</td>';
			$tabla.='<td>'.$d["cantidad"] .'</td>';
			$tabla.="<td><form action=scripts/pago_pdf.php target=_blank> <!-- <input type=submit  value=Imprimir /> --> <input type=hidden name=idPagoPdf id=idPagoPdf value=$rId><input type=hidden name=idEve id=idEve value=$eve></form></td>";
			$tabla.='</tr>';
			$id++;
			$total+=$d["cantidad"];
		}
		$tabla.='<tr><td></td><td style="text-align:right;">Total=</td><td>'.$total.'</td></tr>';
		$tabla.="</table></center>";
		
		$string_return.=$tabla;
	}
	echo $string_return;
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
}
?>