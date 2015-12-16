<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
$emp=$_SESSION["id_empresa"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT
		*
	FROM empleados
	;";
	$articulos=array();
	$res=$bd->query($sql);
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
		$clientes[$d["id_empleado"]]=$d;
		
		$sql="SELECT MAX(id_empleado) as id FROM empleados";
	$res=$bd->query($sql);
	$res=$res->fetchAll(PDO::FETCH_ASSOC);
	$aidi=$res[0]["id"]+1;
		
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
</style>
<style>
.dbc{
	cursor:pointer;
	color:#900;
}
.ui-autocomplete-loading {
	background: white url('img/load.gif') right center no-repeat;
}
</style>

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
		dato=$(this).attr('id');
		$(".guardar").css({ "display": 'none'});
		$(".modificar").css({ "display": 'inline'});
		$.ajax({
	  url:"scripts/busca_empleado1.php",
	  cache:false,
	  async:false,
	  data:{
		term:dato
	  },
	  success: function(r){
	 
	nombre = document.getElementById("nombre").value=r.nombre;
		clave = document.getElementById("clave").value=r.id_empleado;
		puesto = document.getElementById("puesto").value=r.puesto;
		
		pcompra= document.getElementById("pcompra").value=r.pcompra;
		pventa= document.getElementById("pventa").value=r.pventa;
		
	direccion = document.getElementById("direccion").value=r.direccion;
		colonia = document.getElementById("colonia").value=r.colonia;
		ciudad = document.getElementById("ciudad").value=r.ciudad;
		estado = document.getElementById("estado").value=r.estado;
		cp = document.getElementById("cp").value=r.cp;
		telefono = document.getElementById("telefono").value=r.telefono;
		celular = document.getElementById("celular").value=r.celular;
		email = document.getElementById("email").value=r.email;
		
		
		
		if(r.l==1)
		{
		document.getElementById("lun").checked=true;
		}else{document.getElementById("lun").checked=false;}
		
		if(r.m==1)
		{
		document.getElementById("mar").checked=true;
		}else{document.getElementById("mar").checked=false;}
		if(r.mi==1)
		{
		document.getElementById("mie").checked=true;
		}else{document.getElementById("mie").checked=false;}
		if(r.j==1)
		{
		document.getElementById("jue").checked=true;
		}else{document.getElementById("jue").checked=false;}
		if(r.v==1)
		{
		document.getElementById("vie").checked=true;
		}else{document.getElementById("vie").checked=false;}
		if(r.s==1)
		{
		document.getElementById("sab").checked=true;
		}else{document.getElementById("sab").checked=false;}
		if(r.d==1)
		{
		document.getElementById("dom").checked=true;
		}else{document.getElementById("dom").checked=false;}
		
	//	rfcf = document.getElementById("rfcf").value=r.rfc;
	//	direccionf = document.getElementById("direccionf").value=r.direccionf;
	//	coloniaf = document.getElementById("coloniaf").value=r.coloniaf;
	//	ciudadf = document.getElementById("ciudadf").value=r.ciudadf;
	//	estadof = document.getElementById("estadof").value=r.estadof;
	//	cpf= document.getElementById("cpf").value=r.cpf;	
		
		
		
	  }
	 
	});
		
    });
	
	
	
	$(".guardar").click(function(e) {
	
	
	
	
		nombre = document.getElementById("nombre").value;
		clave = document.getElementById("clave").value;
		puesto = document.getElementById("puesto").value;
		
		pcompra= document.getElementById("pcompra").value;
		pventa= document.getElementById("pventa").value;
		
	direccion = document.getElementById("direccion").value;
		colonia = document.getElementById("colonia").value;
		ciudad = document.getElementById("ciudad").value;
		estado = document.getElementById("estado").value;
		cp = document.getElementById("cp").value;
		telefono = document.getElementById("telefono").value;
		celular = document.getElementById("celular").value;
		email = document.getElementById("email").value;
		
		
		var l=0;
		var m=0;
		var mi=0;
		var j=0;
		var v=0;
		var s=0;
		var d=0;
		
		
	
  
	if (document.getElementById('lun').checked)
  {
   l=1;
 
  }
  
	if (document.getElementById('mar').checked)
  {
   m=1;
   
  }
  
	if (document.getElementById('mie').checked)
  {
   mi=1;
  
  }
  
	if (document.getElementById('jue').checked)
  {
   j=1;
   
  }
  
	if (document.getElementById('vie').checked)
  {
   v=1;
   
  }
  
	if (document.getElementById('sab').checked)
  {
   s=1;
    
  }
  
	if (document.getElementById('dom').checked)
  {
   d=1;
   
  }
		
		$.ajax({
			url:'scripts/s_guardar_empleado.php',
			cache:false,
			async:false,
			type:'POST',
			data:{
				'nombre':nombre,
				'clave':clave,
				'puesto':puesto,
				'pcompra':pcompra,
				'pventa':pventa,
				'direccion':direccion,
				'colonia':colonia,
				'ciudad':ciudad,
				'estado':estado,
				'cp':cp,
				'telefono':telefono,
				'celular':celular,
				'email':email,
				'l':l,
				'm':m,
				'mi':mi,
				'j':j,
				'v':v,
				's':s,
				'd':d
		//		'rfcf':rfcf,
		//		'direccionf':direccionf,
		//		'coloniaf':coloniaf,
		//		'ciudadf':ciudadf,
		//		'estadof':estadof,
		//		'cpf':cpf
			},
			success: function(r){		
				if(r.continuar){
					ingresar=true;
					alerta("info","Se agrego correctamente");
					$("#formularios_modulo").hide("slide",{direction:'right'},rapidez,function(){
					$("#botones_modulo").fadeIn(rapidez); });			
				}else{
					alerta("error",r.info);
				}
			}
		});
    });
    $(".volver").click(function(e) {
		ingresar=true;
    	$("#formularios_modulo").hide("slide",{direction:'right'},rapidez,function(){
			$("#botones_modulo").fadeIn(rapidez);
		});
    });
});



