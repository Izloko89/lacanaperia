// JavaScript Document
$(document).ready(function(e) {
    //busca cliente
    
	$( ".nombre" ).autocomplete({
      source: "scripts/busca_concepto.php",
      minLength: 1,
      select: function( event, ui ) {
		//asignacion individual alos campos
		//var clean1 = $(".descripcion").val(ui.item.descripcion.replace('<p>',''));
		var str = ui.item.descripcion.replace(/\<p\\?>/g, "");
		var str2 = str.replace(/\<\/p\\?>/g, "\n");
		
		var clean1 = ui.item.descripcion.replace('<p>','');
		$(".descripcion").html(str2);
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

    $(".dbc").dblclick(function(e) {
		val=$(this).text();
		//console.log(val);
		$.get( "scripts/busca_concepto.php", 
			{ 
				term: val 
			} )
  		.done(function(ui) {
  			//console.log(ui);
  			$.each(ui, function( index, item ) {
  				console.log(item);
  				//alert( index + ": " + item );
  				var str = item.descripcion.replace(/\<p\\?>/g, "");
				var str2 = str.replace(/\<\/p\\?>/g, "\n");
				
				var clean1 = item.descripcion.replace('<p>','');
				$(".descripcion").empty().val(str2);
				$(".modificar").show();
				$(".guardar_individual").hide();
			});
		    	
  		});
		$(".nombre").val(val);

	});
	$(".nueva").click(function(e) {
		
        $.each($("form"),function(i,v){
			this.reset();
		});
		//limpia los campos input ocultos
		$("input[type=hidden]").val('');
		$(".descripcion").empty().html('');
		$(".guardar").show();
		$(".modificar").hide();
    });
    $(".volver").click(function(e) {
		ingresar=true;
    	$("#formularios_modulo").hide("slide",{direction:'right'},rapidez,function(){
			$("#botones_modulo").fadeIn(rapidez);
		});
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

	function guardar_concepto(){
		//if (requerido()) {
			term = document.getElementById("nombre").value;
			//term1 = document.getElementById("titulo").value;
			term2 = document.getElementById("descripcion").value;
			//datos de los formularios
			//procesamiento de datos
			$.ajax({
				url:'scripts/s_guardar_conceptos.php',
				cache:false,
				async:false,
				type:'POST',
				data:{
					'term':term,
					//'term1':term1,
					'term2':term2
				},
				success: function(r){
					if(r){
						alerta("info","Registro añadido satisfactoriamente");
						ingresar=true;
						$("#formularios_modulo").hide("slide",{direction:'right'},rapidez,function(){
							$("#botones_modulo").fadeIn(rapidez);
						});
					}else{
						alerta("error","ocurrio un error al agregar el registro");
					}
				}
			});
		//}
	}

	function modificar_concepto(){
		//$(".modificar").click(function(e) {
    	//if (requerido()) {
			term = document.getElementById("nombre").value;
			//term1 = document.getElementById("titulo").value;
			term2 = document.getElementById("descripcion").value;
			//console.log("modificar");
			//datos de los formularios
			//procesamiento de datos
			$.ajax({
				url:'scripts/s_modificar_concepto.php',
				cache:false,
				async:false,
				type:'POST',
				dataType: 'json',
				data:{
					'term':term,
					//'term1':term1,
					'term2':term2
				},
				success: function(res){
					console.log(res);
					if(res){
						alerta("info","Registro modificado satisfactoriamente");
						ingresar=true;
						$("#formularios_modulo").hide("slide",{direction:'right'},rapidez,function(){
							$("#botones_modulo").fadeIn(rapidez);
						});
					}else{
						alerta("error","ocurrio un error al modificar el registro");
					}
				}
			});
		//}
    //});
	}