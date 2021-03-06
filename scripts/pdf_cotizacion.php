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
$total_bienvenida = 0;
$total_tiempos = 0;
$total_postres = 0;
$total_barra = 0;
$total_torna = 0;
$total_coctel = 0;

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
        t1.id_tipo,
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

try{
    $bd=new PDO($dsnw,$userw,$passw,$optPDO);
    // para saber los datos del cliente
    $sql="SELECT
        ca.total,
        co.nombre,
        cot.noinvitados,
        (total/noinvitados) as por_invitado
    FROM cotizaciones_articulos as ca
    LEFT JOIN conceptos as co ON co.id_concepto = ca.id_concepto
    LEFT JOIN cotizaciones as cot ON cot.id_cotizacion = ca.id_cotizacion
    WHERE ca.id_cotizacion=$id;";
    $res=$bd->query($sql);
    $concep=$res->fetchAll(PDO::FETCH_ASSOC);
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
        t2.image,
        t3.nombre as concept_name,
        t3.descripcion
    FROM cotizaciones_articulos t1
    LEFT JOIN articulos t2 ON t1.id_articulo=t2.id_articulo
    LEFT JOIN conceptos t3 ON t1.id_concepto=t3.id_concepto
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
                <p style="width:100%; text-align:center; margin:5px auto; font-size:10px; color:#484848">
                    www.lacanaperia.com
                    <br/>                        
                    Tel (55) 59.16.37.52
                </p>
            </td>
        </tr>
    </table>
</page_footer>
<style>
span {
    display:inline-block;
    padding:10px;
}
h1 {
    font-size:20px;
}
.spacer {
    display:inline-block;
    height:1px;
}
.div {
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
.cursiva {
font-family: "Pacifico", sans-serif, cursive;
}
</style>
<!-- header -->
<table style="width:100%; text-align:center;" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width:25%; vertical-align:middle; border-bottom:3px solid #78343B;"><img src="../img/logo.png" style="width:95%;" /></td>
        <td style="width:72%; text-align:left; padding-bottom:2mm; border-bottom:3px solid #78343B;"></td>
    </tr>
</table><!-- Fin header -->
<br/>
<!-- Fecha -->
<table style="width:100%;" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width:85%; text-align:left;"></td>
        <td style="width:20%; text-align:justify;">'.varFechaAbr($fecha).'</td>
    </tr>
</table><!-- Fin fecha -->
<br/>
<!-- Body -->
<!-- Bienvenida -->
<table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
    <tr>
        <td style="width:100%; text-align:left;">Estimad@: <strong>'. $cliente.'</strong></td>
    </tr>        
</table>
<table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
    <tr>
        <td style="width:100%;">
            <div style="width:100%; padding 20px; font-size:12px;text-align:justify;">
                Espero que te encuentres muy bien. Antes que nada quiero agradecerte la oportunidad que nos brindas para participar contigo en la planeación de "'.$nombreEve.'". Sabemos que es un evento muy importante, por ello siéntete en plena confianza de preguntarme cualquier duda respecto al recorrido culinario   y   servicios   adicionales que te  ofrecemos.
            </div>
        </td>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
    <tr>
        <td style="width:100%;">
            <div style="width:100%; padding 20px; font-size:12px;text-align:justify;">
                Hemos creado una historia gastronómica que iremos contando a los invitados a través de diversas creaciones culinarias que degustarán desde su llegada. La historia dice así...
            </div>
        </td>
    </tr>
</table><!-- Fin Bienvenida -->

<!-- Bienvenida de invitados -->';
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "bienvenida";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $html.='
            <!-- Texto -->
            <br/>
            <div style="width:100%; padding:0 20px; text-align:justify;"><strong>'. $d["concept_name"].':</strong></div>
            <br/>
            <div style="width:100%; padding:0 20px; text-align:justify;">'. $d["descripcion"] .'</div>
            <br/>
            <div style="width:100%; padding:0 15px; text-align:center;"><img src="../img/ribbon-inv.png" style="width:50%;" /></div><!-- Fin texto -->
            <div style="width:100%; margin-top:5px;"></div>
            ';
            break;
        }
    }
}
$html.='
<!-- Lista de articulos para Bienvenida invitados -->
<table align="center" border="0" cellspacing="0" cellpadding="0">';
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "bienvenida";
        $pos = strpos($concept_name,$compara);
        if ($pos === false){

        }
        else {
            $html.='
            <tr>
                <td class = "cursiva" style="width:55%; text-align:center;">'. $d["nombre"].'</td>
            </tr>';
        }
    }
}
$html.='
</table><!-- Fin lista -->';
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "bienvenida";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $html.='
            <div style="width:100%; margin-top:5px;"></div>
            <div style="width:100%; padding:5 20px; text-align:justify;">A continuación te presento los canapés seleccionados para la Bienvenida:</div>
            <div style="width:100%; margin-top:5px;"></div>';
            break;
        }
    }
}
$html.='
<!-- Imagenes de articulos -->
<table align="center" border="0" cellspacing="0" cellpadding="0">
    <tr>';
        $total=0;
        foreach($articulos as $id=>$d){
            if (isset($d["id_concepto"])){
                $concept_name = strtolower($d["concept_name"]);
                $compara = "bienvenida";
                $pos = strpos($concept_name,$compara);
                if ($pos === false){

                }
                else {
                    if (!($total == 3)){
                        $html.='
                        <td>
                            <table>
                                <tr>'; 
                                    (isset($d["image"]) && $d["image"] != "") ?
                                    $html.='
                                    <td><img src="../img/articulos/'. $d["image"].'" width="170" height="130" /></td>
                                    ' : $html.='<td></td>';
                                    $html.='
                                </tr>
                                <tr>
                                    <td class = "cursiva" style="width:55%; text-align:center; font-size:10px;">'. $d["nombre"].'</td>
                                </tr>
                            </table>
                        </td>';
                    }
                    else {
                        $total = 0;
                        $html.='</tr></table>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;margin-top:5px; padding:5 30px; text-align:center">
                            <tr>
                                <td>
                                    <table>
                                        <tr>';
                                            (isset($d["image"]) && $d["image"] != "" ) ?
                                            $html.='
                                            <td><img src="../img/articulos/'. $d["image"].'" width="170" height="130" /></td>
                                            ' : $html.='<td></td>';
                                            $html.='
                                        </tr>
                                        <tr>
                                            <td class = "cursiva" style="width:55%; text-align:center">'. $d["nombre"].'</td>
                                        </tr>
                                    </table>
                                </td>';
                    }
                    $total++;
                }
            }
        }
        $html.='
    </tr>