//nueva seccion de codigo de prueba //


	$(".modificar").click(function(e) {
		nombre = document.getElementById("nombre").value;
		clave = document.getElementById("clave").value;
		puesto = document.getElementById("puesto").value;
		
		pcompra= document.getElementById("pcompra").value;
		pventa= document.getElementById("pventa").value;
		
	direccion = document.getElementById("direccion").value;
		colonia = document.getElementById("colonia").value;
		ciudad = document.getElementById("ciudad").value;
		estado = document.getElementById("estado").value;
		cp = document.getElementById("cp").value;
		telefono = document.getElementById("telefono").value;
		celular = document.getElementById("celular").value;
		email = document.getElementById("email").value;
		
		
			
		var l=0;
		var m=0;
		var mi=0;
		var j=0;
		var v=0;
		var s=0;
		var d=0;
		
		
	
  
	if (document.getElementById('lun').checked)
  {
   l=1;
 
  }
  
	if (document.getElementById('mar').checked)
  {
   m=1;
   
  }
  
	if (document.getElementById('mie').checked)
  {
   mi=1;
  
  }
  
	if (document.getElementById('jue').checked)
  {
   j=1;
   
  }
  
	if (document.getElementById('vie').checked)
  {
   v=1;
   
  }
  
	if (document.getElementById('sab').checked)
  {
   s=1;
    
  }
  if (document.getElementById('dom').checked)
  {
   d=1;
    
  }
		
		
		
		
	//	rfcf = document.getElementById("rfcf").value;
	//	direccionf = document.getElementById("direccionf").value;
	//	coloniaf = document.getElementById("coloniaf").value;
	//	ciudadf = document.getElementById("ciudadf").value;
	//	estadof = document.getElementById("estadof").value;
	//	cpf= document.getElementById("cpf").value;
		//procesamiento de datos
		$.ajax({
			url:'scripts/s_modificar_empleado.php',
			cache:false,
			async:false,
			type:'POST',
			data:{
				'nombre':nombre,
				'clave':clave,
				'puesto':puesto,
				'direccion':direccion,
				'colonia':colonia,
				'ciudad':ciudad,
				'estado':estado,
				'cp':cp,
				'telefono':telefono,
				'celular':celular,
				'email':email,
				'pcompra':pcompra,
				'pventa':pventa,
				'l':l,
				'm':m,
				'mi':mi,
				'j':j,
				'v':v,
				's':s,
				'd':d
		//		'rfcf':rfcf,
		//		'direccionf':direccionf,
		//		'coloniaf':coloniaf,
		//		'ciudadf':ciudadf,
		//		'estadof':estadof,
		//		'cpf':cpf
			},
			success: function(r){
				
				if(r.continuar){
					ingresar=true;
					alerta("info","Se modifico correctamente");
					$("#formularios_modulo").hide("slide",{direction:'right'},rapidez,function(){
					$("#botones_modulo").fadeIn(rapidez); });			
				}else{
					alerta("error",r.info);
				}
			}
		});
    });
   

/////////////////////////////////////////////////////////
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
  <h3 class="titulo_form">EMPLEADO</h3>
  	<input type="hidden" name="id_cliente" id="id_cliente" class="id_cliente" value="1"/>
    <div class="campo_form">
    <input type="hidden" name="clave" id="clave" class="clave cliente_clave text_corto requerido mayuscula"
	value="<?php echo isset($aidi) ?  $aidi :  '1'  ?>"
    </div>
    <div class="campo_form">
    <label class="label_width">Nombre</label>
    <input type="text" name="nombre" id="nombre" class="nombre text_largo nombre_buscar">
    </div>
    <div class="campo_form">
    <label class="label_width">Puesto</label>
    <input type="text" name="puesto" id="puesto" class="puesto text_mediano">
    </div>
    <input class="boton_dentro" type="reset" value="Limpiar" onclick="limpiar();" />
</form>

