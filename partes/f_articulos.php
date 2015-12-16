<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
$emp=$_SESSION["id_empresa"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT
		articulos.*,
		listado_precios.*,
		areas.nombre as area,
		familias.nombre as familia,
		subfamilias.nombre as subfamilia
	FROM articulos
	LEFT JOIN listado_precios ON articulos.id_articulo=listado_precios.id_articulo
	LEFT JOIN areas ON articulos.area=areas.id_area
	LEFT JOIN familias ON articulos.familia=familias.id_familia
	LEFT JOIN subfamilias ON articulos.subfamilia=subfamilias.id_subfamilia
	WHERE articulos.id_empresa=$emp;";
	$res=$bd->query($sql);
	$articulos=array();
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
		$articulos[$d["id_articulo"]]=$d;
	}
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
}
?>
<style>
.dbc{
	cursor:pointer;
	color:#900;
}
.ui-autocomplete-loading {
	background: white url('img/load.gif') right center no-repeat;
}
</style>
<script src="js/formularios.js"></script>
<form id="f_articulos" class="formularios">
<h3 class="titulo_form">ARTÍCULO</h3>
  <input type="hidden" name="id_articulo" class="id_articulo">
    <div class="campo_form">
        <label class="label_width">CLAVE</label>
        <input type="text" name="clave" class="requerido clave mayuscula" value="">
    </div>
    <div class="campo_form">
        <label class="label_width">NOMBRE</label>
        <input type="text" name="nombre" class="requqerido nombre">
    </div>
    <div class="campo_form">
        <label class="label_width">Descripción</label>
        <input type="text" name="descripcion" class="descripcion" style="width:400px;" >
    </div>
    <div class="campo_form">
        <label class="label_width">Unidades</label>
        <input type="text" name="unidades" class="unidades">
    </div>
    <div class="campo_form">
    	<label class="label_width">Pertenece a:</label>
        <select name="area" class="area"><option selected="selected" value="">Área</option><?php afs("area"); ?></select>
        <select name="familia" class="familia"><option selected="selected" value="">Familia</option><?php afs("familia"); ?></select>
        <select name="subfamilia" class="subfamilia"><option selected="selected" value="">SubFamilia</option><?php afs("subfamilia"); ?></select>

     <input type="hidden" name="id_item" class="id_item" />
  <input type="hidden" name="id_empresa" class="id_empresa" value="<?php empresa(); ?>">
    <div class="campo_form">
        <label class="label_width">Precio de recuperación</label>
        <input type="text" name="compra" class="compra requerido" />
    </div>
    <div class="campo_form">
        <label class="label_width">Precio de lista</label>
        <input type="text" name="precio1" class="precio1 requerido" />
    </div>
    <div class="campo_form">
        <label class="label_width">Precio de mayoreo</label>
        <input type="text" name="precio2" class="precio2" />
    </div>
    <div class="campo_form">
        <label class="label_width">Precio productor</label>
        <input type="text" name="precio3" class="precio3" />
    </div>
    <div class="campo_form">
        <label class="label_width">Precio de patrocinio</label>
        <input type="text" name="precio4" class="precio4" />
    </div>     
    
</form>
<form id="addImage" action="scripts/upload.php" method="post" enctype="multipart/form-data">
    <label class="label_width">Seleccionar imagen :</label>
    <input type="file" name="fileToUpload" id="fileToUpload">
    <div align="right">
		<input type="submit" value="Guardar" id="addImageButton" name="submit">
    	<input type="button" class="volver" value="VOLVER">
	</div>
</form>


</div>



</form>
<center>
<div id="uploadPreview"></div>
<div id="infoMessage"></div>
</center>
</div>

<div class="formularios">
<h3 class="titulo_form">Listado de artículos registrados</h3>
	<table style="width:100%;">
    	<tr>
        	<th>CLAVE<br /><font style="font-size:0.4em; color:#999;">Doble Clic<br />para modificar</font></th>
            <th>NOMBRE</th>
            <th>ÁREA</th>
            <th>FAMILIA</th>
            <th>SUBFAMILIA</th>
            <th>ELIMINAR</th>
        </tr>
        
    <?php if(count($articulos)>0){foreach($articulos as $art=>$d){
		echo '<tr>';
		echo '<td class="dbc" data-action="clave">'.$d["clave"].'</td>';
		echo '<td>'.$d["nombre"].'</td>';
		echo '<td>'.$d["area"].'</td>';
		echo '<td>'.$d["familia"].'</td>';
		echo '<td>'.$d["subfamilia"].'</td>';
		echo '<td><img src="../img/cruz.png" height="20" onclick="eliminar('.$art.');" /></td>';
		echo '</tr>';
	}//foreach
	}//if end ?>
    </table>
</div>
<script>
	function eliminar(id_item){
		$.ajax({
			url:'scripts/eArticulo.php',
			cache:false,
			type:'POST',
			data:{
				'id_item':id_item
			},
			success: function(r){
			  if(r){
			//	document.getElementById("tableEve").deleteRow(elemento);
				alerta("info","<strong>Articulo</strong> Eliminado");
			  }else{
				alerta("error", r);
			  }
			}
		});
	}
