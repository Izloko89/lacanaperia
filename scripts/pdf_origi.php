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
/*td{
	background-color:#FFF;
}*/
th{
	color:#FFF;
		text-align:center;
		}
		</style>
	<table style="width:100%;border-bottom:'.pxtomm(2).' solid #000;" cellpadding="0" cellspacing="0" >
		<tr>
			<td valign="top" style="width:50%; text-align:left; font-size:10px;"><img src="../img/logo.png" style="width:50%;"/></td>
			<td valign="top" style="width:35%; text-align:left;">
				<div style="width:100%; text-align:left;font-size:16px;"><strong>ORDEN DE ALQUILER</strong></div></td>
			<td valign="top" style="width:15%; text-align:left;">
				<div style="width:100%; background-color:#E1E1E1; font-weight:bold; text-align:center; padding-top:5px; padding-bottom:5px;">Folio</div>
			</td>
		</tr>
	</table>
	<div style="width:100%; text-align:right;font-size:12px;">
	<table style="text-align:left;">
		<tr>
			<td valign="top" style="width:30%;">
				<table cellpadding="0" cellspacing="0.8" style="background-color:#000;text-align:center;font-size:12px;width:100%;">
					<tr>
						<td style="width:33%;padding:5px;background-color:#E1E1E1;">Día</td>
						<td style="width:33%;padding:5px;background-color:#E1E1E1;">Mes</td>
						<td style="width:33%;padding:5px;background-color:#E1E1E1;">Año</td>
					</tr>
					<tr>
						<td style="width:33%;padding:5px; background-color:#FFF"><?php echo date("d"); ?></td>
						<td style="width:33%;padding:5px; background-color:#FFF"><?php echo date("m"); ?></td>
						<td style="width:33%;padding:5px; background-color:#FFF"><?php echo date("Y"); ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</div>
	<br>
	<table style="font-size:10px; width:100%; text-align:justify" cellpadding="0" cellspacing="0" >
		<tr>
			<td style="width:10%;">Nombre:</td>
			<td style="width:40%;">
				<div style="border-bottom:0.8px solid #000;"><?php echo $cliente; ?></div>
			</td>
			<td style="width:10%;">Teléfono:</td>
			<td style="width:35%;">
				<div style="border-bottom:0.8px solid #000;"><?php echo $telCliente; ?></div>
			</td>
		</tr>
		</table>
	<table style="font-size:10px; width:100%;" cellpadding="0" cellspacing="0" >
		<tr>
			<td style="width:18%;">Domicilio Particular:</td>
			<td style="width:35%;">
				<div style="border-bottom:0.5px solid #000;"><?php echo $domicilio; ?></div>
			</td>
		</tr>
		<tr>
			<td style="width:18%;">Dirección del Evento:</td>
			<td style="width:40%;">
				<div style="border-bottom:0.5px solid #000;"><?php echo '&nbsp;'; ?></div>
			</td>
			<td style="width:5%;">Lat:</td>
			<td style="width:15%;">
				<div style="border-bottom:0.5px solid #000;"><?php echo '&nbsp;'; ?></div>
			</td>
			<td style="width:5%;">Long:</td>
			<td style="width:15%;">
				<div style=" border-bottom:0.5px solid #000;"><?php echo '&nbsp;'; ?></div>
			</td>
		</tr>
	</table>
	<table style="font-size:10px; width:100%;" cellpadding="0" cellspacing="0" >
		<tr>
			<td style="width:35%;">Si es Salòn Rentado, Telèfono del mismo:</td>
			<td style="width:15%;">
				<div style="border-bottom:0.5px solid #000;"><?php echo '&nbsp;'; ?></div>
			</td>
			<td style="width:18%;">Nombre del encargado:</td>
			<td style="width:30%;">
				<div style="border-bottom:0.5px solid #000;"><?php echo '&nbsp;'; ?></div>
			</td>
		</tr>
		</table>
		<table style="font-size:10px; width:100%;" cellpadding="0" cellspacing="0" >
		<tr>
			<td style="width:18%;">Fecha/Hr. de evento:</td>
			<td style="width:30%;">
				<div style="border-bottom:0.8px solid #000;"><?php echo '&nbsp;'; ?></div>
			</td>
			<td style="width:20%;">Fecha/Hr. de devolución:</td>
			<td style="width:30%;">
				<div style="border-bottom:0.8px solid #000;"><?php echo '&nbsp;'; ?></div>
			</td>
		</tr>
		</table>
		<br/>
		<table style="font-size:10px; width:100%;" cellpadding="0" cellspacing="0" >
		<tr>
			<td style="width:100%;">DOCUMENTOS ENTREGADOS POR EL CLIENTE PARA CELEBRAR EL CONTRATO:</td>
		</tr>
		</table>
		<table style="font-size:10px; width:100%;" cellpadding="0" cellspacing="0" >
		<tr>
			<td style="width:15%;">IFE/INE CLAVE:</td>
			<td style="width:20%;">
			<div style="border-bottom:0.5px solid #000;"><?php echo '&nbsp;'; ?></div>
			</td>
			<td style="width:12%;">No. EMISION:</td>
			<td style="width:20%;">
			<div style="border-bottom:0.5px solid #000;"><?php echo '&nbsp;'; ?></div>
			</td>
			<td style="width:12%;">No. VERTICAL:</td>
			<td style="width:20%;">
			<div style="border-bottom:0.5px solid #000;"><?php echo '&nbsp;'; ?></div>
			</td>
		</tr>
		</table>
		<table style="font-size:10px; width:100%;" cellpadding="0" cellspacing="0" >
		<tr>
			<td style="width:18%;">COMPROBANTE DOM:</td>
			<td style="width:35%;">
			<div style="border-bottom:0.5px solid #000;"><?php echo '&nbsp;'; ?></div>
			</td>
			<td style="width:13%;">FOLIO FISCAL:</td>
			<td style="width:33%;">
			<div style="border-bottom:0.5px solid #000;"><?php echo '&nbsp;'; ?></div>
			</td>
		</tr>
		</table>
		<br/>
		<table style="width:100%; margin-top:5px;">
		<tr><td valign="top" style="width:100%;">
			<table cellpadding="0" cellspacing="0" style=" font-size:10px;width:100%; padding:5px; padding-top:5px; padding-bottom:5px; border::1px solid #000; border-radius:6px;">
		<tr>
		<td style="width:100%; text-align:center"><strong>Aditamentos extras al equipo entregados al cliente:</strong></td>
		</tr>
		<tr>
		<td valign="top" style="width:50%; text-align:center">
		<table cellpadding="0" cellspacing="0" style=" font-size:10px;width:50%;">
		<tr>
			<td style="width:38%; text-align:center">PAR DE MICROFONOS:</td>
			<td style="width:5%;"><div style="padding:4px; padding-top:4px; padding-bottom:3px; border:0.5px solid #000;"></div>&nbsp;</td>
			<td style="width:28%; text-align:center">EXTRA PILAS:</td>
			<td style="width:5%; text-align:left"><div style="padding:4px; padding-top:4px; padding-bottom:3px; border:0.5px solid #000;"></div>&nbsp;</td>
			<td style="width:38%; text-align:center">MOTOR DE INFLABLE:</td>
			<td style="width:5%;text-align:left"><div style="padding:4px; padding-top:4px; padding-bottom:3px; border:0.5px solid #000;"></div>&nbsp;</td>
			<td style="width:23%; text-align:center">EXTENSION:</td>
			<td style="width:5%;text-align:left"><div style="padding:4px; padding-top:4px; padding-bottom:3px; border:0.5px solid #000;"></div>&nbsp;</td>
			<td style="width:45%; text-align:center">REGULADOR DE VOLTAJE:</td>
			<td style="width:5%;text-align:left"><div style="padding:4px; padding-top:4px; padding-bottom:3px; border:0.5px solid #000;"></div>&nbsp;</td>
		</tr>
		</table>
		</td>
		</tr>
	</table>
