<?php session_start();
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es_MX");
include_once("datos.php");
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

$error="";
if(isset($_GET["cot"])){
	$id=$_GET["cot"];
try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	// para saber los datos del cliente
	$sql="SELECT
		t1.id_cotizacion,
		t1.fecha,
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
	FROM cotizaciones t1
	LEFT JOIN clientes t2 ON t1.id_cliente=t2.id_cliente
	LEFT JOIN clientes_contacto t3 ON t1.id_cliente=t3.id_cliente
	WHERE id_cotizacion=$id;";
	$res=$bd->query($sql);
	$res=$res->fetchAll(PDO::FETCH_ASSOC);
	$evento=$res[0];
	$cliente=$evento["nombre"];
	$telCliente=$evento["telefono"];
	$domicilio=$evento["direccion"]." ".$evento["colonia"]." ".$evento["ciudad"]." ".$evento["estado"]." ".$evento["cp"];
	$fecha=$evento["fecha"];
	$fechaEve=$evento["fechaevento"];
}catch(PDOException $err){
	echo $err->getMessage();
}
$bd=NULL;

//para saber los articulos y paquetes
try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT
		t1.*,
		t2.nombre
	FROM cotizaciones_articulos t1
	LEFT JOIN articulos t2 ON t1.id_articulo=t2.id_articulo
	WHERE t1.id_cotizacion=$id;";
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
}
	catch(PDOException $err){
		echo $err->getMessage();
	}
}

//var_dump($articulos);
?>
<?php if($error==""){ ?>
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
            <td valign="top" style="width:50%; text-align:left; font-size:10px;"><img src="../img/gumpy/logo.gif" style="width:50%;"/></td>
            <td valign="top" style="width:35%; text-align:left;">
                <div style="width:100%; text-align:left;font-size:16px;"><strong>ORDEN DE ALQUILER</strong></div></td>
            <td valign="top" style="width:15%; text-align:left;">
                <div style="width:100%; background-color:#E1E1E1; font-weight:bold; text-align:center; padding-top:5px; padding-bottom:5px;">Folio</div>
            </td>
        </tr>
    </table>
    <div style="width:100%; text-align:right;font-size:14px;">
    <table style="text-align:center;">
        <tr>
            <td valign="top" style="width:30%;">
                <table cellpadding="0" cellspacing="0.8" style="background-color:#000;text-align:center;font-size:12px;width:100%;">
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
            </td>
        </tr>
    </table>
    </div>
    <br>
    <table style="font-size:12px; width:50%;" cellpadding="0" cellspacing="0" >
        <tr>
            <td style="width:25%;">Nombre:</td>
            <td style="width:25%;">
                <div style="border-bottom:0.5px solid #000;"><?php echo 'nombre'; ?></div>
            </td>
            <td style="width:25%;">Teléfono:</td>
            <td style="width:25%;">
                <div style="border-bottom:0.5px solid #000;"><?php echo 'telCliente'; ?></div>
            </td>
        </tr>
        </table>
    <br/>
    <table style="font-size:10px; width:50%;" cellpadding="0" cellspacing="0" >
        <tr>
            <td style="width:25%;">Dirección Particular:</td>
            <td style="width:75%;">
                <div style="border-bottom:0.5px solid #000;"><?php echo 'cliente'; ?></div>
            </td>
        </tr>
        <tr>
            <td style="width:15%;">Dirección del Evento:</td>
            <td style="width:35%;">
                <div style="border-bottom:0.5px solid #000;"><?php echo 'direccion'; ?></div>
            </td>
            <td style="width:10%;">Lat:</td>
            <td style="width:15%;">
                <div style="border-bottom:0.5px solid #000;"><?php echo 'latitud'; ?></div>
            </td>
            <td style="width:10%;">Long:</td>
            <td style="width:15%;">
                <div style=" border-bottom:0.5px solid #000;"><?php echo 'altitud'; ?></div>
            </td>
        </tr>
    </table>


<table border="0" cellspacing="0.8" style="width:100%;background-color:#000;font-size:10px;margin-top:5px;">
	<tr>
		<th style="width:10%;">CANTIDAD</th>
		<th style="width:50%;">DESCRIPCIÓN</th>
        <th style="width:10%;">FALTANTE CLIENTE</th>
		<th style="width:15%;">PRECIO UNITARIO</th>
		<th style="width:15%;">IMPORTE</th>
	</tr>
	<tr>
		<td style="width:10%;text-align:center;">1</td>
		<td style="width:50%;">Básico 1</td>
		<td style="width:10%;text-align:center;">10000</td>
		<td style="width:15%;text-align:center;">10000</td>
        <td style="width:15%;text-align:center;">10000</td>
	</tr>
	<tr>
		<td style="width:10%;text-align:center;">1</td>
		<td style="width:50%;">Básico 1</td>
		<td style="width:10%;text-align:center;">10000</td>
		<td style="width:15%;text-align:center;">10000</td>
        <td style="width:15%;text-align:center;">10000</td>
	</tr>
	<tr>
		<td style="width:10%;text-align:center;">1</td>
		<td style="width:50%;">Básico 1</td>
		<td style="width:10%;text-align:center;">10000</td>
		<td style="width:15%;text-align:center;">10000</td>
        <td style="width:15%;text-align:center;">10000</td>
	</tr>
	<tr>
		<td style="width:10%;text-align:center;">1</td>
		<td style="width:50%;">Básico 1</td>
		<td style="width:10%;text-align:center;">10000</td>
		<td style="width:15%;text-align:center;">10000</td>
        <td style="width:15%;text-align:center;">10000</td>
	</tr>
	<tr>
		<td style="width:10%;text-align:center;">1</td>
		<td style="width:50%;">Básico 1</td>
		<td style="width:10%;text-align:center;">10000</td>
		<td style="width:15%;text-align:center;">10000</td>
        <td style="width:15%;text-align:center;">10000</td>
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

<?php }else{
	echo $error;
}?>
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