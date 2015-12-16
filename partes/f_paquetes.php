<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
$id_emp=$_SESSION["id_empresa"];

try{
	$areas="SELECT * FROM areas WHERE id_empresa=$id_emp;";
	$familias="SELECT * FROM familias WHERE id_empresa=$id_emp;";
	$subfamilias="SELECT * FROM subfamilias WHERE id_empresa=$id_emp;";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	
	$res=$bd->query($areas);
	$areas="<option>Áreas</option>";
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$areas.='<option value="'.$v["id_area"].'">'.$v["nombre"].'</option>';
	}
	$res=$bd->query($familias);
	$familias="<option>Familias</option>";
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$familias.='<option value="'.$v["id_familia"].'">'.$v["nombre"].'</option>';
	}
	$res=$bd->query($subfamilias);
	$subfamilias="<option>Subfamilias</option>";
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$subfamilias.='<option value="'.$v["id_subfamilia"].'">'.$v["nombre"].'</option>';
	}
	
	$sql="SELECT
		*
	FROM paquetes
	;";
	$res=$bd->query($sql);
	$cont=1;
	$paquetes=array();
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
		$paquetes[$d["id_paquete"]]=$d;
	}
	
	
	
	
}catch(PDOException $err){
	echo $err->getMessage();
}

?>
<style>
#articulos_tabla{
	margin:0 auto;
}
</style>

<style>

.eliminar_tevento{
	background:  url('img/cruz.png') center no-repeat;
	background-size:contain;
	cursor:pointer;
	width:20px;
	height:20px;
	display:inherit;
	text-align:center;
}
</style>

<script>
	function eliminar_art(elemento, id_item){
		$.ajax({
			url:'scripts/ePaquete.php',
			cache:false,
			type:'POST',
			data:{
				'id_item':id_item
			},
			success: function(r){
			  if(r){
				document.getElementById("tableEve").deleteRow(elemento);
				alerta("info","<strong>Paquete</strong> Eliminado");
				$(".volver").click();
			  }else{
				alerta("error", r);
			  }
			}
		});
	}
</script>

<script src="js/formularios.js"></script>
<script src="js/paquetes.js"></script>
<form id="f_paquetes" class="formularios">
<h3 class="titulo_form">PAQUETE</h3>
  <input type="hidden" name="id_paquete" class="id_paquete">
    <div class="campo_form">
        <label class="label_width">CLAVE</label>
        <input type="text" name="clave" class="requerido clave mayuscula text_corto" value="<?php nCvePaq(); ?>">
    </div>
    <div class="campo_form">
        <label class="label_width">NOMBRE</label>
        <input type="text" name="nombre" class="nombre requqerido text_largo">
    </div>
    <div class="campo_form">
        <label class="label_width">Descripción</label>
        <textarea name="descripcion" class="descripcion"></textarea>
    </div>
    <div class="campo_form">
        <label class="label_width">Unidades</label>
        <input type="text" name="unidades" class="unidades">
    </div>
  	<input type="hidden" name="id_item" class="id_item" />
  	<input type="hidden" name="id_empresa" class="id_empresa" value="<?php empresa(); ?>">
    <div class="campo_form">
        <label class="label_width">Precio de compra</label>
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
        <label class="label_width">Precio de otro concepto</label>
        <input type="text" name="precio3" class="precio3" />
    </div>
    <div class="campo_form">
        <label class="label_width">Precio de otro concepto</label>
        <input type="text" name="precio4" class="precio4" />

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
<center>
<div id="uploadPreview"></div>
<div id="infoMessage"></div>
</center>
  
<div id="articulos" class="formularios">
  <h3 class="titulo_form">Artículos en el paquete</h3>
  <div align="center">
  	  <input type="hidden" class="id_paquete" />
      <select class="areas" onchange="verArticulos(this);" data-zone="areas"><?php echo $areas; ?></select>
      <select class="familias" onchange="verArticulos(this);" data-zone="familias"><?php echo $familias; ?></select>
      <select class="subfamilias" onchange="verArticulos(this);" data-zone="subfamilias"><?php echo $subfamilias; ?></select>
      <select class="id_articulo"></select>
      <input type="text" class="cantidad numerico" size="10" placeholder="cantidad" />
      <input type="button" class="agregar_articulo" value="Agregar al paquete" />
  </div>
  <table id="articulos_tabla">
      <tr class="noborrar">
      	<th>Área</th>
        <th>Familia</th>
        <th>Subfamilia</th>
        <th width="250">Articulo</th>
        <th width="100">Unidades</th>
        <th width="100">Cantidad</th>
        <th width="100">Quitar</th>
      </tr>
  </table>
</div>

