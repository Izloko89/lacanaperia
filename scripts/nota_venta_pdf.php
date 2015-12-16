<?php session_start();
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es_MX");
include_once("datos.php");
require_once('../clases/html2pdf.class.php');
include_once("func_form.php");
$emp=$_SESSION["id_empresa"];

//funciones para usarse dentro de los pdfs
function mmtopx($d){
	$fc=96/25.4;
	$n=$d*$fc;
	return $n."px";
}
function pxtomm($d){
	$fc=96/25.4;
	$n=$d/$fc;
	return $n."mm";
}
function checkmark(){
	$url="http://".$_SERVER["HTTP_HOST"]."/img/checkmark.png";
	$s='<img src="'.$url.'" style="height:10px;" />';
	return $s;
}
function folio($digitos,$folio){
	$usado=strlen($folio);
	$salida="";
	for($i=0;$i<($digitos-$usado);$i++){
		$salida.="0";
	}
	$salida.=$folio;
	return $salida;
}
//tamaño carta alto:279.4 ancho:215.9
$heightCarta=960;
$widthCarta=660;
$celdas=12;
$widthCell=$widthCarta/$celdas;
$mmCartaH=pxtomm($heightCarta);
$mmCartaW=pxtomm($widthCarta);
ob_start();

//sacar los datos del cliente
$error="";
if(isset($_GET["id_evento"])){
	$obs=$_GET["obs"];
	$eve=$_GET["id_evento"];
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		// para saber los datos del cliente
		$sql="SELECT
			t1.id_evento,
			t1.fechaevento,
			t1.fechamontaje,
			t1.fechadesmont,
			t1.id_cliente,
			t2.nombre,
			t3.direccion,
			t3.colonia,
			t3.ciudad,
			t3.estado,
			t3.cp,
			t3.telefono
		FROM eventos t1
		LEFT JOIN clientes t2 ON t1.id_cliente=t2.id_cliente
		LEFT JOIN clientes_contacto t3 ON t1.id_cliente=t3.id_cliente
		WHERE id_evento=$eve;";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$evento=$res[0];
		$cliente=$evento["nombre"];
		$telCliente=$evento["telefono"];
		$domicilio=$evento["direccion"]." ".$evento["colonia"]." ".$evento["ciudad"]." ".$evento["estado"]." ".$evento["cp"];
		$fechaEve=$evento["fechaevento"];

		//para saber los articulos y paquetes
		$sql="SELECT
			t1.*,
			t2.nombre
		FROM eventos_articulos t1
		LEFT JOIN articulos t2 ON t1.id_articulo=t2.id_articulo
		WHERE t1.id_evento=$eve;";
		$res=$bd->query($sql);
		$articulos=array();
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
			if($d["id_articulo"]!=""){
				$art=$d["id_item"];
				unset($d["id_item"]);
				$articulos[$art]=$d;
			}else{
				$art=$d["id_item"];
				unset($d["id_item"]);
				$articulos[$art]=$d;
				$paq=$d["id_paquete"];

				//nombre del paquete
				$sql="SELECT nombre FROM paquetes WHERE id_paquete=$paq;";
				$res3=$bd->query($sql);
				$res3=$res3->fetchAll(PDO::FETCH_ASSOC);
				$articulos[$art]["nombre"]="PAQ. ".$res3[0]["nombre"];

				$sql="SELECT
					t1.cantidad,
					t2.nombre
				FROM paquetes_articulos t1
				INNER JOIN articulos t2 ON t1.id_articulo=t2.id_articulo
				WHERE id_paquete=$paq AND t2.perece=0;";
				$res2=$bd->query($sql);

				foreach($res2->fetchAll(PDO::FETCH_ASSOC) as $dd){
					$dd["precio"]="";
					$dd["total"]="";
					$dd["nombre"]=$dd["cantidad"]." ".$dd["nombre"];
					$dd["cantidad"]="";
					$articulos[]=$dd;
				}
			}
		}
		//para saber el anticipo
		$emp_eve=$emp."_".$eve;
		$sql="SELECT SUM(cantidad) as pagado FROM eventos_pagos WHERE id_evento='$emp_eve';";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$pagado=$res[0]["pagado"];
	}catch(PDOException $err){
		$error= $err->getMessage();
	}
}

$html='<page>
<style>
span{
	display:inline-block;
	padding:10px;
}
h1{
	font-size:20px;
}
.spacer{
	display:inline-block;
	height:1px;
}
td{
	background-color:#FFF;
}
th{
	color:#FFF;
	text-align:center;
}
</style>
<table style="width:100%;border-bottom:'.pxtomm(2).' solid #000;" cellpadding="0" cellspacing="0" >
    <tr>
	  <td valign="top" style="width:15%; text-align:left;">.</td>
      <td valign="top" style="width:70%; text-align:center; font-size:10px;"><img src="../img/logo.png" style="width:50%;" /></td>
      <td valign="top" style="width:15%; text-align:left;">
         	<div style="width:100%; background-color:#E1E1E1; font-weight:bold; text-align:center; padding-top:5px; padding-bottom:5px;">Folio</div>
            <div style="width:100%; color:#C00; text-align:center;">'.folio(5,$eve).'</div>
         </td>
    </tr>