</table><!-- Fin de imagenes de articulos -->';
$total = 0;
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "bienvenida";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $total+=$d["total"];            
        }
    }
}
$total_bienvenida = $total;
$html.='<!-- Fin Bienvenida de invitados -->

<!-- Menú de 3 tiempos -->';
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "tiempos";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $html.='
            <!-- Texto -->
            <div style="width:100%; margin-top:5px;"></div>
            <div style="width:100%; padding:0 20px; text-align:justify;"><strong>'. $d["concept_name"].':</strong></div>
            <br/>
            <div style="width:100%; padding:0 20px; text-align:justify;">'. $d["descripcion"] .'</div>
            <br/>
            <div style="width:100%; padding:0 15px; text-align:center;"><img src="../img/ribbon-menu.png" style="width:50%;" /></div><!-- Fin texto -->
            <table align="center" border="0" cellspacing="0" cellpadding="0" style="margin-top:5px;">
                <tr>
                    <td width="278" style="text-align:center; padding-bottom:5px; font-size:16px; font-weight:bold;">Opción 1</td>
                    <td width="278" style="text-align:center; padding-bottom:5px; font-size:16px; font-weight:bold;">Opción 2</td>
                </tr>
            </table>';
            break;
        }
    }
}

$html.='
<!-- Lista de articulos para Menú de 3 tiempos -->
<table align="center" border="0" cellspacing="0" cellpadding="0">
    <tr>';
        $total=0;
        foreach($articulos as $id=>$d){
            if (isset($d["id_concepto"])){
                $concept_name = strtolower($d["concept_name"]);
                $compara = "tiempos";
                $pos = strpos($concept_name,$compara);
                if ($pos === false){

                }
                else {
                    if (!($total == 2)){
                        $html.='
                        <td width="278" style="text-align:center; font-size:10px;">'. $d["nombre"].'</td>';
                    }
                    else {
                        $total = 0;
                        $html.='</tr></table>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" style="margin-top:3px;">
                            <tr>
                                <td width="278" style="text-align:center; font-size:10px;">'. $d["nombre"].'</td>';
                    }
                    $total++;
                }
            }
        }
        $html.='
    </tr>
