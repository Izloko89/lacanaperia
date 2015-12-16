<?php
setlocale(LC_ALL,"");
setlocale(LC_ALL,"es_MX");
include_once("datos.php");
require_once('../clases/html2pdf.class.php');
include_once("func_form.php");
$id = 0;
if(isset($_GET["idPagoPdf"])){
	$id=$_GET["idPagoPdf"];
}
$idEve = 0;
if(isset($_GET["idEve"])){
	$idEve=$_GET["idEve"];
}
$cosas = "";
//funciones para convertir px->mm
function mmtopx($d){
	$fc=96/25;
	$n=$d*$fc;
	return $n."px";
}
function pxtomm($d){
	$fc=96/25;
	$n=$d/$fc;
	return $n."mm";
}
function checkmark(){
	$url="http://".$_SERVER["HTTP_HOST"]."/img/checkmark.png";
	$s='<img src="'.$url.'" style="height:10px;" />';
	return $s;
}

try{
	$sql="SELECT logo FROM empresas WHERE id_empresa=1;";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$res=$bd->query($sql);
	$res=$res->fetchAll(PDO::FETCH_ASSOC);
	$logo='<img src="../'.$res[0]["logo"].'" width="189" />';
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
}

try{
	//id_evento id_cliente plazo fecha cantidad
	$sql="SELECT eventos_pagos.id_pago, clientes.nombre as cliente, eventos.nombre as evento, eventos_pagos.plazo, eventos_pagos.fecha, eventos_pagos.cantidad, eventos_pagos.modo_pago, bancos.nombre as banco FROM eventos_pagos
	INNER JOIN eventos ON eventos_pagos.id_evento = '$idEve'
	INNER JOIN clientes ON eventos_pagos.id_cliente = clientes.id_cliente
	INNER JOIN bancos ON eventos_pagos.id_banco = bancos.id_banco
	WHERE eventos_pagos.id_pago=$id;";
	$res=$bd->query($sql);
	$cosas=$res->fetchAll(PDO::FETCH_ASSOC);
	if(count($cosas) < 1)
	{
		$sql1="SELECT eventos_pagos.id_pago, clientes.nombre as cliente, eventos.nombre as evento, eventos_pagos.plazo, eventos_pagos.fecha, eventos_pagos.cantidad, eventos_pagos.modo_pago FROM eventos_pagos
		INNER JOIN eventos ON eventos_pagos.id_evento = '$idEve'
		INNER JOIN clientes ON eventos_pagos.id_cliente = clientes.id_cliente
		WHERE eventos_pagos.id_pago=$id;";
		$res=$bd->query($sql1);
		$cosas=$res->fetchAll(PDO::FETCH_ASSOC);
	}
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
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
$heightCarta=850;
$widthCarta=600;
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
</style>

<table style="width:100%;" cellpadding="0" cellspacing="0" >
    <tr>		 
      <td style="width:30%; text-align:left;">
       	   <p style="width:100%; padding:4px; margin:0; font-size:7px; text-align:center;">Tel / Fax: (33) 3642-0913 3642-0904<br/>
            eulogio parra #2714<br/>
            colonia providencia, Guadalajara. Jal.<br/>
           www.bariconcept.net</p>
            
         </td>
         <td style="width:55%; text-align:center;"><img src="../img/logo.png" width="76%" height="60" /></td>
      <td style="width:15%; text-align:left; font-size:7px;">
         	<div style="width:100%; text-align:center; ">Recibo </div>
            <div style="width:100%; color:#C00; text-align:center;font-size:14px"><strong>N&ordm; &nbsp;<?php echo $id;?></strong></div>
         </td>
    </tr>
</table>

<table style="width:100%; margin-top:5px;">
<tr>
  <td valign="top" style="width:100%;">
    <table cellpadding="0" cellspacing="0" style=" font-size:9px;width:100%; padding:5px; padding-top:5px; padding-bottom:5px;border:0.5px solid #000; border-radius:20px;">
        <tr>
            <td height="10" style="width:100%; margin-left:5px; border-bottom:0.5px solid #000;"><strong>• Fecha</strong>  &nbsp;<?php echo $cosas[0]["fecha"];?></td>
        </tr><tr>
            <td height="10" style="width:100%; margin-left:5px; border-bottom:0.5px solid #000;"><strong>• Nombre</strong> &nbsp; <?php echo $cosas[0]["cliente"];?></td>            
        </tr><tr>
            <td height="10" style="width:100%; margin-left:5px; border-bottom:0.5px solid #000;"><strong>• Dirección</strong> &nbsp; <?php echo 'domicilio'; ?></td>
            </tr><tr>
            <td height="10" style="width:100%; "><strong>• Fecha del evento</strong> &nbsp; <?php echo 'fechaEvento'; ?></td>
        </tr>
    </table>
</td>
</tr>
</table>
<table style="font-size:9px;width:100%; padding:10px; padding-top:5px; padding-bottom:5px; border:0.5px solid #000; border-radius:20px;" cellpadding="0" cellspacing="0" >
<tr>
		<td style="width:50%"><div style="width:100%; text-align:justify; padding-top:5px; padding-bottom:5px; font-size:10px;">• Por concepto de:&nbsp;<?php echo $cosas[0]["evento"];?></div></td>
		<td style="width:50%"><div style="width:100%; font-size:9px; text-align:center">
			<input name="concepto" type="checkbox" value="anticipo"/>anticipo
			<input name="concepto" type="checkbox" value="Saldo"/>saldo</div>			
		</td>
	</tr>
    <tr><td height="120" style="border-bottom:0.5px solid #000;">&nbsp;</td>
    <td style="width:50%; padding-bottom:5px;border-bottom:0.5px solid #000;">&nbsp;</td>    
    </tr>  
	<tr>		
		<td style="width:50%; padding-bottom:5px;border-bottom:0.5px solid #000;">• La cantidad de: &nbsp;<?php echo number_format($cosas[0]["cantidad"],2);?></td>
		<td style="width:50%; padding-bottom:5px;border-bottom:0.5px solid #000;">&nbsp;</td>
	</tr>	
	<tr>
		<td style="width:50%;">• Forma de pago:&nbsp;<?php echo $cosas[0]["modo_pago"];?></td>	
		<td style="width:50%"><div style="width:100%; font-size:9px; text-align:center">
			<input name="fpago" type="checkbox" value="anticipo"/>cheque <a>No. cheque</a>
			<input name="fpago" type="checkbox" value="Saldo"/>efectivo
			</div>			
		</td>	
	</tr>
	<?php if(isset($cosas[0]["banco"])){?>
	<tr>
		<!--<td style="width:20%"><div style="width:100%; background-color:#E1E1E1; font-weight:bold; text-align:center; padding-top:5px; padding-bottom:5px; font-size:12px;">Banco:</div></td>
		<td style="width:20%"><div style="width:100%; font-size:12px; text-align:center; border-bottom:1px solid #000;"><?php echo $cosas[0]["banco"];?></div></td>-->
	</tr>
	<?php }?>
</table>
<table border="0" cellpadding="0" cellspacing="0" style="font-size:9px; width:100%;">
	<tr>
	  <td style="width:50%; text-align:center;">
      <br/><br/><br/>
			__________________________
            <br />Nombre de quien recibe</td>
            <td style="width:50%; text-align:center;">
             <br/><br/><br/>
			_________________________
            <br />Firma
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