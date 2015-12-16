<?php session_start();
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es_MX");
include_once("datos.php");
require_once('../clases/html2pdf.class.php');
include_once("func_form.php");
$emp=$_SESSION["id_empresa"];
$id = 0;
if(isset($_GET["id_cotizacion"])){
	$id=$_GET["id_cotizacion"];
	$salon=$_GET["salon"];
	
	
	$varpath = "../img/logo.png";
	
	
	 if($salon=="CARACOL")
		{ 
		$varpath = "../img/caracol.png";
		} 
		 if($salon=="HORMIGA") { 
		$varpath = "../img/hormiga.png";
		}
		
	
	
	
}else
{
echo "no trae datos" . $id;
}

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

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	// para saber los datos del cliente
	$sql="SELECT 
		t1.id_cotizacion,
		t1.nombre As nombreEvento,
		t1.fecha,
		t1.personaje,
		t1.medio,
		t1.fechaevento,
		t1.edad,
		t1.no_personas,
		t1.no_ninos,
		t1.no_adultos,
		t1.fechamontaje,
		t1.fechadesmont,
		t1.id_cliente,
		t1.no_ninos_menu,
		t1.no_adultos_menu,
		t1.guarnicion,
		t1.botana,
		t1.hora_cena,
		t1.pastel,
		t1.pinata,
		t1.centro_mesa,
		t1.invitaciones,
		t1.refrescos,
		t1.aguas,
		t1.promocion,
		t1.color_mantel,
		t1.servicios_extra,
		t2.nombre,
		t3.direccion,
		t3.colonia,
		t3.ciudad,
		t3.estado,
		t3.cp,
		t3.telefono,
		t3.celular,
		t3.email
		
		
	FROM cotizaciones t1
	LEFT JOIN clientes t2 ON t1.id_cliente=t2.id_cliente
	LEFT JOIN clientes_contacto t3 ON t1.id_cliente=t3.id_cliente
	WHERE t1.clave=$id;";
	$res=$bd->query($sql);
	$res1=$res->fetchAll(PDO::FETCH_ASSOC);
	$noIn = $res1[0]["noinvitados"];
	$evento=$res1[0];
	$no_invitados=$evento["no_personas"];
	$no_ninos=$evento["no_ninos"];
	$no_adultos=$evento["no_adultos"];
	$edad=$evento["edad"];
	$cliente=$evento["nombre"];
	$personaje=$evento["personaje"];
	$telCliente=$evento["telefono"];
	$nombreEve=$evento["nombreEvento"];
	$domicilio=$evento["direccion"]." ".$evento["colonia"]." ".$evento["ciudad"]." ".$evento["estado"]." ".$evento["cp"];
	$fecha=$evento["fecha"];
	$fechaEve=$evento["fechaevento"];
	$dirEve = $evento["dirEvento"];
	$no_ninos_menu = $evento["no_ninos_menu"];
	$no_adultos_menu = $evento["no_adultos_menu"];
	$guarnicion = $evento["guarnicion"];
	$botana = $evento["botana"];
	$hora_cena = $evento["hora_cena"];
	$pastel = $evento["pastel"];
	$pinata = $evento["pinata"];
	$centro_mesa = $evento["centro_mesa"];
	$invitaciones = $evento["invitaciones"];
	$refrescos = $evento["refrescos"];
	$aguas = $evento["aguas"];
	$promocion = $evento["promocion"];
	$color_mantel = $evento["color_mantel"];
	$servicios_extra = $evento["servicios_extra"];
	
	$medio= $evento["medio"];
	
	$celular= $evento["celular"];
	$email= $evento["email"];
	
	$id_coti= $evento["id_cotizacion"];
	
	//print_r($fecha);
	
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
	WHERE t1.id_cotizacion=$id_coti;";
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
}catch(PDOException $err){
	echo $err->getMessage();
}

//var_dump($articulos);

$html='
<page backbottom="15px">
	
	<page_footer> 
		<TABLE align="right" >
			<TR>
			<TD>
			Blvd. De Los Caminos N.135
			</TD>
			</TR>
			<TR>
			<TD>
			Tel. (871)2044450
			</TD>
			</TR>
			<TR>
			<TD>
			Nextel:
			</TD>
			</TR>
			</TABLE>
	</page_footer> 
	
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
@font-face {
    font-family: "NombreFont";
    src:url(../css/Century_Gothic.ttf) format("truetype");
}
.div{
    color: #000;
    font-family: "NombreFont";
    font-size:12px;
}
</style>
<table style="width:100%;" cellpadding="0" cellspacing="0">    
	<tr>
	
	
		<td valign="top" style="width:20%; text-align:left;"></td>
		
		
		
		<td valign="top" style="width:60%; text-align:center; font-size:10px;"><img src='. $varpath.' style="width:40%;" /></td>
		
		
		
		
		
		<td valign="top" style="width:20%; text-align:left;">
		<div style="width:80%; background-color:#E1E1E1; font-weight:bold; text-align:center; padding-top:5px; padding-bottom:5px; font-size:12px;">COTIZACION N&ordm;</div>
		<div style="width:90%; font-size:12px; color:#C00; text-align:center;"> '. folio(4,$id).'</div></td>
		
		
		
	</tr>
	<tr>
		<td style="width:20%; text-align:left;"></td>
		<td style="width:60%; text-align:left;"></td>
		<td style="width:20%; text-align:justify;">'.varFechaAbr($fecha).'</td>
	</tr>
	
