function calculoImporte(cantidad,precio,importe){

	/* Parametros:
	cantidad - entero con la cantidad
	precio - entero con el precio
	inputtotal - nombre del elemento del formulario donde ira el total
	*/
	
	// Calculo del subtotal
	importe.value = parseFloat(precio*cantidad).toFixed(2);
	
	
}


// document.querySelector('#total').addEventListener('change', (event) => {

// 	if (parseFloat($('#total').val()) <= 0) {
// 		console.log(parseFloat($('#total').val()));
// 		$("#bt_finalizar").prop('disabled', true);
// 	}
// 	if (parseFloat($('#total').val()) > 0) {
// 		console.log(parseFloat($('#total').val()));
// 		$("#bt_finalizar").prop('disabled', false);
// 	}

// });



// if (parseFloat($('#total').val()) <= 0) {
// 	console.log(parseFloat($('#total').val()));
// 	$("#bt_finalizar").prop('disabled', true);
// }else{
// 	console.log(parseFloat($('#total').val()));
// 	$("#bt_finalizar").prop('disabled', false);
// }





$(document).ready(function() {


//array de productos y cantidad de cada uno
const productos = [];
const cantidades = [];





$('.cancelar').click(function() {


	var id = $(this).prop('value');
	// var cancelId = "#cantidad"+id;
	var prodNombre = ".nomProd"+id;
	var precioId = "#precio"+id;
	var cantidadId = "#cantidad"+id;
	var importId = ".importe"+id;

	
	parseFloat($(importId).val(parseFloat($(precioId).val())));


	parseFloat(parseFloat($('#total').val( parseFloat($('#total').val()) - (parseFloat($(precioId).val())*parseFloat($(cantidadId).val())) )  )).toFixed(2);
	if (parseFloat($('#total').val()) < 0) {

		parseFloat($('#total').val(0));




	}

	var indexP = productos.indexOf($(prodNombre).val());
	console.log(indexP);
	if (indexP > -1) {
		productos.splice(indexP, 1);
		cantidades.splice(indexP, 1);

		var vPool1="";
		jQuery.each(productos, function(i, val1) {
			vPool1 += val1 + "<br />";
		});
		$('#listaProductos').html(vPool1);

		$('#listaProductos2').val(productos);




		var vPool2="";
		jQuery.each(cantidades, function(i, val2) {
			vPool2 += val2 + "<br />";
		});
		$('#listaCantidades').html(vPool2);

		$('#listaCantidades2').val(cantidades);
	}
	
	$(cantidadId).val(1);
	$('#listaTotal').html($('#total').val());

	// productos = productos.filter(function(producto) {
	// 	return producto.brand != $(prodNombre).val(); 
	// });

	console.log(parseFloat($('#total').val()));
});



// MANEJO DE VALORES
$('.confirmar').click(function() {


	var id = $(this).prop('value');


	var importId = ".importe"+id;

	parseFloat($('#total').val()).toFixed(2);
	parseFloat($(importId).val());
	parseFloat($('#total').val(  parseFloat($('#total').val()) + parseFloat($(importId).val()) )).toFixed(2);

	console.log("TOTAL PRODUCTO: " + $('#total').val());


		//------------PRODUCTO Y CANTIDAD

		//NOMBRE DEL PRODUCTO
		var prodNombre = ".nomProd"+id;
		//CANTIDAD DEL PRODUCTO
		var prodCantidad = ".cantProd"+id;

		//INSERTAR VALORES PARALELOS
		if (productos.indexOf($(prodNombre).val()) === -1) {

			productos.push($(prodNombre).val());
			cantidades.push($(prodCantidad).val());
			console.log('Nuevo producto: ' + $(prodNombre).val());


			var vPool1="";
			jQuery.each(productos, function(i, val1) {
				vPool1 += val1 + "<br />";
			});
			$('#listaProductos').html(vPool1);

			$('#listaProductos2').val(productos);




			var vPool2="";
			jQuery.each(cantidades, function(i, val2) {
				vPool2 += val2 + "<br />";
			});
			$('#listaCantidades').html(vPool2);

			$('#listaCantidades2').val(cantidades);




			$('#listaTotal').html($('#total').val()+" €");

			$('#totalPedido2').val($('#total').val());



		}else if (productos.indexOf($(prodNombre).val()) > -1) {
			console.log($(prodNombre).val() + ' ya existe en la colección.');
		}
		// productos.push($(prodNombre).val());
		// cantidades.push($(prodCantidad).val());



		// var vPool1="";
		// jQuery.each(productos, function(i, val1) {
		// 	vPool1 += val1 + "<br />";
		// });
		// $('#listaProductos').html(vPool1);

		// $('#listaProductos2').val(productos);




		// var vPool2="";
		// jQuery.each(cantidades, function(i, val2) {
		// 	vPool2 += val2 + "<br />";
		// });
		// $('#listaCantidades').html(vPool2);

		// $('#listaCantidades2').val(cantidades);




		// $('#listaTotal').html($('#total').val()+" €");

		// $('#totalPedido2').val($('#total').val());



	});



$('#confirmarPedido').click(function() {

	if (parseFloat($('#total').val()).toFixed(2) > 0) {
		document.formPedido.submit();

	}else{
		alert("Selecciona mínimo un producto antes de finalizar.");
	}
	

});









$('#confirmarBorradoEmpleado').click(function() {
	document.formBorradoEmpleado.submit();
});

$('#confirmarBorradoCliente').click(function() {
	document.formBorradoCliente.submit();
});

$('#confirmarBorradoProducto').click(function() {
	document.formBorradoProducto.submit();
});









});