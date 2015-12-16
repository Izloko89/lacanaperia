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
</style>
<form id="f_tipo_evento" class="formularios">
  <h3 class="titulo_form">Tipo de evento</h3>
  	<input type="hidden" name="id_tipo" class="id_tipo" value="" />
    <div class="campo_form">
        <label class="label_width">Nombre de evento</label>
        <input type="text" name="nombre" id="nombre" class="nombre text_mediano">
    </div>
   	<div align="right">
        <input type="button" class="guardar_tipoevento guardar" value="GUARDAR"  />
        <input type="button" class="modificar" value="MODIFICAR" style="display:none;" />
        <input type="button" class="nueva" value="NUEVA" />
    </div>
    
</form>
<div align="right">
    <input type="button" class="volver" value="VOLVER">
</div>


<script>
$(".guardar_tipoevento").click(function(e) {
		
		nombre=$("#nombre").val();
		alert(nombre);
		if(nombre!=""){
			$.ajax({
				url:'scripts/s_agregar_tipoevento.php',
				cache:false,
				type:'POST',
				data:{
					'nombre':nombre
				
				},
				success: function(r){
					if(r.continuar){
						
						alerta("info",r.info);
					}else{
						alerta("error",r.info);
					}
				}
			});
		}else{
		  
		}
    });
	
	</script>