</table>
<br/>
<table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
	<tr>
	<td style="width:100%;"><div style="width:100%; padding 20px; font-size:12px;text-align:justify;">
	<strong>NOMBRE DEL FESTEJADO: '. $nombreEve.'</strong><BR>
	<strong>EDAD QUE CUMPLE: </strong>'. $edad.'   <strong>  PERSONAJE DE LA FIESTA: </strong>'. $personaje.'<BR>
	<strong>NOMBRE DE PAPA O MAMA: </strong>'. $cliente.' <BR>  
	<strong>TELEFONO: </strong>'. $telCliente.'   <strong>  CELULAR: </strong>'. $celular.'<BR>
	<strong>DIRECCION: </strong>'. $domicilio.' <BR>  
	<strong>FECHA DE CONTRATACION: </strong>'. varFechaAbr($fecha).' <BR>
	<strong>EMAIL: </strong>'. $email.' <BR>
	<strong>MEDIO DE PUBLICIDAD: </strong>'. $medio.' <BR>
	
	</div></td>
    </tr>
</table>
<BR>
<div align="center"><strong>DATOS DEL EVENTO</strong></div>
<BR>
<table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
<TR>
<TD>
NUM. DE PERSONAS: '. $no_invitados.' </TD><TD>   NIÑOS: '. $no_ninos.'</TD><TD>     ADULTOS: '. $no_adultos.'</TD>
</TR>
<TR>
<TD>
FECHA DE EVENTO: '. varFechaAbr($fechaEve).' </TD>
</TR>
<TR>
<TD>
PAQUETE:  </TD>
</TR>
<TR>
<TD>
<STRONG>MENU</STRONG></TD>
</TR>
<TR>

<TD>   NIÑOS: '. $no_ninos_menu.'</TD><TD>     ADULTOS: '. $no_adultos_menu.'</TD>
</TR>
<TR>
<TD>
   GUARNICION: '. $guarnicion.'</TD>
</TR>
<TR>
<TD>
   BOTANA: '. $botana.'</TD>
</TR>
<TR>
<TD>
   INTINERARIO</TD>
</TR>
<TR>
<TD>
   HORA DEL EVENTO: '. varHoraAbr($fechaEve).'</TD>
</TR>
<TR>
<TD>
   HORA DE CENA: '. varHoraAbr($hora_cena).'</TD> <td><table><tr><td>
   
   PASTEL: '. $pastel.'
   </td></tr>
   <tr><td>
   
   PIÑATA: '. $pinata.'
   </td></tr><tr><td>
   
   CENTRO DE MESA: '. $centro_mesa.'
   </td></tr><tr><td>
   
   INVITACIONES: '. $invitaciones.'
   </td></tr>
   
   
   </table></td>
</TR>
<TR>
<TD>
   BEBIDA</TD>
</TR>
<TR>
<TD>
   REFRESCOS: '. $refrescos.'</TD>
</TR>
<TR>
<TD>
   AGUAS FRESCAS: '. $aguas.'</TD>
</TR>
<TR>
<TD>
   PROMOCION: '. $promocion.'</TD>
</TR>
<TR>
<TD>
   COLOR DE MANTEL: '. $color_mantel.'</TD>
</TR>
<TR>
<TD>
   </TD>
</TR>
<TR>
<TD>
  SERVICIOS EXTRA: '. $servicios_extra.'</TD>
</TR>
<TR>
<TD>
   FORMA DE PAGO:</TD>
</TR>

</TABLE>


<BR>
<div style="width:95%; padding 20px; font-size:12px; ">
<P STYLE="margin-left: 1cm;"> TOTAL DEL EVENTO:</P>
</DIV>



<div style="width:95%; padding 20px; font-size:12px;  margin-left: 1cm;">
<table align="center" border="0.3" cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;margin-top:5px; padding:5 30px; text-align:center">
                <tr align="center">
                    <th style="width:15%;">CANT.</th>
                    <th style="width:55%;">CONCEPTO</th>
                    <th style="width:15%;">P.U.</th>
                    <th style="width:15%;">IMPORTE</th>
                </tr>';
            $total=0;
            foreach($articulos as $id=>$d){ 
            $total+=$d["total"];
            $html.='
                <tr>
                    <td style="width:15%;text-align:center;">'.$d["cantidad"].'</td>
                    <td style="width:55%;">'. $d["nombre"].'</td>
                    <td style="width:15%;text-align:center;">'. number_format($d["precio"],2).'</td>
                    <td style="width:15%;text-align:right;">'. number_format($d["total"],2).'</td>
                </tr>';
            } 
            $html.='
                <tr>
                    <td style="width:15%;text-align:center;"></td>
                    <td style="width:55%;"></td>
                    <td style="width:15%;text-align:right;">
                        <strong>Total:</strong>
                    </td>
                    <td style="width:15%;text-align:right;">
                        <strong>'. number_format($total,2).'</strong>
                    </td>
                </tr>
            </table>
</div>
<div style="width:95%; padding 20px; font-size:12px; ">
<P STYLE="margin-left: 1cm;"> RESTANTE:</P>
</DIV>



<table  style="font-size:12px; ">
	<tr>
		<td style="width:100%; text-align:left;">
			VIA:
		</td>
	</tr>
	<tr>
		<td style="width:100%; text-align:left;">
			ATENDIO:
		</td>
	</tr>
</table>


<br/>


<table border="" style="font-size:12px; ">
	<tr>
		<td style=" text-align:left;">
			N/S: NO SOLICITO
		</td>
		<td style=" text-align:left;">
			EF: EFECTIVO
		</td>
		<td  width=5085 style=" text-align:right;">
			
		</td>
	</tr>
	<tr>
		<td style=" text-align:left;">
			N/A: NO APLICA
		</td>
		<td style=" text-align:left;">
			TJ: TARJETA
		</td>
		
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