</table>
<table style="width:100%; margin-top:5px;">
  <tr>
    <td style="width:20%; font-size:12px;">Fecha de evento:</td>
    <td style="width:30%;font-size:10px;"><u>'.varFechaAbr($fechaEve).'</u></td>
    <td style="width:20%;font-size:12px;">Lugar de evento:</td>
    <td style="width:35%;font-size:10x;"><u>'. $domicilio .'</u></td>
  </tr>
  <tr>
    <td style="width:20%;font-size:12px;">Nombre de cliente:</td>
    <td style="width:30%;font-size:10px;"><u>'.$nombre.'</u></td>
  </tr><tr>
  <td style="width:20%;font-size:12px;">Domicilio</td>
  <td style="width:30%;font-size:10px;"><u>'.$domicilio.'</u></td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" style=" font-size:11px;width:100%; margin-top:5px;">
	<tr>
    	<td style="width:30%; font-size:12px;">Fecha de entrega:</td>
        <td style="width:70%; font-size:10px;"><div style="margin-left:5px; border-bottom:1px solid #000;"><input style="width:100%; border:0;" type="text" value="'. varFechaExtensa($evento["fechamontaje"])." a ".date("h:i a",strtotime($evento["fechamontaje"])+7200).'" /></div></td>
    </tr><tr>
        <td style="width:30%; font-size:12px;">Fecha para recoger:</td>
        <td style="width:70%; font-size:10px;"><div style="margin-left:5px; border-bottom:1px solid #000;"><input style="width:100%; border:0;" type="text" value="'.varFechaExtensa($evento["fechadesmont"])." a ".date("h:i a",strtotime($evento["fechadesmont"])+7200).'" /></div></td>
    </tr>
</table>
<table style="width:100%; margin-top:5px;">
<tr>
      <td style="width:15%; text-align:left;font-size:12px;">Encargado:</td>
      <td style="width:35%; text-align:left;font-size:10px;">_________________________</td>
      <td style="width:15%; text-aling:right;font-size:12px;">Montan:</td>
      <td style="width:35%; text-align:left;font-size:10px;">_________________________</td>
    </tr>
    <tr>
      <td style="width:15%; text-align:left;font-size:12px;">Unidad utilizada:</td>
      <td style="width:35%;text-aling:left;font-size:10px;">_________________________</td>
    </tr>
</table>
<table border="0.1" cellspacing="0.8" style="width:100%;background-color:#000;font-size:10px;margin-top:2px;">
	<tr>
    <th style="width:15%;">CODIGO</th>
    <th style="width:40%;">DESCRIPCION DE MOBILIARIO</th>
    	<th style="width:15%;">CANT.</th>
        <th style="width:15%;">COLOR</th>
      <th style="width:15%;">COSTO DE REPOSICION</th>
    </tr>	
    <tr>
        <td style="width:15%;text-align:center;"></td>
        <td style="width:40%;text-align:justify;">'. $d["nombre"].'</td>
        <td style="width:15%;text-align:center;">'.$d["cantidad"].'</td>
        <td style="width:15%;"></td>
      <td style="width:15%; text-align:right; "></td>
    </tr>
	<tr>
        <td style="width:15%;text-align:center;"> </td>
        <td style="width:40%;"> </td>
        <td style="width:15%;text-align:right;"><strong>Total:</strong></td>
        <td style="width:15%;text-align:right;"><strong>'.number_format($total,2).'</strong></td>
        <td style="width:15%; text-align:right; "></td>
    </tr>
</table>
<br/>
<br/>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
Debo y pagare a la orden de Gumpy Alquiladoras S.A. de C.V.  En esta ciudad de Puebla, Pue., el día <u><?php echo varFechaAbr($fechaEve)?></u> La cantidad de $'. number_format($total,2).'</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
valor de mercancías arriba mencionadas y que  recibí a mi entera satisfacción. Este pagare es mercantil y esta regido por la  Ley General de Títulos y Operaciones de Crédito en su Art. 173 parte final y Art. Correlativos por no ser pagare domiciliado.
</div>
<table cellpadding="0" cellspacing="0" style=" font-size:11px;width:100%; margin-top:5px;">
	<tr>
        <td style="width:60%; font-size:9px; text-align:justify;"><div style="margin-left:5px; border-bottom:1px solid #FFF;">
        NOTA:SUPLICAMOS AL RECIBIR EL SERVICIO, CONTAR Y REVISAR QUE TODO ESTE CORRECTO. PUES UNA VEZ FIRMADO ESTE PEDIDO ES USTED RESPONSABLE DE ROTURAS, FALTANTES, MANCHAS DE TINTA OCASIONADAS CON PAPEL DE CHINA Y/O CUALQUIER OTRO PRODUCTO INDELEBLE Y/O DAÑO POR MAL USO.(EL CLIENTE ACEPTA EL BUEN ESTADO DEL EQUIPO, EN CASO DE NO REVISION DEL MISMO)
