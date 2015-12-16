<?php session_start();
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es_MX");
include_once("datos.php");
require_once('../clases/html2pdf.class.php');
include_once("func_form.php");
$emp=$_SESSION["id_empresa"];
$id = 0;
if(isset($_GET["id"])){
	$id=$_GET["id"];
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
		t3.telefono,
		t1.noinvitados,
		t1.dirEvento
	FROM cotizaciones t1
	LEFT JOIN clientes t2 ON t1.id_cliente=t2.id_cliente
	LEFT JOIN clientes_contacto t3 ON t1.id_cliente=t3.id_cliente
	WHERE id_cotizacion=$id;";
	$res=$bd->query($sql);
	$res1=$res->fetchAll(PDO::FETCH_ASSOC);
	$noIn = $res1[0]["noinvitados"];
	$evento=$res1[0];
	$cliente=$evento["nombre"];
	$telCliente=$evento["telefono"];
	$nombreEve=$evento["nombreEvento"];
	$domicilio=$evento["direccion"]." ".$evento["colonia"]." ".$evento["ciudad"]." ".$evento["estado"]." ".$evento["cp"];
	$fecha=$evento["fecha"];
	$fechaEve=$evento["fechaevento"];
	$dirEve = $evento["dirEvento"];
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
		t2.nombre,
        t2.image
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
}catch(PDOException $err){
	echo $err->getMessage();
}

//var_dump($articulos);

$html='
<page backbottom="15px">
	
	<page_footer>
        <table border="0" cellpadding="0" cellspacing="0" style="font-size:13px; width:100%; margin-top:30px; padding:0 20px;">
            <tr>
                <td style="width:100%;vertical-align:top; text-align:center; border-top:'.pxtomm(2).' solid #484848;">
                    <p style="width:100%; text-align:center; margin:5px auto; font-size:10px; color:#484848">www.lacanaperia.com
                        <br/>                        
                    Tel (55) 59.16.37.52
                    </p>
                </td>
            </tr>
        </table>
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
.div{
    color: #000;
    font-family: "NombreFont";
    font-size:12px;
}
@font-face {
    font-family: "Pacifico";
    font-style: normal;
    font-weight: normal;
    color:#C00;
    src:url("fonts/Pacifico.ttf") format("truetype");
}
.cursiva
{
font-family: "Pacifico", sans-serif;
}
</style>
<table style="width:100%; text-align:center;" cellpadding="0" cellspacing="0">
        <tr>
        <td style="width:25%; vertical-align:middle; border-bottom:3px solid #78343B;">
                <img src="../img/logo.png" style="width:95%;" />
        </td>
            <td style="width:72%; text-align:left; padding-bottom:2mm; border-bottom:3px solid #78343B;">
            </td>
        </tr>
        </table>
        <br/>
        <table style="width:100%;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:85%; text-align:left;"></td>
            <td style="width:20%; text-align:justify;">'.varFechaAbr($fecha).'</td>
        </tr>
    </table>
    <br/>
    <table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
        <tr>
            <td style="width:100%; text-align:left;">
                Estimad@: <strong>'. $cliente.'</strong>
            </td>
        </tr>        
    </table>    
    <table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
        <tr>
            <td style="width:100%;">
                <div style="width:100%; padding 20px; font-size:12px;text-align:justify;">
                Espero que te encuentres muy bien. Antes que nada quiero agradecerte la oportunidad que nos brindas para participar contigo en la planeación de "'.$nombreEve.'". Sabemos que es un evento muy importante, por ello siéntete en plena confianza de preguntarme cualquier duda respecto al recorrido	culinario	y	servicios	adicionales	que	te	ofrecemos.</div>
            </td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
        <tr>
            <td style="width:100%;">
                <div style="width:100%; padding 20px; font-size:12px;text-align:justify;">
                Hemos creado una historia gastronómica que iremos contando a los invitados a través de diversas creaciones	culinarias	que	degustarán	desde	su	llegada.	La	historia	dice	así...</div>
            </td>
        </tr>
    </table>
    <br/>
    <div style="width:100%; padding:0 20px; text-align:justify;"><strong>Bienvenida de Invitados:</strong></div>
    <div style="width:100%; padding:0 20px; text-align:justify;">
    <textarea cols="68" rows="4" style="width:100%; padding:0 20px; text-align:justify;border:none;">A la llegada de los invitados se ofrecerá una barra de aguas frescas acompañadas de una selección especial de canapés que	refrescarán	a sus paladares</textarea>
  </div>   
    <div style="width:100%; padding:0 15px; text-align:center;"><img src="../img/Ribbon.png" style="width:50%;" /></div>
    <table align="center" border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;margin-top:5px; padding:5 30px; text-align:center">
        <tr>';
            $total=0;
            foreach($articulos as $id=>$d){ 
            if (!($total == 3)){
                $html.='
                <td>
                    <table>
                        <tr>
                            <td><img src="../img/articulos/'. $d["image"].'" width="150" /></td>
                        </tr>
                        <tr>
                            <td class = "cursiva" style="width:55%; text-align:center">'. $d["nombre"].'</td>
                        </tr>
                    </table>
                </td>';
            }
            else {
                $total = 0;
                $html.='</tr></table>
                <table align="center" border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;margin-top:5px; padding:5 30px; text-align:center"> <tr>';
            }
            $total++;
            }
            $html.='</tr></table>'; 
            $html.='
            <div style="width:100%; padding:5 20px; text-align:justify;">
            A	continuación	te	presento	los	canapés	seleccionados	para	la	Bienvenida:
        </div>
        <br/>
        <div style="width:100%; padding:0 20px; text-align:justify;"><strong>Menú de  3	Tiempos:</strong></div>
        <table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
        <tr>
            <td style="width:100%;">
                <div style="width:100%; padding 20px; font-size:12px;text-align:justify;">
                Al	llegar	a	sus	hermosas	mesas	decoradas	de	forma	muy	especial,	se	les	ofrecerá	a	los	invitados	un	
