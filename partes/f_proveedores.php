<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
$emp=$_SESSION["id_empresa"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT
		*
	FROM proveedores
	";
	$res=$bd->query($sql);
	$proveedores=array();
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
		$proveedores[$d["id_proveedor"]]=$d;
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
	$(".direccion, .colonia, .ciudad, .estado, .cp").focusout(function(e) {
		$("."+$(this).attr("name")).val($(this).val());
    });
	$( ".proveedor_clave" ).keyup(function(e){
		_this=$(this);
		if(e.keyCode!=8 && _this.val()!=""){
			if(typeof timer=="undefined"){
				timer=setTimeout(function(){
					ClaveProv();
				},300);
			}else{
				clearTimeout(timer);
				timer=setTimeout(function(){
					ClaveProv();
				},300);
			}
		}else{
			resetform();
		}
    }); //termina buscador de cotizacion
	$(".dbc").dblclick(function(e) {
        accion=$(this).attr("data-action");
		val=$(this).text();
		switch(accion){
			case 'clave':
				$(".clave").val(val);
				scrollTop();
				ClaveProv();
			break;
		}
    });
});
</script>
<form id="f_proveedores" class="formularios">
  <h3 class="titulo_form">PROVEEDOR</h3>
  	<input type="hidden" name="id_proveedor" class="id_proveedor" />
    <div class="campo_form">
    <label class="label_width">CLAVE</label>
    <input type="text" name="clave" class="clave proveedor_clave text_corto requerido mayuscula" value="<?php nCveProv(); ?>">
    </div>
    <div class="campo_form">
    <label class="label_width">Nombre</label>
    <input id="nombre_buscar" type="text" name="nombre" class="nombre text_largo">
    </div>
    <div class="campo_form">
    <label class="label_width">Límite de crédito</label>
    <input type="text" name="limitecredito" class="limitecredito text_mediano">
    </div>
    <input class="boton_dentro" type="reset" value="Limpiar" />
</form>
<form id="f_proveedores_contacto" class="formularios">
  <h3 class="titulo_form">DATOS DE CONTACTO</h3>
  <input type="hidden" name="id" class="id" />
  <input type="hidden" name="id_empresa" value="<?php echo $_SESSION["id_empresa"]; ?>" />
    <div class="campo_form">
        <label class="label_width">CLAVE</label>
        <input type="text" name="clave" class="requerido mayuscula clave">
    </div>
    <div class="campo_form">
        <label class="label_width">Dirección</label>
        <input type="text" name="direccion" class="direccion">
    </div>
    <div class="campo_form">
        <label class="label_width">Colonia</label>
        <input type="text" name="colonia" class="colonia">
    </div>
    <div class="campo_form">
        <label class="label_width">Ciudad</label>
        <input type="text" name="ciudad" class="ciudad">
    </div>
    <div class="campo_form">
        <label class="label_width">Estado</label>
        <input type="text" name="estado" class="estado">
    </div>
    <div class="campo_form">
        <label class="label_width">Código Postal</label>
        <input type="text" name="cp" class="cp">
    </div>
    <div class="campo_form">
        <label class="label_width">Telefono</label>
        <input type="text" name="telefono" class="telefono">
    </div>
    <div class="campo_form">
        <label class="label_width">Celular o Nextel</label>
        <input type="text" name="celular" class="celular">
    </div>
    <div class="campo_form">
        <label class="label_width">E-mail</label>
        <input type="text" name="email" class="email">
    </div>
</form>
<form id="f_proveedores_fiscal" class="formularios">
  <h3 class="titulo_form">INFORMACIóN FISCAL</h3>
  <input type="hidden" name="id" class="id" />
  <input type="hidden" name="id_empresa" value="<?php echo $_SESSION["id_empresa"]; ?>" />
    <div class="campo_form">
        <label class="label_width">RFC</label>
        <input type="text" name="rfc" class="requerido mayuscula rfc">
    </div>
    <div class="campo_form">
        <label class="label_width">Razón social</label>
        <input type="text" name="razon" class="requerido razon">
    </div>
    <div class="campo_form">
        <label class="label_width">Nombre Comercial</label>
        <input type="text" name="nombrecomercial" class="requerido nombrecomercial">
    </div>
    <div class="campo_form">
        <label class="label_width">Direccion Fiscal</label>
        <input type="text" name="direccion" class="requerido direccion">
    </div>
    <div class="campo_form">
        <label class="label_width">Colonia</label>
        <input type="text" name="colonia" class="requerido colonia">
    </div>
    <div class="campo_form">
        <label class="label_width">Ciudad</label>
        <input type="text" name="ciudad" class="requerido ciudad">
    </div>
    <div class="campo_form">
        <label class="label_width">Estado</label>
        <input type="text" name="estado" class="requerido estado">
    </div>
    <div class="campo_form">
        <label class="label_width">Código Postal</label>
        <input type="text" name="cp" class="requerido cp">
    </div>
    </form>
    <div align="right">
        <input type="button" class="guardar" value="GUARDAR" data-accion="nuevo" data-m="pivote" />
        <input type="button" class="modificar" value="MODIFICAR" style="display:none;" />
    	<input type="button" class="volver" value="VOLVER">
    </div>
</div>
<div class="formularios">
<h3 class="titulo_form">Listado de proveedores registrados</h3>
	<table style="width:100%;">
    	<tr>
        	<th>CLAVE<br /><font style="font-size:0.4em; color:#999;">Doble Clic<br />para modificar</font></th>
            <th>NOMBRE</th>
        </tr>
        
    <?php if(count($proveedores)>0){foreach($proveedores as $art=>$d){
		echo '<tr>';
		echo '<td class="dbc" data-action="clave">'.$d["clave"].'</td>';
		echo '<td>'.$d["nombre"].'</td>';
		echo '</tr>';
	}//foreach
	} ?>
    </table>
</div>
<script>
function ClaveProv(){
	$(".id_cliente").val('');
	dato=$(".proveedor_clave").val();
	input=$(".proveedor_clave");
	input.addClass("ui-autocomplete-loading");
	$.ajax({
	  url:"scripts/busca_proveedores.php",
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
		datosContacto(r[0].id_proveedor,"proveedores");
		datosFiscal(r[0].id_proveedor,"proveedores");
		//asigna el id de cotización
		input.removeClass("ui-autocomplete-loading");
	  }
	});
}
</script>