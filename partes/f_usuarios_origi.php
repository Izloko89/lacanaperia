<?php session_start();
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
$emp=$_SESSION["id_empresa"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT
		*
	FROM usuarios
	WHERE id_empresa=$emp;";
	$res=$bd->query($sql);
	$usuarios=array();
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
		$usuarios[$d["id_usuario"]]=$d;
	}
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
}

?>
<script src="js/formularios.js"></script>
<script>
$(document).ready(function(e) {
	$(".nombre").focusout(function(e) {
		$(".razon").val($(this).val());
	});
	$("form").submit(function(e) {
		e.preventDefault();
	});
	$( ".usuario" ).keyup(function(e){
		_this=$(this);
		//e.keyCode!=8 && _this.val()!=""
		if(e.keyCode==13){
			if(typeof timer=="undefined"){
				timer=setTimeout(function(){
					usuario();
				},300);
			}else{
				clearTimeout(timer);
				timer=setTimeout(function(){
					usuario();
				},300);
			}
		}else if(e.keyCode==8 && _this.val()==""){
			resetform();
		}
	}); //termina buscador de cotizacion
	$(".dbc").dblclick(function(e) {
		accion=$(this).attr("data-action");
		val=$(this).text();
		switch(accion){
			case 'clave':
				$(".usuario").val(val);
				scrollTop();
				usuario();
			break;
		}
	});
	$( ".nombre_buscar" ).autocomplete({
		source: "scripts/busca_usuarios.php",
		minLength: 2,
		select: function( event, ui ) {
		//muestra el botón modificar
		$(".modificar").show();
		$(".guardar").hide();

		//da el nombre del formulario para buscarlo en el DOM
		form=ui.item.form;

		//asigna el valor en el campo
		$.each(ui.item,function(i,v){
			selector=form+" ."+i
			$(selector).val(v);
		});
		datosContacto(ui.item.id_cliente,'clientes');
		datosFiscal(ui.item.id_cliente,'clientes');
	  }
	});
	$(".mostrar").click(function(e) {
		ref=$(this).attr("data-c");
		$("."+ref).toggle();
	});
});
</script>
<form id="f_usuarios" class="formularios">
	<h3 class="titulo_form">USUARIO</h3>
		<input type="hidden" name="id_usuario" class="id_usuario" />
	<div class="campo_form">
	<label class="label_width">Usuario</label>
	<input type="text" name="usuario" class="usuario text_mediano requerido" value="">
	</div>
	<div class="campo_form">
	<label class="label_width">Nombre</label>
	<input type="text" name="nombre" class="nombre text_largo nombre_buscar">
	</div>
	<div class="campo_form">
	<label class="label_width">Contraseña</label>
	<input type="text" name="password" class="password text_corto">
	</div>
	<input class="boton_dentro" type="reset" value="Limpiar" />
</form>
<form id="f_usuarios_contacto" class="formularios">
	<h3 class="titulo_form">INFORMACIÓN DEL USUARIO <input type="button" class="mostrar" data-c="wrap_hide_1" value="Mostrar/Ocultar" /></h3>
<div class="wrap_hide_1" style="display:none;">
	<input type="hidden" name="id_usuario" class="id_usuario" />
	<input type="hidden" name="id_empresa" value="<?php echo $_SESSION["id_empresa"]; ?>" />
	<div class="campo_form">
		<label class="label_width">CLAVE</label>
		<input type="text" name="clave"  id="clave" class="requerido mayuscula clave">
	</div>
	<div class="campo_form">
		<label class="label_width">Dirección</label>
		<input type="text" name="direccion" id="direccion" class="direccion">
	</div>
	<div class="campo_form">
		<label class="label_width">Colonia</label>
		<input type="text" name="colonia" id="colonia" class="colonia">
	</div>
	<div class="campo_form">
		<label class="label_width">Ciudad</label>
		<input type="text" name="ciudad" id="ciudad" class="ciudad">
	</div>
	<div class="campo_form">
		<label class="label_width">Estado</label>
		<input type="text" name="estado" id="estado" class="estado">
	</div>
	<div class="campo_form">
		<label class="label_width">Código Postal</label>
		<input type="text" name="cp"  id="cp" class="cp">
	</div>
	<div class="campo_form">
		<label class="label_width">Telefono</label>
		<input type="text" name="telefono" id="telefono" class="telefono">
	</div>
	<div class="campo_form">
		<label class="label_width">Celular</label>
		<input type="text" name="celular" id="celular" class="celular">
	</div>
	<div class="campo_form">
		<label class="label_width">E-mail</label>
		<input type="text" name="email" id="email" class="email">
	</div>
