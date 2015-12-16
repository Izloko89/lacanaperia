// JavaScript Document
$(document).ready(function(e) {
    //busca cliente
	$( ".nombre" ).autocomplete({
      source: "scripts/busca_concepto.php",
      minLength: 1,
      select: function( event, ui ) {
		//asignacion individual alos campos
		$("#f_concepto .id_concepto").val(ui.item.id_tipo);
		$(".modificar").show();
		$(".guardar_individual").hide();
	  }
    });
	$(".nombre").keyup(function(e) {
        if(e.keyCode==8){
			if($(this).val()==""){
				$(".modificar").hide();
				$(".guardar_individual").show();
			}
		}
    });
});
	function eliminar_art(elemento, id_item){
		$.ajax({
			url:'scripts/eConcepto.php',
			cache:false,
			type:'POST',
			data:{
				'id_item':id_item
			},
			success: function(r){
			  if(r){
				document.getElementById("tableEve").deleteRow(elemento);
				alerta("info","<strong>Concepto</strong> Eliminado");
			  }else{
				alerta("error", r);
			  }
			}
		});
	}