<div class="formularios">
<h3 class="titulo_form">Listado de Paquetes registrados</h3>
	<table style="width:100%;" id="tableEve">
		<tr>
			<th>CLAVE<br /><font style="font-size:0.4em; color:#999;">Doble Clic<br />para modificar</font></th>
			<th>PAQUETE</th>
			<th>ELIMINAR</th>
		</tr>

	<?php if(count($paquetes)>0){
		foreach($paquetes as $art=>$d){
		echo '<tr>';
		echo '<td class="dbc" data-action="clave">'.$d["id_paquete"].'</td>';
		echo '<td>'.$d["nombre"].'</td>';
		echo	'<td class="eliminar_tevento" style="text-align:center;" onclick="eliminar_art(' . $cont . ',' . $d["id_paquete"] . ')"></td>';
		echo '</tr>';
		$cont++;
	}//foreach
	}//if end ?>
	</table>
</div>






<script>
$(document).ready(function(e) {
	$(".numerico").numeric();
    $( ".clave" ).keyup(function(e){
		_this=$(this);
		if(e.keyCode!=8 && _this.val()!=""){
			if(typeof timer=="undefined"){
				timer=setTimeout(function(){
					buscarClave();
				},300);
			}else{
				clearTimeout(timer);
				timer=setTimeout(function(){
					buscarClave();
				},300);
			}
		}else{
			resetform();
		}
    }); //termina buscador de cotizacion
    $(".dbc").dblclick(function(e) {
    	console.log('evento');
		accion=$(this).attr("data-action");
		val=$(this).text();
		switch(accion){
			case 'clave':
				$(".clave").val(val);
				scrollTop();
				$('#fileToUpload').show();
				buscarClave();
			break;
		}
	});
	$(".agregar_articulo").click(function(e) {
		paq=$("#articulos .id_paquete").val();
		art=$(".id_articulo").val();
		cant=$(".cantidad").val();
		if( (art!="null" || art!="") && cant!="" && paq!="" ){
			$.ajax({
				url:'scripts/s_agregar_art_paq.php',
				cache:false,
				type:'POST',
				data:{
					'paq':paq,
					'art':art,
					'cant':cant
				},
				success: function(r){
					if(r.continuar){
						buscaArtPaq(paq);
						alerta("info",r.info);
					}else{
						alerta("error",r.info);
					}
				}
			});
		}else{
		   alert("Artículo no seleccionado o cantidad vacía");
		}
    });
});
function buscarClave(){
	$(".id_paquete").val('');
	dato=$(".clave").val();
	input=$(".clave");
	input.addClass("ui-autocomplete-loading");
	$.ajax({
	  url:"scripts/s_busca_paq_get.php",
	  cache:false,
	  data:{
		term:dato
	  },
	  success: function(r){
		clave=$(".clave").val();
		resetform();
		$(".clave").val(clave);
		$.each(r,function(i,v){
			$("."+i).empty().text(v);
			$("."+i).empty().val(v);
			if(i=='image'){
				if(v){
					image = v.replace(" ", '%20');
					$('#uploadPreview').empty().prepend('<img src=img/articulos/'+ image +'> <br>');
				}else{
					$('#uploadPreview').empty();
				}
			}
		});
		buscaArtPaq(r.id_paquete);
		//asigna el id de cotización
		input.removeClass("ui-autocomplete-loading");
	  }
	});
}

//función para ver los articulos seun el afs
function verArticulos(e){
	elem=$(e);
	$(".id_articulo").html('');
	zona=elem.attr("data-zone");
	id=elem.find("option:selected").val();
	$.ajax({
		url:'scripts/s_get_articulo.php',
		cache:false,
		type:'POST',
		data:{
			'zona':zona,
			'id':id
		},
		success: function(r){
			$(".id_articulo").html(r);
		}
	});
}

//function para buscar los articulos dentro del paquete
function buscaArtPaq(paq){
	$.ajax({
		url:'scripts/s_get_art_paq.php',
		cache:false,
		type:'POST',
		data:{
			'paq':paq,
		},
		success: function(r){
			//$("tr:not(.noborrar)").remove();
			$("#articulos_tabla").empty().append(r);
		}
	});
}
function eliminar(e){
	e=$(e);
	id=e.attr("data-row");
	mouseLoad(true);
	$.ajax({
		url:'scripts/s_quitar_art_paq.php',
		cache:false,
		type:'POST',
		data:{
			id:id
		},
		success: function(r){
			mouseLoad(false);
			if(r.continuar){
				alerta("info","Articulo removido exitosamente");
				e.parent().parent().remove();
			}else{
				alerta("error",r.info);
			}
		}
	});
}
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
	var compra = $('.compra').val();
	var precio1 = $('.precio1').val();
	var precio2 = $('.precio2').val();
	var precio3 = $('.precio3').val();
	var precio4 = $('.precio4').val();
	var emp = <?php echo $id_emp ?>;
	if (clave && compra && precio1){
		if(!($('#fileToUpload')[0].value)){
			console.log('Guardando sin imagen');
			$.get( "scripts/addPaquetesImage.php",{ 
	  			'clave':clave,
	  			'nombre':nombre,
	  			'desc':desc,
	  			'unidad':unidad,
	  			 'emp':emp,
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
	  		$.get( "scripts/addPaquetesImage.php",{ 
	  			'clave':clave,
	  			'nombre':nombre,
	  			'desc':desc,
	  			'unidad':unidad,
	  			 'image':response.imagen,
	  			 'emp':emp,
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