</script>
<script>
$(document).ready(function(e) {
    $( ".clave" ).keyup(function(e){
		_this=$(this);
		continuar=true;
		if(_this.val()==""){
			continuar=false;
			resetform();
		}
		if(e.keyCode<37 && e.keyCode>40){
			continuar=false;
		}
		if(continuar){
			if(typeof timer=="undefined"){
				timer=setTimeout(function(){
					buscarClaveArt()
				},300);
			}else{
				clearTimeout(timer);
				timer=setTimeout(function(){
					buscarClaveArt();
				},300);
			}
		}
    }); //termina buscador de cotizacion
	$(".dbc").dblclick(function(e) {
        accion=$(this).attr("data-action");
		val=$(this).text();
		switch(accion){
			case 'clave':
				$(".clave").val(val);
				scrollTop();
				$('#fileToUpload').show();
				buscarClaveArt();
			break;
		}
    });
});
function buscarClaveArt(){
	dato=$(".clave").val();
	input=$(".clave");
	var image;
	input.addClass("ui-autocomplete-loading");
	$.ajax({
	  url:"scripts/s_busca_art_get.php",
	  cache:false,
	  data:{
		term:dato
	  },
	  success: function(r){
		clave=$(".clave").val();
		resetform();
		$(".clave").val(clave);
		$.each(r,function(i,v){
			$("."+i).val(v);
			if(i=='image'){
				if(v){
					image = v.replace(" ", '%20');
					$('#uploadPreview').empty().prepend('<img src=img/articulos/'+ image +'> <br>');
				}else{
					$('#uploadPreview').empty();
				}
			}
		});
		//asigna el id de cotización
		input.removeClass("ui-autocomplete-loading");
	  }
	});
}

//resetar todos los forms
function resetform(){
	cve=$(".clave").val();
	$.each($("body").find("form"),function(i,v){
		this.reset();
	});
	$(".clave").val(cve);
}


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
            	$('#uploadPreview').empty().prepend('<img src="'+ this.src +'"> '+w+'x'+h+' '+s+' '+t+' '+n+'<br>');

            }else{
            	$('#uploadPreview').empty().prepend('<br> Eliga una imagen no mayor a 250x250');

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

$('#addImageButton').click(function(){
	var clave = $('.clave').val();
	var nombre = $('.nombre').val();
	var desc = $('.descripcion').val();
	var unidad = $('.unidades').val();
	var area = $('.area :selected').val();
	var familia = $('.familia :selected').val();
	var subfamilia = $('.subfamilia :selected').val();
	var compra = $('.compra').val();
	var precio1 = $('.precio1').val();
	var precio2 = $('.precio2').val();
	var precio3 = $('.precio3').val();
	var precio4 = $('.precio4').val();
	var emp = <?php echo $emp ?>;
	if (clave && compra && precio1){
		if(!($('#fileToUpload')[0].value)){
			console.log('Guardando sin imagen');
			$.get( "scripts/addImageQuery.php",{ 
	  			'clave':clave,
	  			'nombre':nombre,
	  			'desc':desc,
	  			'unidad':unidad,
	  			 'emp':emp,
	  			 'area': area,
	  			 'familia' : familia,
	  			 'subfamilia':subfamilia,
	  			 'precio1':precio1,
	  			 'precio2':precio2,
	  			 'precio3':precio3,
	  			 'precio4':precio4,
	  			 'compra':compra
	  			 }).done(function(data) {
  					if(data.continuar){
  						alerta("info","Se agrego el articulo correctamente");
  						resetform();
  					}else{
  						alerta("error", "Hubo un error al guardar el articulo");
  						}
					});
  			return false;
	 	}
	 	console.log('Subiendo imagen..');
		$('#addImage').ajaxForm({
			dataType: 'json',
	 	success: function(response) {
	 		console.log(response);
	  		$('#infoMessage').empty().prepend(response.info + response.status);
	  		$.get( "scripts/addImageQuery.php",{ 
	  			'clave':clave,
	  			'nombre':nombre,
	  			'desc':desc,
	  			'unidad':unidad,
	  			 'image':response.imagen,
	  			 'emp':emp,
	  			 'area': area,
	  			 'familia' : familia,
	  			 'subfamilia':subfamilia,
	  			 'precio1':precio1,
	  			 'precio2':precio2,
	  			 'precio3':precio3,
	  			 'precio4':precio4,
	  			 'compra':compra
	  			 }).done(function(data) {
	  				console.log(data);
  					if(data.continuar){
  						alerta("info","Se agrego la imagen para este articulo");
  						resetform();
  					}else{
  						alerta("error", "Hubo un error guarde primero el articulo");
  						}
					});
	 		}
		});
	}else{
		$('.clave').addClass('falta_llenar');
		$('.compra').addClass('falta_llenar');
		$('.precio1').addClass('falta_llenar');
		return false;
	}
});

</script>
<script src="http://malsup.github.com/jquery.form.js"></script>