</table>';
$html.='
<!-- Imagenes de articulos -->
<table align="center" border="0" cellspacing="0" cellpadding="0">
    <tr>';
$total=0;
        foreach($articulos as $id=>$d){
            if (isset($d["id_concepto"])){
                $concept_name = strtolower($d["concept_name"]);
                $compara = "tornaboda";
                $pos = strpos($concept_name,$compara);
                if ($pos === false){

                }
                else {
                    if (!($total == 3)){
                        $html.='
                        <td>
                            <table>
                                <tr>'; 
                                    (isset($d["image"]) && $d["image"] != "" ) ?
                                    $html.='
                                    <td><img src="../img/articulos/'. $d["image"].'" width="170" height="130" /></td>
                                    ' : $html.='<td></td>';
                                    $html.='
                                </tr>
                                <tr>
                                    <td class = "cursiva" style="width:55%; text-align:center; font-size:10px;">'. $d["nombre"].'</td>
                                </tr>
                            </table>
                        </td>';
                    }
                    else {
                        $total = 0;
                        $html.='</tr></table>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;margin-top:5px; padding:5 30px; text-align:center">
                            <tr>
                                <td>
                                    <table>
                                        <tr>';
                                            (isset($d["image"]) &&  $d["image"] != "" ) ?
                                            $html.='
                                            <td><img src="../img/articulos/'. $d["image"].'" width="170" height="130" /></td>
                                            ' : $html.='<td></td>';
                                            $html.='
                                        </tr>
                                        <tr>
                                            <td class = "cursiva" style="width:55%; text-align:center">'. $d["nombre"].'</td>
                                        </tr>
                                    </table>
                                </td>';
                    }
                    $total++;
                }
            }
        }
$html.='</tr></table>';
$total = 0;
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "tiempos";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $total+=$d["total"];            
        }
    }
}
$total_tiempos = $total;
$html.='
<!-- Fin de menu de 3 tiempos -->

