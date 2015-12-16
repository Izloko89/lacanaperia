<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
$emp=$_SESSION["id_empresa"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT
		*
	FROM clientes
	WHERE clientes.id_empresa=$emp;";
	$res=$bd->query($sql);
	$articulos=array();
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
		$clientes[$d["id_cliente"]]=$d;
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
.guardar1{
	padding:10px;
}
</style>
<script src="js/formularios.js"></script>

<script>


$("#clave").on("change keyup paste", function(){
	//alert("entrando en el evento");
   //realizaProceso($('#clave').val());return false;
})

function realizaProceso(valorCaja2){
	/*
	valorCaja2 = document.getElementById("clave").value;

        var parametros = {

              

                "aidi" : valorCaja2

        };

		
		
        $.ajax({

                data:  parametros,

                url:   'select.php',

                type:  'post',

                beforeSend: function () {

                        $("#destino").html("Procesando, espere por favor...");

                },

                success:  function (response) {

                        $("#destino").html(response);

                }

        });*/

}

</script>

<script>



</script>

<script>
$(document).ready(function(e) {
    $(".nombre").focusout(function(e) {
		$(".razon").val($(this).val());
    });
	$( ".cliente_clave" ).keyup(function(e){
		_this=$(this);
		if(e.keyCode!=8 && _this.val()!=""){
			if(typeof timer=="undefined"){
				timer=setTimeout(function(){
					ClaveCliente();
					
				},300);
			}else{
				clearTimeout(timer);
				timer=setTimeout(function(){
					ClaveCliente();
					
				},300);
			}
		}else{
			resetform();
		}
    }); //termina buscador de cotizacion
	$(".dbc").dblclick(function(e) {
        accion=$(this).attr("data-action");
		val=$(this).text();
		try{
			$.ajax({
				url:"scripts/s_check_estado_cliente.php",
				cache:false,
				type:'POST',
				data:{
					'id_cliente': val
				},
				success: function(r){
					$("#estado_cuenta").html(r);
				}
			});
		}
		catch(r){
		}
	
		switch(accion){
			case 'clave':
				$(".clave").val(val);
				scrollTop();
				ClaveCliente();
				realizaProceso($('#clave').val());return false;
			break;
		}
    });
	$( ".nombre" ).autocomplete({
      source: "scripts/busca_clientes2.php",
      minLength: 1,
      select: function( event, ui ) {
		//da el nombre del formulario para buscarlo en el DOM
		form="cotizaciones";
		
		//asignacion individual alos campos
		$(".clave").val(ui.item.id_cliente);
		$( ".cliente_clave" ).keyup();
		try{
			$.ajax({
				url:"scripts/s_check_estado_cliente.php",
				cache:false,
				type:'POST',
				data:{
					'id_cliente': ui.item.id_cliente
				},
				success: function(r){
					$("#estado_cuenta").html(r);
				}
			});
		}
		catch(r){
		}
	  }
    });

	function requerido(seccion){
		selector=seccion + " .requerido";
		continuar=true;
		$.each($(selector),function(i,v){
			console.log(v);
			if($(this).val()==""){
				$(this).addClass("falta_llenar");
				continuar=false;
			}
		});
		return continuar;
	}

	$(".guardar1").click(function(e) {
		if(requerido('#f_clientes_fiscal')){
		var clave = $(".clave").val();
		var nombre = $(".nombre").val();
		var limitecredito = $(".limitecredito").val();
		var dNow = new Date();
		var fecha= dNow.getFullYear() + '-' + (dNow.getMonth()+1) + '-' + dNow.getDate() + ' ' + dNow.getHours() + ':' + dNow.getMinutes() + ':' + dNow.getSeconds();
		
		var direccion = $('.direccion').val();
		var colonia = $('.colonia').val();
		var ciudad = $('.ciudad').val();
		var estado = $('.estado').val();
		var cp = $('.cp').val();
		var telefono = $('.telefono').val();
		var celular = $('.celular').val();
		var mail = $('.email').val();
		
		var rfc = $('.rfc').val();
		var razon = $('.razon').val();
		var nombrecomercial = $('.nombrecomercial').val();
		var direccion_fiscal = $('.direccion_fiscal').val();
		var colonia_fiscal = $('.colonia_fiscal').val();
		var ciudad_fiscal = $('.ciudad_fiscal').val();
		var estado_fiscal = $('.estado_fiscal').val();
		var cp_fiscal = $('.cp_fiscal').val();
		
		//procesamiento de datos
		$.ajax({
			url:'scripts/s_guardar_cliente.php',
			cache:false,
			async:false,
			type:'POST',
			data:{
				'clave':clave,
				'nombre':nombre,
				'limitecredito':limitecredito,
				'fecha':fecha,
				'direccion':direccion,
				'colonia':colonia,
				'estado':estado,
				'ciudad':ciudad,
				'cp':cp,
				'telefono':telefono,
				'celular':celular,
				'mail':mail,
				'rfc':rfc,
				'razon':razon,
				'nombrecomercial':nombrecomercial,
				'direccion_fiscal':direccion_fiscal,
				'colonia_fiscal':colonia_fiscal,
				'ciudad_fiscal':ciudad_fiscal,
				'estado_fiscal':estado_fiscal,
				'cp_fiscal': cp_fiscal
			},
			success: function(r){
				if(r.continuar){
					alerta("info",'Cliente capturado exitosamente');
				}else{
					if(r.update){
						alerta("info",'Cliente actualizado exitosamente');
					}else{
						alerta("error",r.msg);
					}
				}
			},
			error: function(r){
				alerta('error', JSON.stringify(r));
			}
		});
	  }//if del requerido*/
    });
});
</script>
<?php 	

$sql="SELECT
		MAX(id_cliente ) as cliente
	FROM clientes
	WHERE clientes.id_empresa=$emp;";
	$res=$bd->query($sql);
	$wea=$res->fetchAll(PDO::FETCH_ASSOC);
?>
<form id="f_clientes" class="formularios">
  <h3 class="titulo_form">CLIENTE</h3>
  	<input type="hidden" name="id_cliente" id="id_cliente" class="id_cliente" value="<?php echo $wea[0]["cliente"] + 1;?>"/>
    <div class="campo_form">
    <label class="label_width">CLAVE</label>
    <input type="text" name="clave" id="clave" class="clave cliente_clave text_corto requerido mayuscula"
	value="<?php echo $wea[0]["cliente"] + 1;?>">
    </div>
    <div class="campo_form">
    <label class="label_width">Nombre de mama o papa</label>
    <input type="text" name="nombre" class="nombre text_largo nombre_buscar">
    </div>
    
    <input class="boton_dentro" type="reset" value="Limpiar" />
</form>

<table>
<tr>
<td>
<form id="f_clientes_contacto" class="formularios">
  <h3 class="titulo_form">DATOS DE CONTACTO</h3>
  <input type="hidden" name="id" class="id" />
  <input type="hidden" name="id_empresa" id="id_empresa" value="<?php echo $_SESSION["id_empresa"]; ?>" />

    <div class="campo_form">
        <label class="label_width">Dirección</label>
        <input type="text" name="direccion" class="direccion text_largo">
    </div>
    
   
    <div class="campo_form">
        <label class="label_width">Telefono</label>
        <input type="text" name="telefono" class="telefono">
    </div>
    <div class="campo_form">
        <label class="label_width">Celular</label>
        <input type="text" name="celular" class="celular">
    </div>
    <div class="campo_form">
        <label class="label_width">E-mail</label>
        <input type="text" name="email" class="email">
    </div>
</form>
</td>

	</tr>
	</table>
	
	
	
	
	
    <div align="right">
        <input type="button" class="guardar1" value="GUARDAR" data-wrap="#" data-accion="nuevo" data-m="pivote" />
        <input type="button" class="modificar" value="MODIFICAR" style="display:none;" />
    	<input type="button" class="volver" value="VOLVER">
    </div>
	
	<div class="formularios">
	<h3 class="titulo_form">Estado de Cuenta</h3>
	<div class="mostrar" id="estado_cuenta"></div>

<!--<input type="text" name="aidi" id="aidi" onchange="realizaProceso($('#aidi').val());return false;"></p>-->
<br>
<br>


<div id="destino" align="center"></div>
	
	
	</div>
		
</div>
<div class="formularios">
<h3 class="titulo_form">Listado de clientes registrados</h3>
	<table style="width:100%;">
    	<tr>
        	<th>CLAVE<br /><font style="font-size:0.4em; color:#999;">Doble Clic<br />para modificar</font></th>
            <th>NOMBRE</th>
        </tr>
        
    <?php if(count($clientes)>0){foreach($clientes as $art=>$d){
		echo '<tr>';
		echo '<td class="dbc" data-action="clave">'.$d["clave"].'</td>';
		echo '<td>'.$d["nombre"].'</td>';
		echo '</tr>';
	}//foreach
	}//if end ?>
    </table>
</div>
<script>
function ClaveCliente(){
	
	
	
	$(".id_cliente").val('');
	dato=$(".cliente_clave").val();
	input=$(".cliente_clave");
	input.addClass("ui-autocomplete-loading");
	$.ajax({
	  url:"scripts/busca_clientes1.php",
	  cache:false,
	  async:false,
	  data:{
		term:dato
	  },
	  success: function(r){
		clave=$(".cliente_clave").val();
		resetform();
		$(".cliente_clave").val(clave);
		$.each(r[0],function(i,v){
			$("."+i).text(v);
			$("."+i).val(v);
		});
		datosContacto(r[0].id_cliente,"clientes");
		datosFiscal(r[0].id_cliente,"clientes")
		//asigna el id de cotización
		input.removeClass("ui-autocomplete-loading");
		id = r[0].id_cliente;
		try{
			$.ajax({
				url:"scripts/s_check_estado_cliente.php",
				cache:false,
				type:'POST',
				data:{
					'id_cliente': id
				},
				success: function(r){
					$("#estado_cuenta").html(r);
				}
			});
			$.ajax({
				url:"scripts/buscaImagenes.php",
				cache:false,
				type:'POST',
				data:{
					'id': id
				},
				success: function(r){
					i = 0;
					r = JSON.parse(r);
					dmg = document.getElementById("img" + i);
					while(dmg)
					{
						dmg = document.getElementById("img" + i);
						document.getElementById("Imagenes").removeChild(dmg);
						i++;
						dmg = document.getElementById("img" + i)
					}
					i = 0;
					while(r[i].path)
					{
						var img = document.createElement('img');
						img.setAttribute("width", "100px");
						img.setAttribute("height", "100px");
						img.setAttribute("id", "img" + i);
						img.src = r[i].path;
						document.getElementById("Imagenes").appendChild(img);
						i++;
					}
				}
			});
		}
		catch(r){
		}
	  }
	});
	realizaProceso($('#clave').val());return false;
}
</script>