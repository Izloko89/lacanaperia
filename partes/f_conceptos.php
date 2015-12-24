<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
?>
<script src="js/conceptos.js"></script>
<style>
#f_tipo_evento .guardar_individual{
	position:relative;
}
#f_tipo_evento .modificar{
	position:relative;
}
.salon{
	padding:5px 10px;
	margin-right:10px;
	margin-bottom:10px;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
	display:inherit;
	font-weight:bold;
}
.eliminar_tevento{
	background: blue url('img/cruz.png') left center no-repeat;
	background-size:contain;
	cursor:pointer;
	width:20px;
	height:20px;
	display:inherit;
	margin-right:10px;
}
</style>
<form id="f_tipo_evento" class="formularios">
  <h3 class="titulo_form">Tipo de concepto</h3>
  	<input type="hidden" name="id_tipo" class="id_tipo" id="id_tipo" value="" />
    <div class="campo_form">
        <label class="label_width">Nombre</label>
        <input type="text" name="nombre" id="nombre" class="nombre text_mediano">
    </div>
    <!-- <div class="campo_form">
        <label class="label_width">T&iacute;tulo</label>
        <input type="text" name="titulo" id="titulo" class="titulo text_mediano">
    </div> -->
    <div class="campo_form">
        <label class="label_width">Descripci&oacute;n</label>
        <textarea name="descripcion" id="descripcion" class="descripcion" style="width:400px;"></textarea>
    </div>
   	<div align="right">
        <input type="button" class="guardar_individual guardar" value="GUARDAR" onclick="guardar_concepto()" data-m="individual" />
        <input type="button" class="modificar" value="MODIFICAR" onclick="modificar_concepto()" data-m="individual" style="display:none;" />
        <input type="button" class="nueva" value="NUEVA" />
    </div>
    
</form>
<!-- <table width="95%"> -->
<table id="tableEve" width="50%">
	<tr>
		<td width="25%"><h2>Concepto</h2><font style="font-size:0.4em; color:#999;">Doble Clic<br />para modificar</font></td>
		<!--<td width="70%"><h2>Descripci&oacute;n</h2></td>-->
		<td width="10%"><h2>Eliminar</h2></td></tr>
<?php
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		$id_empresa=$_SESSION["id_empresa"];
		$res=$bd->query("SELECT * FROM conceptos WHERE id_empresa=$id_empresa;");
		$cont = 1;
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			echo '<tr>';
			echo '<td><div style="cursor:pointer;" class="dbc">'.$v["nombre"].'</div></td>';
			//echo '<td style="text-align:left;">'.$v["descripcion"].'</td>';
			echo '<td><center><span class="eliminar_tevento" onclick="eliminar_art('. $cont .',' . $v["id_concepto"] . ')"/></center></td>';
			echo '</tr>';
			$cont++;
		}
	}catch(PDOException $err){
		echo '<tr><td colspan="20">Error encontrado: '.$err->getMessage().'</td></tr>';
	}
?>
</table>
<div align="right">
    <input type="button" class="volver" value="VOLVER">
</div>