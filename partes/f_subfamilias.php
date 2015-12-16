<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
?>
<script src="js/formularios.js"></script>

<style>
#f_tipo_evento .guardar_individual{
	position:relative;
}
#f_tipo_evento .modificar{
	position:relative;
}
.subfamilia{
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

<form id="f_subfamilias" class="formularios">
<h3 class="titulo_form">SUBFAMILIAS</h3>
<!-- <div class="campo_form">
<label class="label_width">CLAVE</label>
<input type="text" name="clave" class="text_mediano requerido mayuscula">
</div> -->
<div class="campo_form">
<label class="label_width">Nombre de subfamilia</label>
<input type="text" name="nombre" class="text_mediano">
</div>
<div align="right">
        <input type="button" class="guardar_individual" value="GUARDAR" data-m="individual">
    </div>
</form>
<div align="right">
	<input type="button" class="volver" value="VOLVER">
</div>

<table id="tableEve">
<tr><td><h2>SUB-FAMILIAS</h2></td></tr>
<?php
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		$res=$bd->query("SELECT * FROM subfamilias order by nombre;");
		$cont = 1;
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			echo '<tr class="salon fondo_azul"><td ><div align="left" >'.$v["nombre"].'</div></td><td colspan="2" align="right"><span class="eliminar_tevento" onclick="eliminar_art(' . $cont . ', ' . $v["id_subfamilia"] . ');"> <input id="idTipo" type="hidden" value="' . $v["id_subfamilia"] . '"/></td></tr>';
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
			url:'scripts/eSubfamilia.php',
			cache:false,
			type:'POST',
			
			data:{
				'id_item':id_item
			},
			success: function(r){
			  if(r){
				document.getElementById("tableEve").deleteRow(elemento);
				alerta("info","<strong>Subfamilia</strong> Eliminada");
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