</div>
</form>
<form id="f_usuarios_permisos" class="formularios">
	<h3 class="titulo_form">PERMISOS</h3>
	<input type="hidden" class="id_permiso" name="id_permiso" />
		<input type="hidden" class="id_usuario" name="id_usuario" />
	<div class="formularios" style="border:0;">
		<h3 class="titulo_form"><input type="checkbox" id="cot" name="cot" value="1" /> - Cotizaciones</h3>
	</div>
	<div class="formularios" style="border:0;">
		<h3 class="titulo_form"><input type="checkbox" id="eve" name="eve" value="1" /> - Eventos</h3>
	</div>
	<div class="formularios" style="border:0;">
		<h3 class="titulo_form"><input type="checkbox" id="alm" name="alm" value="1" /> - Almacén</h3>
	</div>
	<div class="formularios" style="border:0;">
		<h3 class="titulo_form"><input type="checkbox" id="com" name="com" value="1" /> - Compras</h3>
	</div>
	<div class="formularios" style="border:0;">
		<h3 class="titulo_form"><input type="checkbox" id="ban" name="ban" value="1" /> - Bancos</h3>
	</div>
	<div class="formularios" style="border:0;">
		<h3 class="titulo_form"><input type="checkbox" id="modu" name="modu" value="1" /> - Módulos</h3>
	</div>
</form>
	<div align="right">
		<input type="button" class="guardar" value="GUARDAR"  />
		<input type="button" class="modificar" value="MODIFICAR" style="display:none;" />
		<input type="button" class="volver" value="VOLVER">
	</div>
<div class="formularios">
<h3 class="titulo_form">Listado de usuarios registrados</h3>
	<table style="width:100%;">
		<tr>
			<th>USUARIO<br /><font style="font-size:0.4em; color:#999;">Doble Clic<br />para modificar</font></th>
			<th>NOMBRE</th>
			<th>CATEGORÍA</th>
		</tr>

	<?php if(count($usuarios)>0){foreach($usuarios as $art=>$d){
		echo '<tr>';
		echo '<td class="dbc" data-action="clave">'.$d["usuario"].'</td>';
		echo '<td>'.$d["nombre"].'</td>';
		echo '<td>'.$d["categoria"].'</td>';
		echo '</tr>';
	}//foreach
	}//if end ?>
	</table>
</div>
<script>
function usuario(){
	$(".id_usuario").val('');
	dato=$(".usuario").val();
	input=$(".usuario");
	input.addClass("ui-autocomplete-loading");
	$.ajax({
	  url:"scripts/busca_usuarios.php",
	  cache:false,
	  async:false,
	  data:{
		term:dato
	  },
	  success: function(r){
		clave=$(".usuario").val();
		resetform();
		$(".usuario").val(clave);
		$.each(r[0],function(i,v){
			$("."+i).text(v);
			$("."+i).val(v);
		});
		datosContacto(r[0].id_usuario,"usuarios");
		permisos();
		//asigna el id de cotización
		input.removeClass("ui-autocomplete-loading");
	  }
	});
}


function datosContacto()
{
	
	
	
	id_usuario=$(".id_usuario").val();
	$.ajax({
	  url:"scripts/s_usuarios_contacto.php",
	  cache:false,
	  async:false,
	  type:'POST',
	  data:{
		id_usuario:id_usuario
	  },
	  success: function(r){
		  if(r.continuar){
		//	  alert(r.datos[1]);
			  
			$.each(r.datos,function(i,v){
				
			
				//alert(i);
				//alert(v);
				
				if(i=="clave")
				{
					document.getElementById("clave").value= v;
				}
				if(i=="direccion")
				{
					document.getElementById("direccion").value=v;
				}
				if(i=="colonia")
				{
					document.getElementById("colonia").value=v;
				
				}
				if(i=="ciudad")
				{
					document.getElementById("ciudad").value=v;
				}
				if(i=="estado")
				{
					document.getElementById("estado").value=v;
				}
				if(i=="cp")
				{
					document.getElementById("cp").value=v;
				}
				if(i=="telefono")
				{
					document.getElementById("telefono").value=v;
				}
				if(i=="celular")
				{
					document.getElementById("celular").value=v;
				}
				if(i=="email")
				{
					document.getElementById("email").value=v;
				}
				
				
				
				
				
				
				
				
				
			});
		  }else{
			  alerta("info",r.info);
		  }
	  }
	});
	
	
	
}


