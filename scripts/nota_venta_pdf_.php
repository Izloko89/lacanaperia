<?php session_start();
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es_MX");
require_once('../clases/html2pdf.class.php');
include_once("func_form.php");
$emp=$_SESSION["id_empresa"];

//funciones para convertir px->mm
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
//tamaño carta alto:279.4 ancho:215.9
$heightCarta=480;
$widthCarta=400;
$celdas=12;
$widthCell=$widthCarta/$celdas;

$mmCartaH=pxtomm($heightCarta);
$mmCartaW=pxtomm($widthCarta);
ob_start();
?>
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
<table style="width:100%;border-bottom:<?php echo pxtomm(2); ?> solid #000;" cellpadding="0" cellspacing="0" >
    <tr>
		 <td style="width:25%; text-align:left;"><img src="../img/laspalmas/logo.jpg" style="width:100%;" /></td>
         <td style="width:50%; text-align:left;">
         	<p style="width:100%; padding:4px; margin:0; font-size:10px; text-align:justify;">RENTA DE SILLAS, MESAS, TABLONES, BANQUETES Y TODO PARA SUS FIESTAS</p>
            <p style="width:100%; padding:4px; margin:0; font-size:10px; text-align:center;">Araceli Crisanto Arteaga<br />R.F.C. : CIIA 651204 J37</p>
         </td>
         <td style="width:25%; text-align:left;">
         	<div style="width:100%; background-color:#E1E1E1; font-weight:bold; text-align:center; padding-top:5px; padding-bottom:5px;">PEDIDO No</div>
            <div style="width:100%; color:#C00; text-align:center;">0001</div>
         </td>
    </tr>
</table>

<p style="width:100%; text-align:center; margin:5px auto; font-size:10px;">CARRANZA No 702 COL. CENTRO TEL. 212-52-31 CEL. 921 123 0765 COATZACOALCOS, VER</p>
<table style="width:100%; margin-top:5px;">
<tr><td valign="top" style="width:70%;">
    <table cellpadding="0" cellspacing="0" style=" font-size:10px;width:100%; padding:10px; padding-top:5px; padding-bottom:5px; border::1px solid #000; border-radius:6px;">
        <tr>
            <td style="width:25%;">Nombre:</td>
            <td style="width:75%;"><div style="margin-left:5px; border-bottom:1px solid #000;"><?php echo 'fechaCot'; ?></div></td>
        </tr><tr>
            <td style="width:25%;">Dirección:</td>
            <td style="width:75%;"><div style="margin-left:5px; border-bottom:1px solid #000;"><?php echo 'cliente'; ?></div></td>
        </tr><tr>
            <td style="width:25%;">Teléfono:</td>
            <td style="width:75%;"><div style="margin-left:5px; border-bottom:1px solid #000;"><?php echo 'telCliente'; ?></div></td>
        </tr>
    </table>
</td>
<td valign="top" style="width:30%;">
	<table cellpadding="0" cellspacing="0.8" style="background-color:#000;text-align:center;font-size:10px;width:100%;">
        <tr>
            <td style="width:33%;padding:5px;background-color:#E1E1E1;">Día</td>
            <td style="width:33%;padding:5px;background-color:#E1E1E1;">Mes</td>
            <td style="width:33%;padding:5px;background-color:#E1E1E1;">Año</td>
        </tr>
        <tr>
            <td style="width:33%;padding:5px;"><?php echo date("d"); ?></td>
            <td style="width:33%;padding:5px;"><?php echo date("m"); ?></td>
            <td style="width:33%;padding:5px;"><?php echo date("Y"); ?></td>
        </tr>
    </table>
</td></tr>
</table>

<table cellpadding="0" cellspacing="0" style=" font-size:11px;width:100%; margin-top:5px;">
	<tr>
    	<td style="width:27%; font-size:10px;">Fecha de entrega:</td>
        <td style="width:73%;"><div style="margin-left:5px; border-bottom:1px solid #000;"><input style="width:100%; border:0;" type="text" value="<?php echo varFechaExtensa(date("Y-m-d H:i:s"))." a ".date("h:i a",strtotime(date("Y-m-d H:i:s"))+7200); ?>" /></div></td>
    </tr><tr>
        <td style="width:27%; font-size:10px;">Fecha para recoger:</td>
        <td style="width:73%;"><div style="margin-left:5px; border-bottom:1px solid #000;"><input style="width:100%; border:0;" type="text" value="<?php echo varFechaExtensa(date("Y-m-d H:i:s"))." a ".date("h:i a",strtotime(date("Y-m-d H:i:s"))+7200); ?>" /></div></td>
    </tr>
</table>

<table border="0" cellspacing="0.8" style="width:100%;background-color:#000;font-size:10px;margin-top:5px;">
	<tr>
    	<th style="width:15%;">CANT.</th>
        <th style="width:55%;">CONCEPTO</th>
        <th style="width:15%;">P.U.</th>
        <th style="width:15%;">IMPORTE</th>
    </tr>
    <tr>
        <td style="width:15%;text-align:center;">1</td>
        <td style="width:55%;">Básico 1</td>
        <td style="width:15%;text-align:center;">10000</td>
        <td style="width:15%;text-align:center;">10000</td>
    </tr>
    <tr>
        <td style="width:15%;text-align:center;">1</td>
        <td style="width:55%;">Básico 1</td>
        <td style="width:15%;text-align:center;">10000</td>
        <td style="width:15%;text-align:center;">10000</td>
    </tr>
    <tr>
        <td style="width:15%;text-align:center;">1</td>
        <td style="width:55%;">Básico 1</td>
        <td style="width:15%;text-align:center;">10000</td>
        <td style="width:15%;text-align:center;">10000</td>
    </tr>
    <tr>
        <td style="width:15%;text-align:center;">1</td>
        <td style="width:55%;">Básico 1</td>
        <td style="width:15%;text-align:center;">10000</td>
        <td style="width:15%;text-align:center;">10000</td>
    </tr>
    <tr>
        <td style="width:15%;text-align:center;">1</td>
        <td style="width:55%;">Básico 1</td>
        <td style="width:15%;text-align:center;">10000</td>
        <td style="width:15%;text-align:center;">10000</td>
    </tr>
    <tr>
        <td style="width:15%;text-align:center;"></td>
        <td style="width:55%;"></td>
        <td style="width:15%;text-align:right;">Total:</td>
        <td style="width:15%;text-align:center;">10000</td>
    </tr>
    <tr>
        <td style="width:15%;text-align:center;"> </td>
        <td style="width:55%;"> </td>
        <td style="width:15%;text-align:right;">Anticipo:</td>
        <td style="width:15%;text-align:center;"><?php echo '1000'; ?></td>
    </tr>
    <tr>
        <td style="width:15%;text-align:center;"> </td>
        <td style="width:55%;"> </td>
        <td style="width:15%;text-align:right;">Restante:</td>
        <td style="width:15%;text-align:center;">9000</td>
    </tr>
</table>
<p style="font-size:10px;">NOTA: El cliente <u><?php echo 'Nombre del cliente'; ?></u> se hace responsable por cualquier daño o maltrato en el equipo o material rentado, pagando el costo de mismo. La renta es hasta por 12 horas, El acomodo es por parte del cliente.</p>
<table border="0" cellpadding="0" cellspacing="0" style="font-size:11px; width:100%; margin-top:5px;">
	<tr>
    	<td style="width:100%;vertical-align:top; text-align:center;">
        	ATENTAMENTE<br />C. P. Salomón Bahena Salinas<br />Gerente
        </td>
    </tr>
</table>
<?php
$html=ob_get_clean();
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