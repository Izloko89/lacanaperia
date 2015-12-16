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
    <div class="campo_form">
        <label class="label_width">T&iacute;tulo</label>
        <input type="text" name="titulo" id="titulo" class="titulo text_mediano">
    </div>
    <div class="campo_form">
        <label class="label_width">Descrpici&oacute;n</label>
        <input type="text" name="descripcion" id="descripcion" class="descripcion" style="width:400px;">
    </div>
   	<div align="right">
        <input type="button" class="guardar_individual guardar" value="GUARDAR" onclick="guardar_concepto()" data-m="individual" />
        <input type="button" class="modificar" value="MODIFICAR" style="display:none;" />
        <input type="button" class="nueva" value="NUEVA" />
    </div>
    
</form>
<table id="tableEve">
<tr><td><h2>Tipo de Concepto</h2></td></tr>
<?php
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		$id_empresa=$_SESSION["id_empresa"];
		$res=$bd->query("SELECT * FROM conceptos WHERE id_empresa=$id_empresa;");
		$cont = 1;
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			echo '<tr class="salon fondo_azul" ><td ><div align="left" >'.$v["nombre"].'</div></td><td colspan="2" align="right"><span class="eliminar_tevento" onclick="eliminar_art('. $cont .',' . $v["id_concepto"] . ')"/></td></tr>';
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





<script>
function guardar_concepto(){
		term = document.getElementById("nombre").value;
		term1 = document.getElementById("titulo").value;
		term2 = document.getElementById("descripcion").value;
		//datos de los formularios
		//procesamiento de datos
		$.ajax({
			url:'scripts/s_guardar_conceptos.php',
			cache:false,
			async:false,
			type:'POST',
			data:{
				'term':term,
				'term1':term1,
				'term2':term2
			},
			success: function(r){
				if(r){
					alerta("info","Registro añadido satisfactoriamente");
					ingresar=true;
					$("#formularios_modulo").hide("slide",{direction:'right'},rapidez,function(){
						$("#botones_modulo").fadeIn(rapidez);
					});
				}else{
					alerta("error","ocurrio un error al agregar el registro");
				}
			}
		});
	}

</script>