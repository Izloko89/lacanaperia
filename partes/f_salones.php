<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
?>
<script src="js/formularios.js"></script>
<script src="js/salones.js"></script>
<style>
.salon{
	padding:5px 10px;
	margin-right:10px;
	margin-bottom:10px;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
	display:inline-block;
	font-weight:bold;
}
</style>

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


<form id="f_salones" class="formularios">
  <h3 class="titulo_form">Salones</h3>
  	<input type="hidden" name="id_salon" class="id_salon" value="" />
    <div class="campo_form">
        <label class="label_width">Nombre del salón</label>
        <input type="text" name="nombre" class="nombre text_mediano">
    </div>
   	<div align="right">
        <input type="button" class="guardar_individual guardarb" value="GUARDAR" data-m="individual" />
        <input type="button" class="modificar" value="MODIFICAR" style="display:none;" />
        <input type="button" class="nueva" value="NUEVA" />
    </div>
</form>

<table id="tableEve">
<tr><td><h2>SALONES</h2></td></tr>
<?php
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		$res=$bd->query("SELECT * FROM salones order by nombre;");
		$cont = 1;
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			echo '<tr class="salon fondo_azul"><td ><div align="left" >'.$v["nombre"].'</div></td><td colspan="2" align="right"><span class="eliminar_tevento" onclick="eliminar_art(' . $cont . ', ' . $v["id_salon"] . ');"> <input id="idTipo" type="hidden" value="' . $v["id_salon"] . '"/></td></tr>';
			$cont++;
		}
	}catch(PDOException $err){
		echo '<tr><td colspan="20">Error encontrado: '.$err->getMessage().'</td></tr>';
	}
?>
</table>


<script>
	function eliminar_art(elemento, id_item){
		$.ajax({
			url:'scripts/eSalon.php',
			cache:false,
			type:'POST',
			
			data:{
				'id_item':id_item
			},
			success: function(r){
			  if(r){
				document.getElementById("tableEve").deleteRow(elemento);
				alerta("info","<strong>Salon</strong> Eliminado");
				$(".volver").click();
			  }else{
				alerta("error", r);
			  }
			}
		});
	}
</script>

<div align="right">
    <input type="button" class="volver" value="VOLVER">
</div>