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
<table style="width:100%;border-bottom:<?php echo pxtomm(2); ?> solid #000;" cellpadding="0" cellspacing="0" >
    <tr>
	  <td valign="top" style="width:15%; text-align:left;">.</td>
      <td valign="top" style="width:70%; text-align:center; font-size:10px;"><img src="../img/gumpy/logo.gif" style="width:50%;" />
      </td>
      <td valign="top" style="width:15%; text-align:left;">

        <div style="width:100%; color:#C00; text-align:center;"></div>
         </td>
    </tr>
</table>

<p style="width:100%; text-align:center; margin:5px auto; font-size:12px;"><strong><u>CONTRATO DE ARRENDAMIENTO DE BIENES MUEBLES:</u></strong></p>
<br/>
<div style="width:100%; padding:0 20px; font-size:10px;text-align:justify;">
CONTRATO DE ARRENDAMIENTO DE BIENES MUEBLES QUE CELEBRAN POR UNA PARTE EL C. ALBERTO ROSETE ZEPEDA, EN ADELANTE “ALQUILADORA GUMPY”, Y POR OTRA EL C.<?php echo $cliente; ?> EN ADELANTE “EL CLIENTE”, QUIENES SE SOMETEN AL TENOR DE LAS SIGUIENTES DECLARACIONES Y CLAUSULAS:</div>
<br/>
<div style="width:100%; padding:0 20px; text-align:center; font-size:10px;"><strong>DECLARACIONES:</strong></div>
<div style="width:100%; padding:0 20px; text-align:justify; font-size:10px;"><strong>I.-</strong> DECLARA “ALQUILADORA GUMPY” TENER SU DOMICILIO EN BLVD. MUNICIPIO LIBRE 1222 LOCAL 3, COL. LOMA LINDA, PUEBLA, PUEBLA, CON PLENA CAPACIDAD PARA CONTRATAR Y OBLIGARSE,</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>II.-</strong> DECLARA “EL CLIENTE” TENER SU DOMICILIO EN  <?php echo $domicilio?>, TAL Y COMO SE DESPRENDE DE LA CREDENCIAL DE ELECTOR NO.<?php echo $domicilio?>, LA CUAL SE ANEXA EN COPIA SIMPLE A LA PRESENTE PARA DEBIDO EFECTO LEGAL CON TELEFONO NO.<?php echo $telCliente?>,  CON PLENA CAPACIDAD PARA CONTRATAR Y OBLIGARSE.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>III.-</strong> DECLARA “ALQUILADORA GUMPY”  SER PROPIETARIO DE TODO DESCRITOS Y SOLICITADOS EN ALQUILER AL ANVERSO DEL PRESENTE, EN ADELANTE “EL EQUIPO”.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>IV.-</strong>  DECLARA “EL CLIENTE” QUE DESEA ARRENDAR “EL EQUIPO” POR EL TIEMPO INDICADO EN EL ANVERSO DEL PRESENTE.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>V.-</strong> POR TRATARSE DE UN SERVICIO ESPORÁDICO Y POR NO CONTAR CON UNA PÓLIZA DE SEGURO PARA ESTAS OPERACIONES EVENTUALES, “ALQUILADORA GUMPY” SE LIBERA DE TODA RESPONSABILIDAD POR CUALQUIER TIPO DE DAÑO O GOLPE QUE PUEDA OCASIONAR ACCIDENTALMENTE EL PERSONAL DE LA EMPRESA SOBRE BIENES DE “EL CLIENTE”.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>VI.-</strong> SI AL RECOGER EL EQUIPO NO SE ENCUENTRA UNA PERSONA AUTORIZADA PARA ENTREGAR EL EQUIPO, O BIEN LA PERSONA QUE ENTREGA EL EQUIPO NO SE HACE RESPONSABLE SOBRE LOS FALTANTES, “EL CLIENTE” DEBERÁ PRESENTARSE A ENTREGARLO, EN CASO DE NO HACERLO, DEBERÁ DE RECONOCER LOS FALTANTES RESULTANTES DE ACUERDO A LA LISTA DE INVENTARIO QUE DECLARE “ALQUILADORA GUMPY” O SUS EMPLEADOS.</div>
<div style="width:100%; padding:0 20px; text-align:center; font-size:10px;"><strong>CLAUSULAS:</strong></div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>PRIMERA: </strong>CONVIENEN AMBAS PARTES QUE EL OBJETO DEL  PRESENTE CONTRATO SON LOS BIENES MUEBLES LISTADOS EN EL ANVERSO DEL PRESENTE,  QUE SON PROPIEDAD DE &rdquo;ALQUILADORA GUMPY&rdquo; LA CUAL SE ENCUENTRAN DESCRITOS EN LA  DECLARACIÓN NÚMERO III, Y QUE SE ENCUENTRA EN ÓPTIMAS CONDICIONES PARA SU USO.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>SEGUNDA: </strong>CONVIENEN AMBAS PARTES QUE EL ARRENDAMIENTO DE “EL EQUIPO” SERÁ POR EL PERIODO DE TIEMPO EN FECHA Y HORA INDICADOS EN EL ANVERSO DEL PRESENTE.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>TERCERA: </strong>CONVIENEN AMBAS PARTES QUE EL COSTO POR EL ARRENDAMIENTO DEL EQUIPO SERÁ EL INDICADO EN EL ANVERSO DEL PRESENTE, EL CUAL PUEDE O NO TENER UN PORCENTAJE DE ANTICIPO CON EL FIN DE APARTAR EL ARRENDAMIENTO DEL EQUIPO PARA LA FECHA Y HORA INDICADOS EN LA SEGUNDA CLÁUSULA, PERO INVARIABLEMENTE SE LIQUIDARÁN AL MOMENTO DE RECIBIR.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>CUARTA: </strong>“EL CLIENTE” UNA VEZ FIRMADO DE RECIBIDO EL PEDIDO DE ALQUILER DE EQUIPO, YA SEA POR ÉL MISMO O POR ALGUNO DE SUS REPRESENTANTES QUE SE ENCONTRARÁ EN EL DOMICILIO AL MOMENTO DE LA ENTREGA, SE HACE RESPONSABLE DEL USO DEL EQUIPO, Y SE ENTIENDE QUE ESTE FUE ENTREGADO EN LAS CONDICIONES Y CANTIDADES ACORDADAS, POR LO QUE CUALQUIER RECLAMACIÓN, AUMENTO O DISMINUCIÓN DE EQUIPO SERÁN POR CUENTA DE “EL CLIENTE”.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>QUINTA: </strong>“EL CLIENTE” ESTÁ OBLIGADO A PROPORCIONAR “ALQUILADORA GUMPY” TODOS LOS DATOS CORRECTOS DEL LUGAR DE SU EVENTO Y EN CASO DE NO HACERLO ASÍ LA EMPRESA NO SE HACE RESPONSABLE POR EL INCUMPLIMIENTO EN LA PRESENTACIÓN DEL SERVICIO.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>SEXTA: </strong>EN CASO DE QUE NO EXISTA ANTICIPO. “ALQUILADORA GUMPY” PODRÁ CANCELAR EL PRESENTE CONTRATO SIN RESPONSABILIDAD ALGUNA.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>SÉPTIMA: </strong>EN CASO DE QUE EL DOMICILIO Y/O EL HORARIO DE INICIO INDICADO POR “EL CLIENTE” DEL EVENTO CAMBIEN, GUMPY SE RESERVA EL DERECHO DE RENOVACIÓN DEL CONTRATO DEPENDIENDO DE LA UBICACIÓN DEL NUEVO DOMICILIO, EN CUYO CASO SE COBRARÍA UNA DIFERENCIA POR CONCEPTO DE TRANSPORTE.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>OCTAVA: </strong>EN CASO DE CANCELACIÓN DEL PRESENTE CONTRATO POR PARTE DE “EL CLIENTE” NO SE DEVOLVERÁ EL ANTICIPO, DEBIDO A GASTOS PREVIOS Y RESERVACIÓN DE LA FECHA DEL EVENTO.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>NOVENA: </strong>AMBAS PARTES CONVIENEN QUE “EL CLIENTE” SERÁ EL ÚNICO RESPONSABLE DE SALVAGUARDAR “EL EQUIPO” DURANTE EL ARRENDAMIENTO, Y RESGUARDARLO EN UN LUGAR SEGURO DE LA INTEMPERIE. EL ARRENDATARIO SE COMPROMETE A ENTREGAR “EL EQUIPO” EN EL ESTADO QUE SE LE ENTREGA DESCRITO EN LA CLÁUSULA PRIMERA.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>DÉCIMA: </strong>“EL CLIENTE” ACEPTA QUE EN CASO DE DAÑO “EL EQUIPO” SE HARÁ RESPONSABLE DEL PAGO DEL CIEN POR CIENTO DE LOS DESPERFECTOS. PARA LO CUAL FIRMARÁ UN PAGARÉ EN BLANCO COMO GARANTÍA EL CUAL ESTÁ AL ANVERSO DEL PRESENTE.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>DÉCIMA PRIMERA: </strong>“EL CLIENTE” ENTREGARÁ COMO MEDIO DE IDENTIFICACION SU CREDENCIAL DE ELECTOR ORIGINAL Y UN COMPROBANTE DOMICILIARIO CON MAXIMO DE DOS MESES DE ANTIGÜEDAD A “ALQUILADORA GUMPY” O SUS EMPLEADOS AL MOMENTO DE RECIBIR “EL EQUIPO”, MISMA QUE SERÁ DEVUELTA AL MOMENTO DE REGRESAR “EL EQUIPO” EN LAS CONDICIONES DESCRITAS EN LA CLAUSULA PRIMERA.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>DÉCIMA SEGUNDA: </strong>AMBAS PARTES CONVIENEN QUE “EL EQUIPO” DEBERÁ PERMANECER EN EL DOMICILIO INDICADO COMO DOMICILIO DE ENTREGA EN EL ANVERSO DEL PRESENTE CONTRATO Y BAJO NINGUNA CIRCUNSTANCIA PODRÁ SERÁ TRASLADADO A OTRO LUGAR.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>DÉCIMA TERCERA: </strong>AMBAS PARTES ACEPTAN Y RECONOCEN EXPRESAMENTE QUE EL PRESENTE CONTRATO, NO CREA NI GENERA NI CONSTITUYE UNA RELACIÓN DE TRABAJO, POR LO QUE AMBAS PARTES QUEDAN RELEVADOS DE CUALQUIER OBLIGACIÓN DE CARÁCTER LABORAL REGULADA Y SANCIONADA POR LA LEY FEDERAL DEL TRABAJO</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>DÉCIMA CUARTA: </strong>AMBAS PARTES ACUERDAN QUE SON CAUSAS DE TERMINACIÓN DEL PRESENTE CONTRATO: <strong>1.- </strong>EL MUTUO ACUERDO BA JO LAS LIMITANTES INDICADAS EN EL PRESENTE CONTRATO; <strong>2.- </strong> LA MUERTE DE “EL CLIENTE” O SU EXTINCIÓN EN CASO DE SER PERSONA MORAL; <strong>3.- </strong> POR LA RENUNCIA DE “ALQUILADORA GUMPY” O “EL CLIENTE”, SIEMPRE QUE DEN AVISO OPORTUNO POR LO MENOS, CON 11 DÍAS HÁBILES DE ANTICIPACIÓN. CUMPLIDO Y SATISFECHO ESTE REQUISITO, SE LIBERA A LOS CONTRATANTES DE CUALQUIER RESPONSABILIDAD. <strong>4.- </strong> EL CIERRE PARCIAL, TEMPORAL O DEFINITIVO DEL ESTABLECIMIENTO “ALQUILADORA GUMPY”, MISMO QUE SE OBLIGA A RESTITUIR LA CANTIDAD PAGADA POR EL PRESTATARIO, SIEMPRE QUE LOS SERVICIOS NO HAYAN SIDO EJECUTADOS.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>DECIMA QUINTA: </strong>LAS PARTES SE SOMETEN A LAS JURISDICCIONES DE LAS LEYES Y TRIBUNALES DE LA CIUDAD DE PUEBLA, DEL ESTADO DE PUEBLA RENUNCIANDO A CUALQUIER OTRO QUE PUDIERE CORRESPONDER EN VIRTUD DE SUS DOMICILIOS PRESENTES O FUTUROS.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>DÉCIMA SEXTA: </strong>EL “CLIENTE” CUENTA CON UN PLAZO DE 5 DÍAS HÁBILES POSTERIORES A LA FIRMA DEL PRESENTE  CONTRATO  PARA CANCELAR  LA OPERACIÓN  SIN RESPONSABILIDAD  ALGUNA DE SU PARTE, EN CUYO CASO “ALQUILADORA GUMPY”  SE OBLIGA A REINTEGRAR TODAS LAS CANTIDADES QUE EL “CLIENTE”  LE HAYA ENTREGADO; LO ANTERIOR  NO SERÁ APLICABLE  SI LA FECHA DE LAS PRESTACIÓN  DEL SERVICIO SE ENCUENTRA  A DIEZ DÍAS HÁBILES O MENOS DE LA FECHA INDICADA EN LA CLÁUSULA SEGUNDA.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>DÉCIMA SÉPTIMA: </strong>EN CASO DE QUE “ALQUILADORA GUMPY” SE ENCUENTRE IMPOSIBILITADO PARA PRESTAR EL SERVICIO POR CASO FORTUITO  O FUERZA MAYOR,  COMO INCENDIO  TEMBLOR U OTROS ACONTECIMIENTOS  DE LA NATURALEZA  O HECHO  DEL HOMBRE AJENOS  A LA VOLUNTAD  “EL PRESTADOR DEL SERVICIO”, NO SE INCURRIRÁ  EN INCUMPLIMIENTO, POR LO QUE NO HABRÁ  PENA CONVENCIONAL EN DICHOS SUPUESTOS,  DEBIENDO ÚNICAMENTE  “ALQUILADORA GUMPY” REINTEGRAR AL CLIENTE EL ANTICIPO QUE LE HUBIERE ENTREGADO.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>DÉCIMA OCTAVA: </strong>PARA LA INTERPRETACIÓN Y CUMPLIMIENTO DEL PRESENTE CONTRATO LAS PARTES  SE SOMETEN A LA COMPETENCIA DE LA PROCURADURÍA FEDERAL DEL CONSUMIDOR  Y A LAS LEYES Y JURISDICCIÓN DE LOS TRIBUNALES COMPETENTES DEL FUERO COMÚN DE LA CIUDAD DE PUEBLA.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>DECIMA NOVENA: </strong>“EL CLIENTE” SE HACE RESPONSABLE POR  “EL EQUIPO” RENTADO EN LOS SIGUIENTES CASOS: ROBO, DESPERFECTO POR ACCIÓN DEL CLIMA, MAL USO EXCESIVO DEL MISMO Y POR DESASTRE NATURAL, CASO FORTUITO O CUALQUIER OTRO CASO QUE NO INVOLUCRE EL MANEJO DE LA EMPRESA CON RESPECTO A “EL EQUIPO”.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>VIGÉSIMA: </strong>”ALQUILADORA GUMPY” SE OBLIGA A PRESTAR EL SERVICIO SOLICITADO EN LA FECHA, LUGAR, Y LA HORA INDICADA QUE SOLICITE “EL CLIENTE”.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>VIGÉSIMA PRIMERA: </strong>“ALQUILADORA GUMPY” RESPETARÁ EL PRECIO FIJADO A “EL CLIENTE” SALVO QUE ESTE CAMBIE LAS ESPECIFICACIONES, SERVICIOS, O EQUIPOS ESPECIFICADOS EN EL CUERPO DEL PRESENTE CONTRATO.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>VIGÉSIMA SEGUNDA: </strong>EL EQUIPO RENTADO SE ENTREGA Y SE RECOGE A PIE DE PISO EN DADO CASO QUE NECESITE MANIOBRA ESTA SE COBRARÁ SEGÚN CADA CASO. SI EL CLIENTE NO PAGA MANIOBRA Y AL RECOGER EL EQUIPO ESTE SE ENCUENTRA EN OTRO LUGAR QUE NO SEA EN DONDE SE ENTREGÓ EL CLIENTE SE COMPROMETE A PAGAR EL 50% DE MANIOBRA A “ALQUILADORA GUMPY” O SU PERSONAL.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>VIGÉSIMA TERCERA: </strong>EN CASO DE ACCIDENTE DEBIDO AL USO O ABUSO DE LAS CARACTERÍSITICAS DE “EL EQUIPO” POR PARTE DE “EL CLIENTE” Y/O SUS FAMILIARES E INVITADOS DURANTE EL TIEMPO QUE DURE EL ARRENDAMIENTO, DESLINDAN A “ALQUILADORA GUMPY” DE TODA RESPONSABILIDAD; “EL CLIENTE” ASUME TODA LA RESPONSABILIDAD DE QUE LOS NIÑOS Y MENORES DE EDAD ESTARÁN EN PERMANENTE SUPERVISIÓN DE UN ADULTO, PRINCIPALMENTE EN ROCKOLAS E INFLABLES. “</div>
<br/>
<br/>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
PUEBLA, PUE. A <?php echo $fechaEve?></div>
<table border="0" cellpadding="0" cellspacing="0" style="font-size:11px; width:100%; margin-top:5px;">
  <tr>
    <td style="width:50%;vertical-align:top; text-align:center;">
    <br/>
    <br/>
    <br/>
    _______________________________________<br />
      NOMBRE Y FIRMA DE REPRESENTANTE DE “ALQUILADORA GUMPY”<br />
      <br /></td>
    <td style="width:50%;vertical-align:top; text-align:center;">
     <br/>
    <br/>
    <br/>
    _________________________________<br />
      NOMBRE Y FIRMA DE “EL CLIENTE”</td>
  </tr>
</table>
<br/>
<br/>
<table border="0" cellpadding="0" cellspacing="0" style="font-size:11px; width:100%; margin-top:5px;">
  <tr>
      <td style="width:100%;vertical-align:top; text-align:center;">
      <p style="width:100%; text-align:center; margin:5px auto; font-size:10px;">Oficina en Eulogio Parra # 2714 Col. Providencia. Guadalajara, Jalisco, México. Tel: 52 (33) 3642 0913,
3642 0904, 3832 5933 </p></td>
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