<table>
<tr>
<td>
<form id="f_clientes_contacto" class="formularios">
  <h3 class="titulo_form">DATOS DE CONTACTO</h3>
  <input type="hidden" name="id" class="id" />
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
        <input type="text" name="cp" id="cp" class="cp">
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
     <div class="campo_form">
        <label class="label_width">Precio de compra</label>
        <input type="number" name="pcompra" id="pcompra" class="pcompra">
    </div>
     <div class="campo_form">
        <label class="label_width">Precio de venta</label>
        <input type="number" name="pventa" id="pventa" class="pventa">
    </div>
</form>
</td>
<td >
<form id="f_clientes_fiscal" class="formularios" style="margin-left:30px;">
  <h3 class="titulo_form">Dias que puede laborar</h3>
 <div class="campo_form">
        <label class="label_width">Lunes</label>
         <input type="checkbox" name="lun" id="lun" value="ln">
    </div>
    <div class="campo_form">
        <label class="label_width">Martes</label>
         <input type="checkbox" name="mar" id="mar" value="m">
    </div>
    <div class="campo_form">
        <label class="label_width">Miercoles</label>
         <input type="checkbox" name="mie" id="mie" value="mi">
    </div>
    <div class="campo_form">
        <label class="label_width">Jueves</label>
         <input type="checkbox" name="jue" id="jue" value="j">
    </div>
    <div class="campo_form">
        <label class="label_width">Viernes</label>
         <input type="checkbox" name="vie" id="vie" value="v">
    </div>
    <div class="campo_form">
        <label class="label_width">Sabado</label>
         <input type="checkbox" name="sab" id="sab" value="s">
    </div>
    <div class="campo_form">
        <label class="label_width">Domingo</label>
         <input type="checkbox" name="dom" id="dom" value="d">
    </div>
    </form>
	</td>
	</tr>
	</table>
	
	
	
	
	
    <div align="right">
        <input type="button" class="guardar" value="GUARDAR" data-wrap="#" data-accion="nuevo" data-m="pivote" />
        <input type="button" class="modificar" value="MODIFICAR" style="display:none;" />
    	<input type="button" class="volver" value="VOLVER">
    </div>
	
	
</div>
<div class="formularios">
<h3 class="titulo_form">Listado de empleados registrados</h3>
	<table style="width:100%;">
    	<tr>
        	<th>CLAVE<br /><font style="font-size:0.4em; color:#999;">Doble Clic<br />para modificar</font></th>
            <th>NOMBRE</th>
        </tr>
        
    <?php 
    $num=1;
    if(count($clientes)>0){foreach($clientes as $art=>$d){
		echo '<tr id='.$d["id_empleado"].'>';
		echo '<td id="'.$d["id_empleado"].'" class="dbc" data-action="clave">'.$num.'</td>';
		echo '<td>'.$d["nombre"].'</td>';
		echo '<td><img src="../img/cruz.png" height="20" onclick="eliminar('.$d["id_empleado"].');" /></td>';
		echo '</tr>';
		$num++;
	
	}} ?>
    </table>
</div>
<script>
function ClaveCliente(){
	
	
	
	$(".id_cliente").val('');
	dato=$(".cliente_clave").val();
	input=$(".cliente_clave");
	input.addClass("ui-autocomplete-loading");
	$.ajax({
	  url:"scripts/busca_empleado1.php",
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
	  }
	});
	realizaProceso($('#clave').val());return false;
}
</script>

<script>
function eliminar(aidi)
{


$.ajax({
			url:'scripts/eliminar_empleado.php',
			cache:false,
			async:false,
			type:'POST',
			data:{
				
				'id':aidi
				
			},
			success: function(r){
				
				if(r.continuar){
					alerta("info","Empleado eliminado exitosamente");
					document.getElementById(aidi).remove();
				}else{
					alerta("info","Empleado eliminado exitosamente");
					document.getElementById(aidi).remove();
				}
			}
		});

}

</script>
<script>
function limpiar()
	{
	nombre = document.getElementById("nombre").value="";
		
		puesto = document.getElementById("puesto").value="";
		
		pcompra= document.getElementById("pcompra").value="";
		pventa= document.getElementById("pventa").value="";
		
	direccion = document.getElementById("direccion").value="";
		colonia = document.getElementById("colonia").value="";
		ciudad = document.getElementById("ciudad").value="";
		estado = document.getElementById("estado").value="";
		cp = document.getElementById("cp").value="";
		telefono = document.getElementById("telefono").value="";
		celular = document.getElementById("celular").value="";
		email = document.getElementById("email").value="";
		
		email = document.getElementById("lun").checked=false;
		email = document.getElementById("mar").checked=false;
		email = document.getElementById("mie").checked=false;
		email = document.getElementById("jue").checked=false;
		email = document.getElementById("vie").checked=false;
		email = document.getElementById("sab").checked=false;
		email = document.getElementById("dom").checked=false;
		
		 $(".guardar").css({ "display": 'inline'});
		$(".modificar").css({ "display": 'none'});
	}
</script>