// JavaScript Document
$(document).ready(function(e) {
    //alerta("info","Vista de eventos NO DISPONIBLE todavía");
	$("#tabs").tabs({
		heightstyle:"content"
	});

    $('#btn-nuevacot').click(function(){
    	id = $(this).attr('class');
    	$('.conceptos[id='+(id)+']').show();
    	$(this).hide();
    });

	$( ".clave_evento" ).keyup(function(){
		_this=$(this);
		if(typeof timer=="undefined"){
			timer=setTimeout(function(){
				buscarClaveGet();
			},300);
		}else{
			clearTimeout(timer);
			timer=setTimeout(function(){
				buscarClaveGet();
			},300);
		}
    }); //termina buscador de evento
    //busca cliente
	$( ".nombre" ).autocomplete({
      source: "scripts/busca_evento_nombre.php",
      minLength: 1,
      select: function( event, ui ) {
      	$('.clave').val(ui.item.id_cotizacion);
		buscarClaveGet();
		$(".modificar").show();
	  }
    });
	
	//para añadir más articulos al evento
$(".agregar_articulo").click(function(){
		id=$(".lista_articulos").length+1;
		$.get("scripts/get_conceptos.php", function(r){
				var columnas="";
				columnas+='<tr id="'+id+'" class="lista_articulos"><td style="background-color:#FFF;"><input type="hidden" class="id_item" value="" /><input type="hidden" class="id_evento" value="" /><input type="hidden" class="id_articulo" /><input type="hidden" class="id_paquete" /></td>';
				$.each(r, function(i, item) {
   					if(id==1){
   						i == 0 ? columnas+='<td><select id='+id+' class="conceptos" width="130" style="width: 130px"> <option value="'+item.id+'">'+item.nombre+'</option>' : columnas+='<option value="'+item.id+'">'+item.nombre+'</option>';
    				}else{
  						idcon = $('.conceptos[id='+(id-1)+']').val();			

    					if(i == 0 ){  
    						if($.isNumeric(idcon)){
    							columnas+='<td><select id='+id+' class="conceptos" style="display:none" width="130" style="width: 130px">'
    						}else{columnas+='<td><select id='+id+' class="conceptos" width="130" style="width: 130px"> '}
    					}
    					idcon==item.id ?  columnas+='<option value="'+item.id+'" selected="selected">'+item.nombre+'</option>' : columnas+='<option value="'+item.id+'">'+item.nombre+'</option> ';
    					
    					$("#btn-nuevacot").attr('class', id);
    					$('#btn-nuevacot').show();

    				}
				});
				columnas+='</select></td><td><input class="cantidad" type="text" size="3" onkeyup="cambiar_cant('+id+')" /></td><td><input class="articulo_nombre text_full_width" onkeyup="art_autocompletar('+id+');" /></td><td>$<span class="precio"></span></td><td>$<span class="total"></span></td><td><span class="guardar_articulo" onclick="guardar_art('+id+')"></span><span class="eliminar_articulo" onclick="eliminar_art('+id+')"></span></td><td id="preview-img-'+id+'"></td></tr>';
				$("#articulos").append(columnas);
			$.each($(".lista_articulos"),function(i,v){
				$(this).find(".id_evento").val(evento);
			});
			$(".cantidad").numeric();		
		}).fail(function(){
			$("#articulos").append('<tr id="'+id+'" class="lista_articulos"><td style="background-color:#FFF;"><input type="hidden" class="id_item" value="" /><input type="hidden" class="id_evento" value="" /><input type="hidden" class="id_articulo" /><input type="hidden" class="id_paquete" /></td><td><select class="conceptos"><option value="0">-</option></select> </td><td><input class="cantidad" type="text" size="7" onkeyup="cambiar_cant('+id+')" /></td><td><input class="articulo_nombre text_full_width" onkeyup="art_autocompletar('+id+');" /></td><td>$<span class="precio"></span></td><td>$<span class="total"></span></td><td><span class="guardar_articulo" onclick="guardar_art('+id+')"></span><span class="eliminar_articulo" onclick="eliminar_art('+id+')"></span></td><td id="preview-img-'+id+'"></td></tr>');
			$.each($(".lista_articulos"),function(i,v){
				$(this).find(".id_evento").val(evento);
			});
			$(".cantidad").numeric();
		});
		
	});

	
	//para ver el formulario de pago
	$(".agregarpago").click(function(e) {
        $("#nuevopago").slideToggle(200);
    });
	//para ver historial de pago
	$(".historial").click(function(e) {
        $("#historial").slideToggle(200);
    });
	//para añadir pago
	$(".anadir").click(function(e) {
		eve=$(".id_evento").get(0).value;
		monto=$(".importe").val();
		fecha=$(".fechapago").val();
		cliente=$(".id_cliente").val();
		metodo=$(".metodo").val();
		//var banco=document.getElementById("bancos");
		banco = $(".bancos").val();
		idbanco = 0;
		if(monto != ""){
			$.ajax({
				url:'scripts/s_pagar.php',
				cache:false,
				type:'POST',
				data:{
					'eve':eve,
					'monto':monto,
					'fecha':fecha,
					'cliente':cliente,
					'metodo':metodo,
					'banco':banco
				},
				success: function(r){
					if(r.continuar){
						alerta("info","Pago añadido exitosamente");
						checarTotalEve(eve);
						historial(evento);
						$("#nuevopago input[type=text]").val('');
					}else{
						alerta("error",r.info);
					}
				}
			});
		}
	});
	$(".metodo").change(function(e) {
		$(".divplazos").hide();
		$(".divbancos").hide();
        if($(this).find("option:selected").val()=="A crédito"){
			$(".divplazos").show();
		}else if($(this).find("option:selected").val()=="Transferencia" || $(this).find("option:selected").val()=="Cheque" || $(this).find("option:selected").val()=="Tarjeta de credito" || $(this).find("option:selected").val()=="Tarjeta de débito"){
			$(".divbancos").show();
		}
    });

	$(".filtro").keyup(function(e) {
		if(e.keyCode!=9){
			if($(this).val()!=""){
				buscar=$(this).val();
				criterio=$(this).attr("data-c");
				$("."+criterio+":not(:contains("+buscar+")):visible").parent().hide();
				$("."+criterio+":contains("+buscar+"):visible").parent().show();
			}else{
				$(".listado *").show();
			}
		}
    });


});
function historial(eve){
	$.ajax({
		url:'scripts/s_historial_pago.php',
		cache:false,
		type:'POST',
		data:{
			'eve':eve
		},
		success: function(r){
			$("#historial .mostrar").html(r);
		}
	});
	//funcion para ver el historial de pagos del evento
}
function cToT(e)
{
 precio = $(e).val();
 $(e).parent().find(".precio").val(precio);
 darprecio($(e).parent().find(".precio"));
}
function buscarClaveGet(id){
	evento="";
	dato=$(".clave").val();
	input=$(".clave_evento");
	input.addClass("ui-autocomplete-loading-left");
	$.ajax({
	  url:"scripts/busca_evento.php",
	  cache:false,
	  data:{
		term:dato
	  },
	  success: function(r){
		form="eventos";
		//console.log(r);
		//asigna el valor en el campo
		
		//añade selecciona option del select
		if(r.bool){
			//graba los datos en los campos correspondientes
			value=r.id_tipo;
			$(".id_tipo option[value='"+value+"']").prop("selected",true);
			
			//para los radio
			eventosalon=r.eventosalon;
			$(".eventosalon").parent().find("."+eventosalon+"r").click();
			
			$.each(r,function(i,v){
				if(i!="label" && i!="id_tipo" && i!="tipo"){
					//console.log(i+" "+v);
					selector="#"+form+" ."+i
					$(selector).val(v);
				}
			});//*/
			
			//asigna el id de cotización
			evento=r.id_evento;
			$(".id_evento").val(evento);
			$(".clave").val(evento);
			get_items_eve(evento);
			checarTotalEve(evento);
			historial(evento);
			//getObservaciones(evento);
			//le da el nombre al boton
			$(".guardar").hide();
			$(".modificar").show();
		}else{
			$("#reset").click();
			alerta("info","Este evento no se ha generado o no existe");
		}
		input.removeClass("ui-autocomplete-loading-left");
	  }
	});
}

