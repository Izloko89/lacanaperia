<script src="js/formularios.js"></script>
<?php
include("../scripts/datos.php");
?>
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
<script>
	function eliminar_art(elemento, id_item){
		$.ajax({
			url:'scripts/eBanco.php',
			cache:false,
			type:'POST',
			data:{
				'id_item':id_item
			},
			success: function(r){
			  if(r){
				document.getElementById("tableEve").deleteRow(elemento);
				alerta("info","<strong>Banco</strong> Eliminado");
				$(".volver").click();
			  }else{
				alerta("error", r);
			  }
			}
		});
	}
</script>
<form id="f_bancos" class="formularios">
<h3 class="titulo_form">Banco</h3>
    <div class="campo_form">
        <label class="label_width">Nombre del banco</label>
        <input type="text" name="nombre" class="text_mediano">
    </div>
    <div class="campo_form">
        <label class="label_width">No de Cuenta</label>
        <input type="text" name="cuenta" class="text_largo">
    </div>
    <div class="campo_form">
        <label class="label_width">CLABE</label>
        <input type="text" name="clabe" class="text_largo">
    </div>
    <div align="right">
        <input type="button" class="guardar_individual" value="GUARDAR" data-m="individual">
    </div>
</form><table id="tableEve">
<tr><td><h2>Area</h2></td></tr>
<?php
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		$res=$bd->query("SELECT * FROM bancos order by nombre;");
		$cont = 1;
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			echo '<tr class="salon fondo_azul"><td ><div align="left" >'.$v["nombre"].'</div></td><td colspan="2" align="right"><span class="eliminar_tevento" onclick="eliminar_art(' . $cont . ', ' . $v["id_banco"] . ');"> <input id="idTipo" type="hidden" value="' . $v["id_banco"] . '"/></td></tr>';
			$cont++;
		}
	}catch(PDOException $err){
		echo '<tr><td colspan="20">Error encontrado: '.$err->getMessage().'</td></tr>';
	}
?>
</table>
<div align="right">
	<input type="button" class="volver" value="VOLVER">
</div>