</td>
</tr>
</table>
<table border="0.3" cellpadding="0" cellspacing="0" style="width:100%; font-size:10px">
	
</table>
<table border="2" cellpadding="0" cellspacing="0" style="width:100%; font-size:8px">
	<tr>
		<td style="width:69%; color:#000">
			<table border="0" cellpadding="0" cellspacing="0" style="width:100%; font-size:8px">
				<tr>
					<td style="width:100%; color:#000; text-align:left">
						<strong>POR FAVOR, EVITE EL COSTO DE IMPREVISTOS MÁS FRECUENTES:
							<br/>REPARACIÓN</strong>
						de Mantelería quemada, $150; Silla Rasgada $50. (Reparación, NO venta).<br/>
						ROCKOLAS/INFLABLES/CARPAS. Según daño.
						<strong>REPOSICIÓN</strong> de Loza/Cristalería Rota de $40 a $50. Cubierto extraviado $20
						<strong>SEGUNDA VISITA</strong> por ausencia de cliente (Sujeto a disponibilidad de ruta): $100
						<strong>SUBIR</strong> rockola o mobiliario pesado a 2ª planta $100 (Sujeto a peso y Factibilidad)</td>
				</tr>
				<tr>
					<td style="width:100%; color:#FFF; text-align:center; background-color:#000">
						RECUERDE QUE ES RENTA DE EQUIPO, NO ES COMPRA NI UN SEGURO POR DAÑOS</td>
				</tr>
			</table>
		</td>
		<td style="width:38%; color:#000">
			<table border="0.3" cellpadding="0" cellspacing="0" style="width:100% background-color:#FFF; font-size:8px">
				<tr>
					<td style="width:40%; color:#000; text-align:left">
						SUB-TOTAL
					</td>
					<td style="width:40%; color:#000; text-align:left">&nbsp;
						
					</td>
				</tr>
				<tr>
					<td style="width:40%; color:#000; text-align:left">
						FLETE
					</td>
					<td style="width:40%; color:#000; text-align:left">&nbsp;
						
					</td>
				</tr>
				<tr>
					<td style="width:40%; color:#000; text-align:left">
						TOTAL
					</td>
					<td style="width:40%; color:#000; text-align:left">&nbsp;
						
					</td>
				</tr>
				<tr>
					<td style="width:40%; color:#000; text-align:left">
						ANTICIPO
					</td>
					<td style="width:40%; color:#000; text-align:left">&nbsp;
						
					</td>
				</tr>
				<tr>
					<td style="width:40%; color:#000; text-align:left">
						RESTA
					</td>
					<td style="width:40%; color:#000; text-align:left">&nbsp;
						
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" style="font-size:11px; width:69%; margin-top:5px;">
	<tr>
		<td style="width:100%;vertical-align:top; text-align:justify;">
			Por este pagaré me(nos) obligo(amos) a pagar incondicionalmente a la orden de:<br />
			_________________________, en la Cd. de Puebla, Pue. el día ______ de ______________ del
			__________ la cantidad de $_______________________________________ <br/>
			________________________________ Este pagaré causará intereses moratorios a razón del _______%<br/>
			mensual si no es pagado a su vencimiento. La firma puesta en cualquier parte del presente <br/>
			documento se considera como aceptación de este pagaré.
		</td>
	</tr>
	<tr>
		<td style="width:50%;vertical-align:top; text-align:right;">
			<br/>
			<br/>
			<br/>___________________________________________<br />
			Valor recibido a total satisfacción, acepto (amos)</td>
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