// function getObservaciones(evento){
// 	$.ajax({
// 		url:'scripts/get_observaciones_eve.php',
// 		cache:false,
// 		async:false,
// 		data:{
// 			'id_evento':evento
// 		},
// 		success: function(r){
// 			$('#encargado').val(r.encargado);
// 			$('#unidad').val(r.unidad);
// 			$('#monta').val(r.monta);
// 			$('#observaciones').val(r.obs);
// 		}
// 	});
// }
function get_items_eve(id){
	$(".lista_articulos").remove();
	$.ajax({
		url:'scripts/get_items_eve.php',
		cache:false,
		async:false,
		data:{
			'id_evento':id
		},
		success: function(r){
			$("#articulos").append(r);
		}
	});
}

function editar(e, id){
	s=$(e);
	$(".clave").val(id);
	buscarClaveGet(id);
	$(".hacer a")[0].click();
}
function quitar_verde(e){
	$(e).parent().parent().removeClass("verde_ok");
}

function guardar_art(elemento){
	row=$("#"+elemento);
	padre=$("#"+elemento).parent();
	
	//mostrar que se esta procesando
	//procesando("mostrar",0);
	
	//checa si se modificó el total
	actTotal=true;
	if(row.hasClass("verde_ok")){
		actTotal=false;
	}
	
	id_item=$("#"+elemento+" .id_item").val();
	id_articulo=$("#"+elemento+" .id_articulo").val();
	id_paquete=$("#"+elemento+" .id_paquete").val();
	id_evento=$(".id_evento").first().val();
	cantidad=$("#"+elemento+" .cantidad").val();
	precio=$("#"+elemento+" .precio").val();
	total=$("#"+elemento+" .total").html();
	$.ajax({
		url:'scripts/guarda_art_eve.php',
		cache:false,
		type:'POST',
		data:{
			'id_item':id_item,
			'id_paquete':id_paquete,
			'id_articulo':id_articulo,
			'id_evento':id_evento,
			'cantidad':cantidad,
			'precio':precio,
			'total':total,
			boolTotal:actTotal
		},
		success: function(r){
			console.log('success');
			console.log(r.sql);
			if(r.continuar){
				$("#"+elemento+" .id_item").val(r.id_item);
				padre.find(".id_evento").val(id_evento);
				alerta("info","Fue agregado exitosamente");
				row.addClass("verde_ok");
				checarTotalEve(id_evento);
				setTimeout(function(){checarTotal('eventos',id_evento);},500);
			  }else{
			  	console.log('error');
				alerta("error",r.info);
			  }
		}
	});
}
function eliminar_art(elemento){
	
	id_evento=$(".id_evento").first().val();
	id_item=$("#"+elemento+" .id_item").val();
	precio=$("#"+elemento+" .total").html();

	if(id_item!=0){
		$.ajax({
			url:'scripts/quita_art_eve.php',
			cache:false,
			type:'POST',
			data:{
				'id_item':id_item,
				'id_evento':id_evento,
				'precio':precio
			},
			success: function(r){
			  if(r.continuar){
				alerta("info",r.info);
				$("#"+elemento).remove();
				checarTotalEve(id_evento);
			  }else{
				alerta("error",r.info);
			  }
			}
		});
	}else{
		$("#"+elemento).remove();
	}
}
function art_autocompletar(id){
	padre=$("#"+id);
	cantidad=padre.find(".cantidad").val()*1;
	id_articulo=padre.find(".id_articulo");
	id_paquete=padre.find(".id_paquete");
	precio=padre.find(".precio").parent();
	total=padre.find(".total");
	$( "#"+id+" .articulo_nombre").autocomplete({
	  source: "scripts/busca_articulos.php",
	  minLength: 1,
	  select: function( event, ui ) {
		  total.parent().parent().removeClass("verde_ok");
		  id_articulo.val(ui.item.id_articulo);
		  id_paquete.val(ui.item.id_paquete);
		  id_articulo.val(ui.item.id_articulo);
			art = ui.item.id_articulo;
			cot = $(".clave").val();
		  precio.html(ui.item.precio);
		  totalca=cantidad*ui.item.precio;
		  total.html(totalca);	
		  $('#preview-img-'+id).empty().append('<img src="img/articulos/'+ui.item.image+'" width="70" height="70" />');		
		  $.ajax({
				url:'scripts/busca_existenciaEve.php',
				cache:false,
				async:false,
				data:{
					'art':art,
					'cot':cot,
					'cant':cantidad
				},
				success: function(r){
					if(r){
						alerta("info", r);
					}
				}
			});
	  }
	});
}
function cambiar_cant(id){
	padre=$("#"+id);
	cantidad=padre.find(".cantidad").val()*1;
	precio=padre.find(".precio").val()*1;
	total=cantidad*precio;
	padre.find(".total").html(total);
	padre.removeClass("verde_ok");
}
function darprecio(e){
	precio=$(e).val();
	$(e).parent().parent().removeClass("verde_ok");
	cant=$(e).parent().parent().find(".cantidad").val();
	$(e).siblings(".precio").val(precio);
	total=(precio*1)*(cant*1);
	$(e).parent().parent().find(".total").html(total);
}
function autorizarEve(id,clave){
	$.ajax({
		url:'scripts/s_autorizar_evento.php',
		cache:false,
		data:{
			id_evento:id
		},
		success: function(r){
			if(r.estatus)
			{
				alerta("info","Este evento ya ha sido autorizado con anterioridad");
			}else{
			if(r.continuar){
				alerta('info','El evento '+clave+' ha sido autorizado');
				$("tr.cot"+clave).find(".bestatus").html('Evento');
			}else{
				alerta('error',r.info);
			}
		}}
	});
}
function eliminar_eve(id, row){
	$.ajax({
		url:'scripts/deleteEve.php',
		cache:false,
		type:'POST',
		data:{
			id_evento:id
		},
		success: function(r){
				alerta('info','El evento '+row+' ha sido eliminado');
				document.getElementById("tablaEve").deleteRow(row);
		}
	});
}
function revocarEve(id,clave){
	$.ajax({
		url:'scripts/s_revocar_evento.php',
		cache:false,
		type:'POST',
		data:{
			id_evento:id
		},
		success: function(r){
			if(r.continuar){
				alerta('info','El evento '+clave+' ha sido revocado');
				$("tr.cot"+clave).find(".bestatus").html('Sin autorizar');
			}else{
				alerta('error',r.info);
			}
		}
	});
}
function checarTotalEve(id){
	var total;
	$.ajax({
		url:'scripts/s_check_total_eve.php',
		cache:false,
		async:false,
		type:'POST',
		data:{
			'id':id
		},
		success: function(r){
			if(r.continuar){
				$(".totalevento").val(r.total);
				if(r.restante > 0){
					$(".restante").val(r.restante);
				}else{
					$(".restante").val(0);
				}

			}else{
				alerta("error",r.info);
			}
		}
	});


}