<!-- Mesa de postres -->';
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "postres";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $html.='
            <!-- Texto -->
            <div style="width:100%; margin-top:5px;"></div>
            <div style="width:100%; padding:0 20px; text-align:justify;"><strong>'. $d["concept_name"].':</strong></div>
            <br/>
            <div style="width:100%; padding:0 20px; text-align:justify;">'. $d["descripcion"] .'</div>
            <br/>
            <div style="width:100%; padding:0 15px; text-align:center;"><img src="../img/ribbon-postres.png" style="width:50%;" /></div><!-- Fin texto -->
            <div style="width:100%; margin-top:5px;"></div>';
            break;
        }
    }
}
$html.='
<!-- Lista de articulos para Mesa de postres -->
<table align="center" border="0" cellspacing="0" cellpadding="0">';
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "postres";
        $pos = strpos($concept_name,$compara);
        if ($pos === false){

        }
        else {
            $html.='
            <tr>
                <td class = "cursiva" style="width:55%; text-align:center">'. $d["nombre"].'</td>
            </tr>';
        }
    }
}
$html.='
</table><!-- Fin lista -->';
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "postres";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $html.='
            <div style="width:100%; margin-top:5px;"></div>
            <div style="width:100%; padding:5 20px; text-align:justify;">A continuación te presento los canapés dulces que adornarán y deleitarán a tus invitados:</div>
            <div style="width:100%; margin-top:5px;"></div>';
            break;
        }
    }
}
$html.='
<!-- Imagenes de articulos -->
<table align="center" border="0" cellspacing="0" cellpadding="0">
    <tr>';
    $total=0;
        foreach($articulos as $id=>$d){
            if (isset($d["id_concepto"])){
                $concept_name = strtolower($d["concept_name"]);
                $compara = "postres";
                $pos = strpos($concept_name,$compara);
                if ($pos === false){

                }
                else {
                    if (!($total == 3)){
                        $html.='
                        <td>
                            <table>
                                <tr>'; 
                                    (isset($d["image"]) && $d["image"] != "" ) ?
                                    $html.='
                                    <td><img src="../img/articulos/'. $d["image"].'" width="170" height="130" /></td>
                                    ' : $html.='<td></td>';
                                    $html.='
                                </tr>
                                <tr>
                                    <td class = "cursiva" style="width:55%; text-align:center; font-size:10px;">'. $d["nombre"].'</td>
                                </tr>
                            </table>
                        </td>';
                    }
                    else {
                        $total = 0;
                        $html.='</tr></table>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;margin-top:5px; padding:5 30px; text-align:center">
                            <tr>
                                <td>
                                    <table>
                                        <tr>';
                                            (isset($d["image"]) && $d["image"] != "" ) ?
                                            $html.='
                                            <td><img src="../img/articulos/'. $d["image"].'" width="170" height="130" /></td>
                                            ' : $html.='<td></td>';
                                            $html.='
                                        </tr>
                                        <tr>
                                            <td class = "cursiva" style="width:55%; text-align:center">'. $d["nombre"].'</td>
                                        </tr>
                                    </table>
                                </td>';
                    }
                    $total++;
                }
            }
        }
        $html.='
    </tr>
</table><!-- Fin de imagenes de articulos -->';
$total = 0;
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "postres";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $total+=$d["total"];            
        }
    }
}
$total_postres = $total;
$html.='<!-- Fin de mesa de postres -->

<!-- Barra -->';
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "panes";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $html.='
            <!-- Texto -->
            <div style="width:100%; margin-top:5px;"></div>
            <div style="width:100%; padding:0 20px; text-align:justify;"><strong>'. $d["concept_name"].':</strong></div>
            <div style="width:100%; padding:0 20px; text-align:justify;">'. $d["descripcion"] .'</div>
            ';
            break;
        }
    }
}
$html.='
<!-- Imagenes de articulos -->
<table align="center" border="0" cellspacing="0" cellpadding="0">
    <tr>';
$total=0;
        foreach($articulos as $id=>$d){
            if (isset($d["id_concepto"])){
                $concept_name = strtolower($d["concept_name"]);
                $compara = "panes";
                $pos = strpos($concept_name,$compara);
                if ($pos === false){

                }
                else {
                    if (!($total == 3)){
                        $html.='
                        <td>
                            <table>
                                <tr>'; 
                                    (isset($d["image"]) && $d["image"] != "" ) ?
                                    $html.='
                                    <td><img src="../img/articulos/'. $d["image"].'" width="170" height="130" /></td>
                                    ' : $html.='<td></td>';
                                    $html.='
                                </tr>
                                <tr>
                                    <td class = "cursiva" style="width:55%; text-align:center; font-size:10px;">'. $d["nombre"].'</td>
                                </tr>
                            </table>
                        </td>';
                    }
                    else {
                        $total = 0;
                        $html.='</tr></table>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;margin-top:5px; padding:5 30px; text-align:center">
                            <tr>
                                <td>
                                    <table>
                                        <tr>';
                                            (isset($d["image"]) &&  $d["image"] != "" ) ?
                                            $html.='
                                            <td><img src="../img/articulos/'. $d["image"].'" width="170" height="130" /></td>
                                            ' : $html.='<td></td>';
                                            $html.='
                                        </tr>
                                        <tr>
                                            <td class = "cursiva" style="width:55%; text-align:center">'. $d["nombre"].'</td>
                                        </tr>
                                    </table>
                                </td>';
                    }
                    $total++;
                }
            }
        }
