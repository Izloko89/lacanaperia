<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
?>
<script src="js/formularios.js"></script>
<script src="js/tipo_evento.js"></script>
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
  <h3 class="titulo_form">Tipo de evento</h3>
  	<input type="hidden" name="id_tipo" class="id_tipo" value="" />
    <div class="campo_form">
        <label class="label_width">Nombre de evento</label>
        <input type="text" name="nombre" id="nombre" class="nombre text_mediano">
    </div>
   	<div align="right">
        <input type="button" class="guardar" value="GUARDAR" onclick="agregar_tipoevento();" />
        <input type="button" class="modificar" value="MODIFICAR" style="display:none;" />
        <input type="button" class="nueva" value="NUEVA" />
    </div>
    
</form>
<table id="tableEve">
<tr><td><h2>Tipo de Evento</h2></td></tr>
<?php
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		$id_empresa=$_SESSION["id_empresa"];
		$res=$bd->query("SELECT * FROM tipo_evento WHERE id_empresa=$id_empresa order by nombre;");
		$cont = 1;
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			echo '<tr class="salon fondo_azul" ><td ><div align="left" >'.$v["nombre"].'</div></td><td colspan="2" align="right"><span class="eliminar_tevento" onclick="eliminar_art(' . $cont . ', ' . $v["id_tipo"] . ');"> <input id="idTipo" type="hidden" value="' . $v["id_tipo"] . '"/></td></tr>';
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
			url:'scripts/eTipo_evento.php',
			cache:false,
			type:'POST',
			data:{
				'id_item':id_item
			},
			success: function(r){
			  if(r){
				document.getElementById("tableEve").deleteRow(elemento);
				alerta("info","<strong>Tipo de evento</strong> Eliminado");
				$(".volver").click();
			  }else{
				alerta("error", r);
			  }
			}
		});
	}
</script>
<script>
	function agregar_tipoevento(){
	var nombre = document.getElementById("nombre").value;
	if(nombre=="")
	{alerta("info","Nombre no valido para el tipo de evento");
	}
	else
	{
	
	
		$.ajax({
			url:'scripts/aTipo_evento.php',
			cache:false,
			type:'POST',
			data:{
				'nombre':nombre
			},
			success: function(r){
			  if(r){
				
				alerta("info","<strong>Tipo de evento</strong> Agregado");
				
			  }else{
				alerta("error", r);
			  }
			}
		});
		}// fin de if
	}
</script>
<div align="right">
    <input type="button" class="volver" value="VOLVER">
</div>