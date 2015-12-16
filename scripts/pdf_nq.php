<?php session_start();
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es_MX");
include_once("datos.php");
require_once('../clases/html2pdf.class.php');
include_once("func_form.php");
$emp=$_SESSION["id_empresa"];

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
        t1.fecha,
        t1.fechaevento,
        t1.fechamontaje,
        t1.fechadesmont,
        t1.id_cliente,
		t1.noinvitados,
		t1.nombre as nombreevento,
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
	$nombre_evento = $evento["nombreevento"];
	$num_invitados=$evento["noinvitados"];
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
}catch(PDOException $err){
    echo $err->getMessage();
}

//var_dump($articulos);
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
.celda_color1
{
    background-color:#FC6;
    color:#000;
}
.celda_color
{
    background-color:#FC9;
    color:#000;
}
</style>
<page_footer> 
<table border="0" cellpadding="0" cellspacing="0" style="font-size:13px; width:100%; margin-top:30px; padding:0 20px;">
	<tr>
		<td style="width:100%;vertical-align:top; text-align:center;">
			<p style="width:100%; text-align:left; margin:5px auto; font-size:10px;">Event Management & Incentives SA de CV
            <br/>Prado de los Tabachines #130 Fracc. Prados Tepeyac
            <br/>Zapopan, Jalisco, México C.P. 45050
            <br/>[52] (33) 31-21-94-67 / 31-22-85-62
            <br>www.procesaeventos.com
            <br/>ventas@procesaeventos.com </p>
        </td>
    </tr>
</table>
</page_footer>
<table style="width:100%;" cellpadding="0" cellspacing="0" >
    <tr>
    <td style="width:15%; text-align:center">&nbsp;</td>
         <td style="width:70%; text-align:center"><img src="../img/logo.png" width="199" height="90"style="width:200px;"/></td>
         <td style="width:15%; text-align:left; padding-bottom:5px;">
            <div style="width:100%; text-align:right;font-size:18px;">FOLIO N&ordm; <?php echo folio(4,$id);  ?></div>
         </td>
    </tr>
</table>
<br/>
<br/>
<div style="width:100%; font-size:12px; text-align:right">Fecha<?php echo varFechaAbr($fecha); ?></div>
<div style="width:60%; font-size:14px; text-align:left"><?php echo $cliente ?></div>
<div style="width:60%; font-size:12px; text-align:left">PRESENTE</div>
<br/>
<br/>
<div style="width:100%; font-size:12px;">Por medio de la presente le envío un cordial saludo, al mismo tiempo aprovecho la ocasion para hacerle llegar la cotizacion que tan amablemente nos fue solicitada para su proximo evento <?php echo $nombre_evento; ?> el día <?php echo $fechaEve; ?>
 para <?php echo $num_invitados; ?> invitados.
</div>

<br>
<table border="1" cellspacing="-0.5" cellpadding="1" style="width:100%;font-size:10px;margin-top:5px;">
    <tr align="center">
        <th style="width:15%;" class="celda_color">CANT.</th>
        <th style="width:55%;"class="celda_color">CONCEPTO</th>
        <th style="width:15%;"class="celda_color">P.U.</th>
        <th style="width:15%;"class="celda_color">IMPORTE</th>
    </tr>
<?php
    $total=0;
    foreach($articulos as $id=>$d){
    $total+=$d["total"];
?>
    <tr>
       <td style="width:15%;text-align:center;"><?php echo $d["cantidad"] ?></td>
        <td style="width:55%;"><?php echo $d["nombre"] ?></td>
        <td style="width:15%;text-align:center;"><?php echo number_format($d["precio"],2) ?></td>
        <td style="width:15%;text-align:center;"><?php echo number_format($d["total"],2) ?></td>
    </tr>
<?php } ?>
    <tr>
        <td style="width:15%;text-align:center;"> </td>
        <td style="width:55%;"> </td>
        <td style="width:15%;text-align:right;"><strong>Total:</strong></td>
        <td style="width:15%;text-align:center;"><strong><?php echo number_format($total,2)?></strong></td>
    </tr>
</table>
<br/>
<br/>
<div style="width:100%;font-size:12px;">Estos costos son más IVA esta sujeto a disponibilidad de fecha y a cambio sin previo aviso, hasta la confirmación del servicio. Misma que tiene que ser por medio de un anticipo del 50%, cantidad restante debera ser liquidada antes del servicio.</div>
<br/>
<div style="width:100%;font-size:12px;">Sin mas por el momento y a la espera de vernos favorecidos con su respuesta, me despido de usted quedando a sus ordenes para cualquier asunto relacionado con el mismo.</div>
<br/>
<br/>
<table border="0" cellpadding="0" cellspacing="0" style="font-size:13px; width:100%; margin-top:30px; padding:0 20px;">
    <tr>
        <td style="width:100%;vertical-align:top; text-align:right;">
            Atentamente,<br /><br/>
            <strong>LIC. RUBI SELEUCO</strong><br />
            GERENTE DE VENTAS Y OPERACIONES</td>
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