$html.='</tr></table>';
$total = 0;
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "panes";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $total+=$d["total"];            
        }
    }
}
$total_barra = $total;
$html.='<!-- Fin de barra -->

<!-- Estacion tornaboda -->';
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "tornaboda";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $html.='
            <!-- Texto --><div style="width:100%; margin-top:5px;"></div>
            <div style="width:100%; padding:0 20px; text-align:justify;"><strong>'. $d["concept_name"].':</strong></div>
            <div style="width:100%; padding:0 20px; text-align:justify;">'. $d["descripcion"] .'</div>
            <br/>
            <div style="width:100%; padding:0 15px; text-align:center;"><img src="../img/ribbon-torna.png" style="width:50%;" /></div><!-- Fin texto -->
            <div style="width:100%; margin-top:5px;"></div>';
            break;
        }
    }
}
$html.='
<!-- Lista de articulos para estacion tornaboda -->
<table align="center" border="0" cellspacing="0" cellpadding="0">';
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "tornaboda";
        $pos = strpos($concept_name,$compara);
        if ($pos === false){

        }
        else {
            $html.='
            <tr>
                <td class = "cursiva" style="width:55%; text-align:center">'. $d["nombre"].'</td>
            </tr>';
        }
    }
}
$html.='
</table><!-- Fin lista -->';
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "tornaboda";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $html.='
            <div style="width:100%; margin-top:5px;"></div>
            <div style="width:100%; padding:5 20px; text-align:justify;">A continuación te presento las creaciones que los chefs estarán preparando al momento en la Estación Tornaboda</div>
            <div style="width:100%; margin-top:5px;"></div>';
            break;
        }
    }
}
$html.='
<!-- Imagenes de articulos -->
<table align="center" border="0" cellspacing="0" cellpadding="0">
    <tr>';
    $total=0;
        foreach($articulos as $id=>$d){
            if (isset($d["id_concepto"])){
                $concept_name = strtolower($d["concept_name"]);
                $compara = "tornaboda";
                $pos = strpos($concept_name,$compara);
                if ($pos === false){

                }
                else {
                    if (!($total == 3)){
                        $html.='
                        <td>
                            <table>
                                <tr>'; 
                                    (isset($d["image"]) && $d["image"] != "" ) ?
                                    $html.='
                                    <td><img src="../img/articulos/'. $d["image"].'" width="170" height="130" /></td>
                                    ' : $html.='<td></td>';
                                    $html.='
                                </tr>
                                <tr>
                                    <td class = "cursiva" style="width:55%; text-align:center; font-size:10px;">'. $d["nombre"].'</td>
                                </tr>
                            </table>
                        </td>';
                    }
                    else {
                        $total = 0;
                        $html.='</tr></table>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;margin-top:5px; padding:5 30px; text-align:center">
                            <tr>
                                <td>
                                    <table>
                                        <tr>';
                                            (isset($d["image"]) &&  $d["image"] != "" ) ?
                                            $html.='
                                            <td><img src="../img/articulos/'. $d["image"].'" width="170" height="130" /></td>
                                            ' : $html.='<td></td>';
                                            $html.='
                                        </tr>
                                        <tr>
                                            <td class = "cursiva" style="width:55%; text-align:center">'. $d["nombre"].'</td>
                                        </tr>
                                    </table>
                                </td>';
                    }
                    $total++;
                }
            }
        }
        $html.='
    </tr>
</table><!-- Fin de imagenes de articulos -->';
$total = 0;
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "tornaboda";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $total+=$d["total"];            
        }
    }
}
$total_torna = $total;
$html.='<!-- Fin de Estacion de tornaboda -->