menú	de	3	tiempos	con	ingredietnes	especialmente	seleccionados	para	la	ocasión	con	deliciosos	
ingredientes.	A	continuación	te	presento	dos	opciones	para	que	ustedes	elijan	cuál	les	gustaría	que	
degustaran	sus	invitados:</div>
            </td>
        </tr>
    </table>
        <table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
        <tr>
            <td style="width:100%; text-align:left;">
                <strong>Cotización</strong>
            </td>
            </tr>
            <tr>
            <td style="width:100%; text-align:justify;">
                La	siguiente	cotización	desglosa	los	servicios	propuestos	para	tu	evento	contemplando	250	invitados:
            </td>
        </tr>        
    </table>
            <table align="center" border="0.3" cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;margin-top:5px; padding:5 30px; text-align:center">
            <tr>
                <td colspan="3" style="background-color:#78343B; text-align:center; color:#FFF"><strong>Cotización	del	Evento</strong></td>
                </tr>
                <tr align="center">
                    <th style="width:55%;">Desgloce de Servicio</th>
                    <th style="width:15%;">Precio por invitado</th>
                    <th style="width:15%;">Precio Total</th>
                </tr>';
            $total=0;
            foreach($articulos as $id=>$d){ 
            $total+=$d["total"];
            $html.='
                <tr>
                    <td style="width:55%;">'. $d["nombre"].'</td>
                    <td style="width:15%;text-align:right;">'. number_format($d["precio"],2).'</td>
                    <td style="width:15%;text-align:right;">'. number_format($d["total"],2).'</td>
                </tr>';
            } 
            $html.='
                <tr>
                    <td style="width:55%;"></td>
                    <td style="width:15%;text-align:right;">
                        <strong>Total:</strong>
                    </td>
                    <td style="width:15%;text-align:right;">
                        <strong>'. number_format($total,2).'</strong>
                    </td>
                </tr>
            </table>       
    <br/>
    <table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
        <tr>
            <td style="width:100%;">
                <div style="width:100%; padding 20px; font-size:12px;text-align:justify;">
                Te mando un cordial saludo y sigo a tus órdenes para cualquier duda o ajuste respecto a la propuesta.</div>
            </td>
        </tr>        
    </table>
 <div style="width:100%; padding 20px; font-size:12px;text-align:justify;">
                ATENTAMENTE</div>
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