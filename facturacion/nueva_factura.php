<?php 
	session_start();

	include "../conexion.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Nueva Factura</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class="title_page">
			<h1>Nueva Factura</h1>
		</div>
		<div class="datos_cliente">
			<div class="action_cliente">
				<h1>Datos del Cliente</h1>
				<a href="#" class="btn_new btn_new_cliente">Nuevo cliente</a>
			</div>
			<form name="form_new_cliente_factura" id="form_new_cliente_factura" class="datos">
				<input type="hidden" name="action" value="addCliente">
				<input type="hidden" id="idcliente" name="idcliente" value="" required>
				<div class="ancho30">
					<label>NIF</label>
					<input type="text" name="nif_cliente" id="nif_cliente">
				</div>
				<div class="wd30">
					<label>Nombre</label>
					<input type="text" name="nom_cliente" id="nom_cliente" disabled required>
				</div>
				<div class="wd30">
					<label>Teléfono</label>
					<input type="number" name="tel_cliente" id="tel_cliente" disabled required>
				</div>
				<div class="wd100">
					<label>Dirección</label>
					<input type="text" name="dir_cliente" id="dir_cliente" disabled required>
				</div>
				<div id="div_registro_cliente" class="wd100">
					<button type="submit" class="btn_save">Guardar</button>
				</div>
			</form>
		</div>
		<div class="datos_factura">
			<h4>Datos de Venta</h4>
			<div class="datos">
				<div class="wd50">
					<label>Vendedor</label>
					<p><?php echo $_SESSION['nombre']; ?></p>
				</div>
				<div class="wd50">
					<label>Acciones</label>
					<div id="acciones_factura">
						<a href="#" class="btn_ok textcenter" id="btn_anular_venta">Anular</a>
						<a href="#" class="btn_new textcenter" id="btn_facturar_venta" style="display:none;">Procesar</a>
					</div>
				</div>
			</div>
		</div>
		<table class="tbl_factura">
			<thead>
				<tr>
					<th width="100px">Código</th>
					<th>Descripción</th>
					<th>Existencia</th>
					<th width="100px">Cantidad</th>
					<th class="textright">Precio</th>
					<th class="textright">Precio Total</th>
					<th>Acción</th>
				</tr>
				<tr>
					<td><input type="text" name="txt_cod_producto" id="txt_cod_producto"></td>
					<td id="txt_descripcion">-</td>
					<td id="txt_existencia">-</td>
					<td><input type="text" name="txt_cant_producto" id="txt_cant_producto" value="0" min="1" disabled>
					</td>
					<td id="txt_precio" class="textright">0.00</td>
					<td id="txt_precio_total" class="textright">0.00</td>
					<td><a href="#" id="add_product_venta" class="link_add">Agregar</a></td>
				</tr>
				<tr>
					<th>Código</th>
					<th colspan="2">Descripción</th>
					<th>Cantidad</th>
					<th class="textright">Precio</th>
					<th class="textright">Precio Total</th>
					<th>Acción</th>
				<tr>
			</thead>
			<tbody id="detalle_venta">
				<!--Contenido Ajax -->
			</tbody>
			<tfoot id= "detalle_totales" >
				<!--Contenido Ajax -->
				
			</tfoot>
		</table>
		
	</section>
	<!--Ejecuto la función despues de q se haya cargado el html, de esta manera si cambio de ventana la ventana de factura no se recarga -->
	<script type="text/javascript">
		$(document).ready(function(){
			
			var usuarioid = '<?php echo $_SESSION['idUser']; ?>';
			serchForDetalle(usuarioid);
		});
	</script>

</body>
</html>