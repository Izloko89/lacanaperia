<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
?>
<style>
#f_tipo_evento .guardar_individual{
	position:relative;
}
#f_tipo_evento .modificar{
	position:relative;
}
.areas{
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
<h3 class="titulo_form">Galeria</h3>
    <div class="campo_form">
	<form id="addImage" class="formularios" action="scripts/upload.php" method="post" enctype="multipart/form-data">
    <label class="label_width">Seleccionar imagen :</label>
    <input type="file" name="fileToUpload[]" id="fileToUpload" multiple>
    <input type="submit" value="Subir Imagen" id="addImageButton" name="submit">
	<div id="uploadPreview"></div>
	<div id="infoMessage"></div>
    </div>

    
</form>
<div align="right">
	<input type="button" class="volver" value="VOLVER">
</div>

<table id="tableEve">
<tr><td><h2>Lista de Galeria</h2></td></tr>

</table>



<div align="right">
    <input type="button" class="volver" value="VOLVER">
</div>

<script>
// var url = window.URL || window.webkitURL; // alternate use
function readImage(file) {

    var reader = new FileReader();
    var image  = new Image();

    reader.readAsDataURL(file);  
    reader.onload = function(_file) {
        image.src    = _file.target.result;              // url.createObjectURL(file);
        image.onload = function() {
            var w = this.width,
                h = this.height,
                t = file.type,                           // ext only: // file.type.split('/')[1],
                n = file.name,
                s = ~~(file.size/1024) +'KB';
            if(w <= 250 && h <= 250){
            	$('#uploadPreview').prepend('<br><img src="'+ this.src +'"> '+w+'x'+h+' '+s+' '+t+' '+n+'<br>');
            	$('#addImageButton').show();
            }else{
            	$('#uploadPreview').prepend('<br>Error al cargar: '+w+'x'+h+' '+s+' '+t+' '+n+'<br> Eliga una imagen no mayor a 250x250<br>');
            	
            }
            
        };
        image.onerror= function() {
            alert('Formato de imagen incorrecto: '+ file.type);
        };      
    };

}

$("#fileToUpload").change(function (e) {
    var imgPath = $(this)[0].value;
    var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
    if (extn == "png" || extn == "jpg" || extn == "jpeg") {
    	if(this.disabled) return alert('Archivo no soportado');
    	var F = this.files;
    	if(F && F[0]) for(var i=0; i<F.length; i++) readImage( F[i] );
	}else{
		$('#uploadPreview').empty().prepend('<br> Los formatos validos son: jpg, jpeg y png');
        
	}
});

// $('#addImageButton').click(function(){
// $('#addImage').ajaxForm({
// 	dataType: 'json',
// 	success: function(response) {
// 	$('#infoMessage').empty().prepend(response.info + response.status);
// 	$.get( "scripts/addImageQuery.php",{ 'image':response.imagen })
// 		.done(function(data) {
// 		console.log(data);
// 		if(data.continuar){
// 			alerta("info","Se agrego la imagen para este articulo");
// 		}else{
// 			alerta("error", "Hubo un error guarde primero el articulo");
// 		}
// 	});
// 	} 
// 	});
// });

</script>
<script src="http://malsup.github.com/jquery.form.js"></script>