function permisos(){
	document.getElementById("cot").checked=0;
	document.getElementById("eve").checked=0;
	document.getElementById("alm").checked=0;
	document.getElementById("com").checked=0;
	document.getElementById("ban").checked=0;
	document.getElementById("modu").checked=0;
	
	
	$(".id_permiso").val('');
	id_usuario=$(".id_usuario").val();
	$.ajax({
	  url:"scripts/s_usuarios_permisos.php",
	  cache:false,
	  async:false,
	  type:'POST',
	  data:{
		id_usuario:id_usuario
	  },
	  success: function(r){
		  if(r.continuar){
		//	  alert(r.datos[1]);
			  
			$.each(r.datos,function(i,v){
				v=v*1; //para segurarnos que tiene valor numérico
				if(i=="id_permiso"){
					
					$("id_permiso").val(v);
				}
				//alert(i);
				//alert(v);
				if(v==1 && i == "cotizacion"){
					document.getElementById("cot").checked=1;
					//$(".cot").prop("checked",true);
				}
				if(v==1 && i == "evento"){
					document.getElementById("eve").checked=1;
				}
				if(v==1 && i == "almacen"){
				document.getElementById("alm").checked=1;
				}
				if(v==1 && i == "compras"){
				document.getElementById("com").checked=1;
				}
				if(v==1 && i== "bancos"){
					document.getElementById("ban").checked=1;
				}
				if(v==1 && i == "modulos"){
				document.getElementById("modu").checked=1;
				}
			});
		  }else{
			  alerta("info",r.info);
		  }
	  }
	});
}
</script>

<script>
$(".guardar").click(function(e) {
		usuario=$(".usuario").val();
		nombre=$(".nombre").val();
		password=$(".password").val();
		
		
		//son los datos de la tabla usuario_contacto
		clave=$(".clave").val();
		direccion=$(".direccion").val();
		colonia=$(".colonia").val();
		ciudad=$(".ciudad").val();
		estado=$(".estado").val();
		cp=$(".cp").val();
		telefono=$(".telefono").val();
		celular=$(".celular").val();
		email=$(".email").val();
		
		//datos de tabla usuario_permisos
		
		control = document.getElementById("cot").checked;
		alert(control);
		if(control == true)
		{
			cotizacion = 1;
		}
		control = document.getElementById("eve").checked;
		
		if(control == true)
		{
			evento = 1;
		}
		control = document.getElementById("alm").checked;
		if(control == true)
		{
			almacen = 1;
		}
		control = document.getElementById("com").checked;
		if(control == true)
		{
			compras = 1;
		}
		control = document.getElementById("ban").checked;
		if(control == true)
		{
			bancos = 1;
		}
		control = document.getElementById("modu").checked;
		if(control == true)
		{
			modulos = 1;
		}
		
		
		if( usuario !=""){
			$.ajax({
				url:'scripts/s_agregar_usuario.php',
				cache:false,
				type:'POST',
				data:{
					'usuario':usuario,
					'nombre':nombre,
					'password':password,
					
					
					'clave':clave,
					'direccion':direccion,
					'colonia':colonia,
					'ciudad':ciudad,
					'estado':estado,
					'cp':cp,
					'telefono':telefono,
					'celular':celular,
					'email':email,
					
					'cotizacion':cotizacion,
					'evento':evento,
					'almacen':almacen,
					'compras':compras,
					'bancos':bancos,
					'modulos':modulos
				
					
					
				
				},
				success: function(r){
					if(r.continuar){
						
						alerta("info",r.info);
					}else{
							alerta("demo",r.demo);
						alerta("error",r.info);
					}
				}
				
				
				
				
			});
			
			
			
			
			
			
		}else{
		   alert("Artículo no seleccionado o cantidad vacía");
		}
    });
	
	</script>