<!-- Barra de mezcladores -->';
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "mezcladores";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $html.='
            <!-- Texto -->
            <div style="width:100%; margin-top:5px;"></div>
            <div style="width:100%; padding:0 20px; text-align:justify;"><strong>'. $d["concept_name"].':</strong></div>
            <div style="width:100%; padding:0 20px; text-align:justify;">'. $d["descripcion"] .'</div>
            <div style="width:100%; margin-top:5px;"></div>';
            break;
        }
    }
}
$html.='
<!-- Imagenes de articulos -->
<table align="center" border="0" cellspacing="0" cellpadding="0">
    <tr>';
$total=0;
        foreach($articulos as $id=>$d){
            if (isset($d["id_concepto"])){
                $concept_name = strtolower($d["concept_name"]);
                $compara = "mezcladores";
                $pos = strpos($concept_name,$compara);
                if ($pos === false){

                }
                else {
                    if (!($total == 3)){
                        $html.='
                        <td>
                            <table>
                                <tr>'; 
                                    (isset($d["image"]) && $d["image"] != "" ) ?
                                    $html.='
                                    <td><img src="../img/articulos/'. $d["image"].'" width="170" height="130" /></td>
                                    ' : $html.='<td></td>';
                                    $html.='
                                </tr>
                                <tr>
                                    <td class = "cursiva" style="width:55%; text-align:center; font-size:10px;">'. $d["nombre"].'</td>
                                </tr>
                            </table>
                        </td>';
                    }
                    else {
                        $total = 0;
                        $html.='</tr></table>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;margin-top:5px; padding:5 30px; text-align:center">
                            <tr>
                                <td>
                                    <table>
                                        <tr>';
                                            (isset($d["image"]) &&  $d["image"] != "" ) ?
                                            $html.='
                                            <td><img src="../img/articulos/'. $d["image"].'" width="170" height="130" /></td>
                                            ' : $html.='<td></td>';
                                            $html.='
                                        </tr>
                                        <tr>
                                            <td class = "cursiva" style="width:55%; text-align:center">'. $d["nombre"].'</td>
                                        </tr>
                                    </table>
                                </td>';
                    }
                    $total++;
                }
            }
        }
$html.='</tr></table>';
$total = 0;
foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "mezcladores";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $total+=$d["total"];            
        }
    }
}
$total_coctel = $total;
$html.='
<!-- Fin de barra -->

<table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
    <tr>
        <td style="width:100%; text-align:left;"><strong>Cotización</strong></td>
    </tr>
    <tr>
        <td style="width:100%; text-align:justify;">La siguiente cotización desglosa los servicios propuestos para tu evento contemplando '. $noIn .' invitados:</td>
    </tr>        
</table>

<table align="center" border="0.3" cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;margin-top:5px; padding:5 30px; text-align:center">
    <tr>
        <td colspan="3" style="background-color:#78343B; text-align:center; color:#FFF"><strong>Cotización del Evento</strong></td>
    </tr>
    <tr>
        <th style="width:55%; text-align:center;">Desgloce de Servicios</th>
        <th style="width:15%; text-align:center;">Precio por invitado</th>
        <th style="width:15%; text-align:center;">Precio Total</th>
    </tr>
';

foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "bienvenida";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $invi_bienvenida = $total_bienvenida/$noIn;
            $html.='
            <tr>
                <td style="width:55%;">'.$d["concept_name"].'</td>
                <td style="width:15%; text-align:right;">'.number_format($invi_bienvenida,2).'</td>
                <td style="width:15%; text-align:right;">'.number_format($total_bienvenida,2).'</td>
            </tr>
            ';
            break;
        }
    }
}

foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "tiempos";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $invi_tiempos = $total_tiempos/$noIn;
            $html.='
            <tr>
                <td style="width:55%;">'.$d["concept_name"].'</td>
                <td style="width:15%; text-align:right;">'.number_format($invi_tiempos,2).'</td>
                <td style="width:15%; text-align:right;">'.number_format($total_tiempos,2).'</td>
            </tr>
            ';
            break;
        }
    }
}

foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "postres";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $invi_postres = $total_postres/$noIn;
            $html.='
            <tr>
                <td style="width:55%;">'.$d["concept_name"].'</td>
                <td style="width:15%; text-align:right;">'.number_format($invi_postres,2).'</td>
                <td style="width:15%; text-align:right;">'.number_format($total_postres,2).'</td>
            </tr>
            ';
            break;
        }
    }
}

foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "panes";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $invi_barra = $total_barra/$noIn;
            $html.='
            <tr>
                <td style="width:55%;">'.$d["concept_name"].'</td>
                <td style="width:15%; text-align:right;">'.number_format($invi_barra,2).'</td>
                <td style="width:15%; text-align:right;">'.number_format($total_barra,2).'</td>
            </tr>
            ';
            break;
        }
    }
}

foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "tornaboda";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $invi_torna = $total_torna/$noIn;
            $html.='
            <tr>
                <td style="width:55%;">'.$d["concept_name"].'</td>
                <td style="width:15%; text-align:right;">'.number_format($invi_torna,2).'</td>
                <td style="width:15%; text-align:right;">'.number_format($total_torna,2).'</td>
            </tr>
            ';
            break;
        }
    }
}

foreach($articulos as $id=>$d){
    if (isset($d["id_concepto"])){
        $concept_name = strtolower($d["concept_name"]);
        $compara = "mezcladores";
        $pos = strpos($concept_name,$compara);

        if($pos === false) {
        
        }
        else {
            $invi_coctel = $total_coctel/$noIn;
            $html.='
            <tr>
                <td style="width:55%;">'.$d["concept_name"].'</td>
                <td style="width:15%; text-align:right;">'.number_format($invi_coctel,2).'</td>
                <td style="width:15%; text-align:right;">'.number_format($total_coctel,2).'</td>
            </tr>
            ';
            break;
        }
    }
}

$total_evento = $total_bienvenida + $total_tiempos + $total_postres + $total_barra + $total_torna + $total_coctel;
$total_invi = $total_evento / $noIn;
$iva_invi = $total_invi * 0.16;
$iva_total = $total_evento * 0.16;
$html.='
    <tr>
        <td style="width:55%; text-align:center; font-weight:bold;">Total</td>
        <td style="width:15%; text-align:right;">'.number_format($total_invi,2).'</td>
        <td style="width:15%; text-align:right;">'.number_format($total_evento,2).'</td>
    </tr>
    <tr>
        <td style="width:55%; text-align:center;">IVA(16%)</td>
        <td style="width:15%; text-align:right;">'.number_format($iva_invi,2).'</td>
        <td style="width:15%; text-align:right;">'.number_format($iva_total,2).'</td>
    </tr>
    <tr>
        <td style="width:55%; text-align:center; font-weight:bold;">Total</td>
        <td style="width:15%; text-align:right;">'.number_format($total_invi + $iva_invi,2).'</td>
        <td style="width:15%; text-align:right;">'.number_format($total_evento + $iva_total,2).'</td>
    </tr>
</table>
<br/>
    
<table cellpadding="0" cellspacing="0" style=" font-size:12px;width:100%; margin-top:10px; padding:0 20px;">
    <tr>
        <td style="width:100%;"><div style="width:100%; padding 20px; font-size:12px;text-align:justify;">Te mando un cordial saludo y sigo a tus órdenes para cualquier duda o ajuste respecto a la propuesta.</div></td>
    </tr>        
</table>

<div style="width:100%; padding 20px; font-size:12px;text-align:justify;">ATENTAMENTE</div>
</page>';

$path='../docs/';
$filename="generador.pdf";
//$filename=$_POST["nombre"].".pdf";

//configurar la pagina
//$orientar=$_POST["orientar"];
$orientar="portrait";

//echo $html;
$topdf=new HTML2PDF($orientar,array($mmCartaW,$mmCartaH),'es');
$topdf->writeHTML($css.$html);
$topdf->Output();
//$path.$filename,'F'

//echo "http://".$_SERVER['HTTP_HOST']."/docs/".$filename;

?>