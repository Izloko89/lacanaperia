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
            t1.nombre AS nombreEve,
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
      t1.dirEvento,
      t1.fecha
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
        $salonEve = $evento["dirEvento"];
        $fechaDesmontaje = $evento["fechadesmont"];
        $fecha = $evento["fecha"];
        $fechaEve=$evento["fechaevento"];
        $NombreEve=$evento["nombreEve"];

        /*$sql = "select fecha from eventos_pagos where id_evento = '1_$eve'";
        $rse = $bd->query($sql);
        $rse = $rse->fetchAll(PDO::FETCH_ASSOC);
        $eve1 = $rse[0];
        $fechaEve=$eve1["fecha"];*/

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
<?php if($error==""){ $html='
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
td{
    background-color:#FFF;
}
th{
    color:#FFF;
    text-align:center;
}
</style>
<table style="width:100%" cellpadding="0" cellspacing="0" >
    <tr>
      <td valign="top" style="width:15%; text-align:left;">.</td>
      <td valign="top" style="width:70%; text-align:center; font-size:10px;"><img src="../img/logo.png" style="width:50%;" />
      </td>
      <td valign="top" style="width:15%; text-align:left;">

        <div style="width:100%; color:#C00; text-align:center;"></div>
         </td>
    </tr>
</table>
<br/><br/>
<p style="width:100%; text-align:center; margin:5px auto; font-size:12px;"><strong><u>CONTRATO</u></strong></p>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
Contrato de servicio que celebran por una parte LA CANAPERIA, S.A de C.V. representada por _______________ y por la otra '.utf8_encode($cliente).' en representación de ella misma quien en lo sucesivo se denominará en el cuerpo del presente instrumento como el CONTRATADO y el CONTRATANTE  respectivamente de conformidad con las siguientes:
</div>
<br/>
<div style="width:100%; padding:5 20px; text-align:center; font-size:10px;"><strong>DECLARACIONES</strong></div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
Declara el contratado tener su domicilio en  calle Calzada las Palmas #175 Ciudad Granja Zapopan, Jal. y estar profesionalmente calificado para brindar los servicios de decoración y renta de mobiliario para eventos  y celebraciones afines a ésta actividad.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
Declara el contratante tener su domicilio en '. $domicilio .' y que el evento "'.$NombreEve.'" para el cual contrata los servicios del contratado, se realizará el día '.varFechaExtensa($fechaEve).' con horario de entrega '.varFechaExtensa($fechaDesmontaje).'</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
Ambas partes se sujetan a las siguientes:</div>
<div style="width:100%; padding:5 20px; text-align:center; font-size:10px;"><strong>CLAUSULAS</strong></div>
<div style="width:100%; padding:5 20px; text-align:justify; font-size:10px;">1.    El contratante contrata los siguientes elementos para la decoración de su evento en renta.</div>
<div style="border: 0pt;">
  <table border="0" align="center" style="width:65%;">
      <tr align="center">
        <th style="width:15%;color:#000; font-size:10px;">CANT.</th>
        <th style="width:70%;color:#000;font-size:10px;text-align:left;">CONCEPTO</th>
        <th style="width:15%;color:#000;font-size:10px;">IMPORTE</th>
    </tr>';
                     $total=0;
                     foreach($articulos as $id=>$d){
                         $total+=$d["total"];
                         $html .= '
    <tr>
        <td style="width:15%;text-align:center;font-size:10px;">'.$d["cantidad"].'</td>
        <td style="width:70%;font-size:10px;">'.$d["nombre"].'</td>
        <td style="width:15%;text-align:right;font-size:10px;">'.number_format($d["total"],2).'</td>
    </tr>';
                     }
                     $html .= '<tr>
        <td style="width:15%;text-align:center;font-size:10px;"> </td>
        <td style="width:70%;text-align:right;font-size:10px;"><strong>Total:</strong></td>
        <td style="width:15%;text-align:right;font-size:10px;">'.number_format($total,2).'</td>
    </tr>
  </table>
</div>
<br/>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>2.</strong>El contratante pagará al contratado conforme el servicio señalado en la cláusula anterior cuyo detalle fue previamente aprobado en presupuesto que se anexa, las cantidades y forma de acuerdo a lo siguiente:  $'.number_format($total,2).'  ('.numtoletras($total).')  por concepto de renta garantizándose con la firma del contrato y un anticipo de '.number_format($pagado,2).' ('.numtoletras($pagado).') y debiéndose liquidar el saldo el día '.varFechaRest($fechaEve).' para la entrega del servicio.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>3.</strong>El contratado se obliga de acuerdo a la descripción señalada en la cláusula primera a entregar el equipo en óptimo estado en la fecha y hora convenidas y a tener a su personal de guardia para cualquier necesidad requerida en el evento.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>4.</strong>El contratante asume la responsabilidad del mobiliario y equipo relativa a su personal e invitados para cualquier eventualidad que pudiese ocurrir durante la celebración del evento. Así mismo el contratante releva al contratado acerca de cualquier afectación por causas de origen natural o técnico no controladas por el segundo y comprendidas en su servicio.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>5.</strong>En caso de existir algún daño o faltante en el equipo, éste lo pagará el contratado a precio de costo de reposición del  equipo señalado. Se deberá dejar un depósito del 10% por faltantes o daños que en caso de no requerirse se devolverá al finalizar el evento.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>6.</strong>Los precios incluyen la instalación del equipo.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>7.</strong>En caso de cualquier eventualidad ajena a la voluntad del contratado que lo imposibilite a la entrega o realización del evento, éste se compromete a entregar la cantidad recibida en depósito o anticipo por apartado de fecha, sin ninguna otra responsabilidad para el contratante.</div>

<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>8.</strong>En caso de cancelación del contrato por causas imputables al contratante, éste perderá el anticipo entregado al contratado por causa de reservado de fecha.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
<strong>9.</strong>Este contrato está regido por las políticas de contratación y las políticas de renta de mantelería incluidas en su presupuesto inicial, las cuales se dan por comprendidas y aceptadas con la firma de el presente contrato.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
Para interpretación del presente instrumento ambas partes se sujetan a la jurisdicción de los tribunales de ésta ciudad de Guadalajara, Jal.</div>
<div style="width:100%; padding:5 20px; font-size:10px;text-align:justify;">
Leído el presente contrato y conscientes de su contenido lo ratifican y firman en Guadalajara, Jal. El día '.varFechaExtensa($fecha).'.</div>
<table border="0" cellpadding="0" cellspacing="0" style="font-size:11px; width:100%; margin-top:5px;">
  <tr>
    <td style="width:50%;vertical-align:top; text-align:center;">
    Contratante
    <br/>
    <br/>
    <br/>____________________________<br />
    '.$cliente.'
      <br /></td>
    <td style="width:50%;vertical-align:top; text-align:center;">
     Contratado
    <br/>
    <br/>
    <br/>____________________________<br />'.$_SESSION["usuario"].'</td>
  </tr>
</table>
</page>';

                    }else{
    echo $error;
}
//$html=ob_get_clean();
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
