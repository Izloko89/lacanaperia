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
		WHERE t1.clave=$eve;";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$evento=$res[0];
		$cliente=$evento["nombre"];
		$telCliente=$evento["telefono"];
		$domicilio=$evento["direccion"]." ".$evento["colonia"]." ".$evento["ciudad"]." ".$evento["estado"]." ".$evento["cp"];
		$fechaEve=$evento["fechaevento"];
		$fechaDesmont=$evento["fechadesmont"];
		$id_evento = $evento["id_evento"];
		
		$ano = substr($fechaEve,0,4);
		$mes= substr($fechaEve,5,2);
		$dia= substr($fechaEve,8,2);
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
		
		
		$sql="SELECT total FROM eventos_total WHERE id_evento=  '1_$id_evento';";
		$res=$bd->query($sql);
		$res=$res->fetchAll(PDO::FETCH_ASSOC);
		$porpagar=$res[0]["total"];
		
		
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
<table style="width:70%;border-bottom:<?php echo pxtomm(2); ?> solid #000;" cellpadding="0" cellspacing="0" border="">
    <tr>
	  <td valign="top" style=" text-align:left;">.</td>
      <td valign="top" style=";"><img src="../img/logo.png" style="width:60%;" />
      
      </td>
       <td valign="top"><img src="../img/salon_hormiga.png" style="width:65%;" />
      </td>
      <td valign="top"><p>FOLIO NO. </p><p style="text-align:right; color:red;"><?php echo $eve; ?></p>
      </td>
    </tr>
</table>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify;">
Contrato de arrendamiento que celebran por una parte <strong>BICHOS FIESTA S.A. DE CV.</strong> quien en lo sucesivo se le denominará arrendador,
quien señala como su domicilio en Blvd. de los caminos No.135 en la ciudad de Torreón,Coahuila y por la parte de la
Sr(a) <strong><?php echo $cliente ?></strong> quien tiene su domicilio en <strong><?php echo $domicilio ?></strong> y a quien en lo sucesivo se le denominará arrendatario, que se sujetan al tenor de las siguientes declaraciones y claúsulas.
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify;">
<strong>PRIMERA.- </strong>Declara el arrendador, ser gerente de la negociación denominada "BICHOS", la cual se ubica en:

______________________________________________________________________________________
</div>

<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify;">
<strong>SEGUNDA.- </strong>Continúa declarando el arrendador que dicha negociación es una construcción con
acabados minimalistas el cual será destinado para la realización de diversos eventos sociales y en lo
particular fiestas infantiles, para lo cual se utiliza el nombre comercial de "BICHOS" estando al corriente
de todos sus impuestos y mismo que cuenta con dos salones, estos disponen de todos los ervicios
urbanos disponibles como lo son: agua, luz, instalaciones sanitarias para hombres y mujeres, cocina
totalmente equipada, áreas sociales y recreativas.
</div>

<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify;">
<strong>TERCERA.- </strong>
Ambas partes manifiestan que es su deseo obligarse,recíprocamente, el arrendador a conceder
el uso y goce temporal del salón de ambos de la negociación denominada "BICHOS" y el arrendatario a
pagar por dicho uso y goce un precio cierto, para lo cual se sujetan al tenor las siguientes:
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify;">
<strong>CLÁUSULAS</strong>
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify;">
<strong>1.- </strong>El arrendador da en arrendamiento al arrendatario, el salón de nombre BICHOS.
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify;">
<strong>2.- </strong>El arrendatario recibe el salón y el mobiliario sin ningún deterioro y a su entera satisfacción, obligandose para con el arrendador
a pagar y/o reponer en su caso por cualquier pérdida, robo o daño, que por motivo de la realización del evento pudiera ocurrirle al
equipo y/o al inmueble en su mención.
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify;">
<strong>3.- </strong>El término del presente contrato será de 4 (cuatro) horas, a partir de : <strong><?php echo $fechaEve ?></strong> a <strong><?php echo $fechaDesmont ?> </strong>mismas que si se prorrogan tendrán un costo de ________ cada hora o fracción.
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify;">
<strong>4.- </strong>La fecha de realización del evento es para el día <strong><?php echo $dia?></strong> del mes <strong><?php echo $mes ?></strong> del <strong><?php echo $ano?></strong>.
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify;">
<strong>5.- </strong>El precio que como contraperstación al uso y goce del salón "BICHOS" y que deberá pagar al arrendatario o el arrendador será
la cantidad de <strong><?php echo $porpagar ?></strong> son:(______________________________) por salón,
pagaderos de la siguiente manera 50% anticipo por separacion y el 50% restante 15 dias hábiles antes de la realización del evento
en caso de incumplimiento, el arrendatario se sujetará a la pena establecida a la claúsula 6 bichos entregará al arrendatario el
recibo correspondiente a los anticipos.
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify;">
<strong>6.- </strong>En caso de que el arrendatario cambie la fecha de evento se le cobrará una penalización de $500 y por su cancelacion de evento
será de $1,500 la penalización. Cantidades que serán retenidas por daños y prejuicios.
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify;">
<strong>7.- </strong>
El arrendador podrá cancelar en cualquier momento el evento social por causa de fuerza mayor, para lo cual se obliga a devolver
al arrendatario el 100% del pago ya efectuado al día de la cancelación; o bien ofrecerá a este una nueva fecha disponible para
realizar en un futuro el evento en cuestión.
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify; align:center;">
<strong>8.-</strong>No se permite chicle en las instalaciónes.
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify; align:center;">
<strong>9.-</strong>El evento debe de estar cubierto en su totalidad 15 dias antes del evento.
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify; align:center;">
<strong>10.-</strong>El salon bichos no se hace responsable de objetos perdidos.
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify; align:center;">
<strong>OTROS SERVICIOS</strong>
</div>

<div style="width:100%; padding:5 20px; font-size:12px;text-align:justify; align:center;">
______________________________________________________________________________________
______________________________________________________________________________________
______________________________________________________________________________________

</div>
<br/>

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