</div></td>
<td style="width:20%;font-size:10; text-align:center;">
<div style="margin-left:5px;">
<br/>_______________________________________
<br/>
Nombre y firma del cliente al recibir el equipo.</div></td>
    </tr>
    <br/>
</table>

<table border="0" cellspacing="0.8" style="width:100%;font-size:10px;margin-top:5px;">
    <tr>
    <td style="width:25%;vertical-align:top; text-align:center; border-bottom:#000;">Descripción del equipo:</td>
    <td style="width:10%;vertical-align:top; text-align:center;border-bottom:#000;"">Cantidad:</td>
  <td style="width:25%;vertical-align:top; text-align:center;border-bottom:#000;"">tipo de Daño:</td>
 <td style="width:15%;text-align:center; padding:inherit"></td>

  </tr>
  <tr>
        <td style="width:25%;text-align:center;"></td>
        <td style="width:10%;"></td>
        <td style="width:25%;text-align:right;"></td>
    </tr>
    <tr>
        <td style="width:25%;text-align:left;border-bottom:#000;">&nbsp;</td>
        <td style="width:10%;border-bottom:#000;"></td>
        <td style="width:25%;text-align:right;border-bottom:#000;"></td>
        <td style="width:35%;text-align:center;"></td>
    </tr>
    <tr>
        <td style="width:25%;text-align:left;border-bottom:#000;">&nbsp;</td>
        <td style="width:10%;border-bottom:#000;"></td>
        <td style="width:25%;text-align:left;border-bottom:#000;"></td>
        <td style="width:35%;text-align:center;"></td>
    </tr>
    <tr>
        <td style="width:25%;text-align:left;border-bottom:#000;">&nbsp;</td>
        <td style="width:10%;border-bottom:#000;"></td>
        <td style="width:25%;text-align:right;border-bottom:#000;"></td>
        <td style="width:35%;text-align:center;"></td>
    </tr>
    <tr>
        <td style="width:25%;text-align:left;border-bottom:#000;">&nbsp;</td>
        <td style="width:10%;border-bottom:#000;"></td>
        <td style="width:25%;text-align:right;border-bottom:#000;"></td>
        <td style="width:35%;text-align:center;"></td>
    </tr>
    <tr>
        <td style="width:25%;text-align:left;border-bottom:#000;">&nbsp;</td>
        <td style="width:10%;border-bottom:#000;"></td>
        <td style="width:25%;text-align:right;border-bottom:#000;"></td>
        <td style="width:35%;text-align:center;"></td>
    </tr>
    <tr>
        <td style="width:25%;text-align:left;"></td>
        <td style="width:10%;"></td>
        <td style="width:25%;text-align:right;"></td>
        <td style="width:20%;vertical-align:top; text-align:center;">
          <br/>
          <br/>_______________________________________
  <br/> Nombre y firma del cliente al retirar el equipo
  <br/>
  <div style="text-align:justify;">Fecha: <br />Hora:</div> </td>
    </tr>
</table>
<p style="font-size:10px; text-align:center;">*LA FALTA DE FIRMA DE LA PRESENTE NO EXENTA DE LA RESPONSABILIDAD DEL CLIENTE SOBRE EL EQUIPO.
</p>
<br/>
<table border="0" cellpadding="0" cellspacing="0" style="font-size:11px; width:100%; margin-top:5px;">
  <tr>
    <td style="width:50%;vertical-align:top; text-align:center;"> _________________________________<br />
      Firma de supervisión del VENDEDOR<br /></td>
    <td style="width:50%;vertical-align:top; text-align:center;"> _________________________________<br />
      Nombre y firma de quién recibe el equipo en bodega.
      <br/>Fecha en que se recibe el equipo en bodega.</td>
  </tr>
</table>
</page>';

$path='../docs/';
$filename="generador.pdf";
//$filename=$_POST["nombre"].".pdf";

//configurar la pagina
//$orientar=$_POST["orientar"];
$orientar="portrait";
$topdf=new HTML2PDF($orientar,array($mmCartaW,$mmCartaH),'es');
$topdf->writeHTML($html);
$topdf->Output();
//$path.$filename,'F'

//echo "http://".$_SERVER['HTTP_HOST']."/docs/".$filename;

?>