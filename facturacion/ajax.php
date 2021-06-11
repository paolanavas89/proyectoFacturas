<?php 

	include "../conexion.php";
	session_start();
	//print_r($_POST);
	//exit;
	if(!empty($_POST)){
		//Extraer datos del producto
		if($_POST['action'] == 'infoProducto'){
			$producto_id = $_POST['producto'];

			$query = mysqli_query($conection,"SELECT codproducto,descripcion,precio,existencia FROM producto WHERE codproducto = $producto_id AND estatus = 1");
			mysqli_close($conection);

			$result = mysqli_num_rows($query);
			if($result > 0){
				$data = mysqli_fetch_assoc($query);
				//Para que no devuelva las tildes como un caracter extraño (JSON_UNESCAPED_UNICODE)
				//(json_encode)me devuelve el array en formato json				
				echo json_encode($data,JSON_UNESCAPED_UNICODE);
				exit;
			}
			echo 'Error';
			exit;
		}

		//Agregar productos a entrada

		if($_POST['action'] == 'addProduct'){
			
			if(!empty($_POST['cantidad']) || !empty($_POST['precio']) || !empty($_POST['producto_id'])){

				$cantidad = $_POST['cantidad'];
				$precio = $_POST['precio'];
				$producto_id = $_POST['producto_id'];
				$usuario_id = $_SESSION['idUser'];
				
				$query_insert = mysqli_query($conection,"INSERT INTO entradas(codproducto,cantidad,precio,usuario_id) VALUES($producto_id,$cantidad,$precio,$usuario_id)");

				if($query_insert){
					//Ejecuto el procedimiento almacenado
					$query_upd = mysqli_query($conection,"CALL actualizar_precio_producto($cantidad,$precio,$producto_id)");
					$result_pro = mysqli_num_rows($query_upd);
					if($result_pro > 0){
						$data = mysqli_fetch_assoc($query_upd);
						$data['producto_id'] = $producto_id;
						echo json_encode($data,JSON_UNESCAPED_UNICODE);
						exit;
					}

				}else{
						echo 'Error';
					}
				mysqli_close($conection);
			}else{
				echo 'Error';
			}
			exit;

		}
		//Eliminar producto
		if($_POST['action'] == 'delProduct'){
			if(empty($_POST['producto_id']) || !is_numeric($_POST['producto_id'])){
					echo 'Error';
				}else{
					$idproducto = $_POST['producto_id'];
					$query_delete = mysqli_query($conection,"UPDATE producto SET estatus = 0 WHERE codproducto = $idproducto");
					mysqli_close($conection);

					if($query_delete){
						echo 'OK';
					}else{
						echo 'error';
					}
					echo 'error';
				}
				
				exit;
		}

		//Buscar Cliente
		if($_POST['action'] == 'searchCliente'){
			if(!empty($_POST['cliente'])){
				$nif = $_POST['cliente'];
				$query = mysqli_query($conection,"SELECT * FROM cliente WHERE nif LIKE '$nif' and estatus = 1");

				mysqli_close($conection);
				$result = mysqli_num_rows($query);

				$data = '';
				if($result > 0){
						$data = mysqli_fetch_assoc($query);
				}else{	
					$data = 0;
				}	
				echo json_encode($data,JSON_UNESCAPED_UNICODE);
							
			}
			exit;
		}

		//Registrar cliente desde factura
		if($_POST['action'] == 'addCliente'){
			$nif = $_POST['nif_cliente'];
			$nombre = $_POST['nom_cliente'];
			$telefono = $_POST['tel_cliente'];
			$direccion = $_POST['dir_cliente'];
			$usuario_id = $_SESSION['idUser'];

			$query_insert = mysqli_query($conection,"INSERT INTO cliente(nif,nombre,telefono,direccion,usuario_id) 
					VALUES('$nif','$nombre','$telefono','$direccion','$usuario_id')");
			if($query_insert){
				$codCliente = mysqli_insert_id($conection);
				$msg = $codCliente;
			}else{
				$msg = 'error';
			}
			mysqli_close($conection);
			echo $msg;
			exit;
		}

		//Agregar productos a la tabla detalle temporal
		if($_POST['action'] == 'addProductoDetalle'){
			if(empty($_POST['producto']) || empty($_POST['cantidad'])){
				echo 'error';

			}else{
				$codproducto = $_POST['producto'];
				$cantidad = $_POST['cantidad'];
				$id_user = $_SESSION['idUser'];

				$query_iva = mysqli_query($conection,"SELECT iva FROM configuracion");
				$result_iva = mysqli_num_rows($query_iva);

				$query_detalle_temp = mysqli_query($conection,"CALL add_detalle_temp($codproducto, $cantidad,'$id_user')");
				$result = mysqli_num_rows($query_detalle_temp);

				$detalleTabla = '';
				$sub_total = 0;
				$iva =0;
				$total = 0;
				$arrayData = array();

				if($result > 0){

					if($result_iva > 0){
						//Almaceno en un array los campos de la tabla configuracion
						$info_iva = mysqli_fetch_assoc($query_iva);
						$iva = $info_iva['iva'];

					}
						//Almaceno en un array los campos de la tabla temporal
					while ($data = mysqli_fetch_assoc($query_detalle_temp)){
						//round funcion para redodear y indico q quiero solo 2 decimales
						$precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
						$sub_total = round($sub_total + $precioTotal, 2);
						$total = round($total + $precioTotal, 2);
						// ' . ' para concatenar el valor q ya tiene con las otras filas
						$detalleTabla .= '<tr>
											<td>'.$data['codproducto'].'</td>
											<td colspan="2">'.$data['descripcion'].'</td>
											<td class="textcenter">'.$data['cantidad'].'</td>
											<td class="textright">'.$data['precio_venta'].'</td>
											<td class="textright">'.$precioTotal.'</td>
											<td class="">
												<a class="link_delete" href="#" onclick="event.preventDefault();
													del_product_detalle('.$data['correlativo'].');">Eliminar</a>
											</td>
										</tr>';
					}
					$impuesto = round($sub_total * ($iva / 100), 2);
					$tl_sniva = round($sub_total - $impuesto,2);
					$total = round($tl_sniva + $impuesto,2);

					$detalleTotales = '<tr>
											<td colspan="5" class="textright">SUBTOTAL €</td>
											<td class="textright">'.$tl_sniva.'</td>
										</tr>
										<tr>
											<td colspan="5" class="textright">IVA ('.$iva.'%)</td>
											<td class="textright">'.$impuesto.'</td>
										</tr>
										<tr>
											<td colspan="5" class="textright">TOTAL €</td>
											<td class="textright">'.$total.'</td>
										</tr>';
					$arrayData['detalle'] = $detalleTabla;
					$arrayData['totales'] = $detalleTotales;

					//Retorno el array en formato json 
					echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);					
				}else{
					echo 'error';
				}
				mysqli_close($conection);
			}
			exit;
		}

		//Extraer datos del detalle_temp
		if($_POST['action'] == 'serchForDetalle'){
			if(empty($_POST['user'])){
				echo 'error';

			}else{
			
				$id_user = $_SESSION['idUser'];


				$query = mysqli_query($conection,"SELECT tmp.correlativo,tmp.token_usuario,tmp.cantidad,
										tmp.precio_venta,p.codproducto,p.descripcion FROM detalle_temp tmp
										INNER JOIN producto p ON tmp.codproducto = p.codproducto
										WHERE token_usuario = '$id_user' ");

				$result = mysqli_num_rows($query);

				$query_iva = mysqli_query($conection,"SELECT iva FROM configuracion");
				$result_iva = mysqli_num_rows($query_iva);

				

				$detalleTabla = '';
				$sub_total = 0;
				$iva =0;
				$total = 0;
				$arrayData = array();

				if($result > 0){

					if($result_iva > 0){
						//Almaceno en un array los campos de la tabla configuracion
						$info_iva = mysqli_fetch_assoc($query_iva);
						$iva = $info_iva['iva'];

					}
						//Almaceno en un array los campos de la tabla temporal
					while ($data = mysqli_fetch_assoc($query)){
						//round funcion para redodear y indico q quiero solo 2 decimales
						$precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
						$sub_total = round($sub_total + $precioTotal, 2);
						$total = round($total + $precioTotal, 2);
						// ' . ' para concatenar el valor q ya tiene con las otras filas
						$detalleTabla .= '<tr>
											<td>'.$data['codproducto'].'</td>
											<td colspan="2">'.$data['descripcion'].'</td>
											<td class="textcenter">'.$data['cantidad'].'</td>
											<td class="textright">'.$data['precio_venta'].'</td>
											<td class="textright">'.$precioTotal.'</td>
											<td class="">
												<a class="link_delete" href="#" onclick="event.preventDefault();
													del_product_detalle('.$data['correlativo'].');">Eliminar</a>
											</td>
										</tr>';
					}
					$impuesto = round($sub_total * ($iva / 100), 2);
					$tl_sniva = round($sub_total - $impuesto,2);
					$total = round($tl_sniva + $impuesto,2);

					$detalleTotales = '<tr>
											<td colspan="5" class="textright">SUBTOTAL €</td>
											<td class="textright">'.$tl_sniva.'</td>
										</tr>
										<tr>
											<td colspan="5" class="textright">IVA ('.$iva.'%)</td>
											<td class="textright">'.$impuesto.'</td>
										</tr>
										<tr>
											<td colspan="5" class="textright">TOTAL €</td>
											<td class="textright">'.$total.'</td>
										</tr>';
					$arrayData['detalle'] = $detalleTabla;
					$arrayData['totales'] = $detalleTotales;

					//Retorno el array en formato json 
					var_dump($arrayData);
					echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
				}else{
					echo 'error';
				}
				mysqli_close($conection);
			}
			exit;
		}

				//Eliminar productos de detalle_temp
		if($_POST['action'] == 'delProductoDetalle'){
				if(empty($_POST['id_detalle'])){
				echo 'Error';

			}else{
			
				$id_detalle = $_POST['id_detalle'];
				$id_user = $_SESSION['idUser'];

				$query_iva = mysqli_query($conection,"SELECT iva FROM configuracion");
				$result_iva = mysqli_num_rows($query_iva);

				$query_detalle_temp = mysqli_query($conection,"CALL del_detalle_temp($id_detalle,'$id_user')");
				$result = mysqli_num_rows($query_detalle_temp);


				$detalleTabla = '';
				$sub_total = 0;
				$iva =0;
				$total = 0;
				$arrayData = array();

				if($result > 0){

					if($result_iva > 0){
						//Almaceno en un array los campos de la tabla configuracion
						$info_iva = mysqli_fetch_assoc($query_iva);
						$iva = $info_iva['iva'];

					}
						//Almaceno en un array los campos de la tabla temporal
					while ($data = mysqli_fetch_assoc($query_detalle_temp)){
						//round funcion para redodear y indico q quiero solo 2 decimales
						$precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
						$sub_total = round($sub_total + $precioTotal, 2);
						$total = round($total + $precioTotal, 2);
						// ' . ' para concatenar el valor q ya tiene con las otras filas
						$detalleTabla .= '<tr>
											<td>'.$data['codproducto'].'</td>
											<td colspan="2">'.$data['descripcion'].'</td>
											<td class="textcenter">'.$data['cantidad'].'</td>
											<td class="textright">'.$data['precio_venta'].'</td>
											<td class="textright">'.$precioTotal.'</td>
											<td class="">
												<a class="link_delete" href="#" onclick="event.preventDefault();
													del_product_detalle('.$data['correlativo'].');">Eliminar</a>
											</td>
										</tr>';
					}
					$impuesto = round($sub_total * ($iva / 100), 2);
					$tl_sniva = round($sub_total - $impuesto,2);
					$total = round($tl_sniva + $impuesto,2);

					$detalleTotales = '<tr>
											<td colspan="5" class="textright">SUBTOTAL €</td>
											<td class="textright">'.$tl_sniva.'</td>
										</tr>
										<tr>
											<td colspan="5" class="textright">IVA ('.$iva.'%)</td>
											<td class="textright">'.$impuesto.'</td>
										</tr>
										<tr>
											<td colspan="5" class="textright">TOTAL €</td>
											<td class="textright">'.$total.'</td>
										</tr>';
					$arrayData['detalle'] = $detalleTabla;
					$arrayData['totales'] = $detalleTotales;

					//Retorno el array en formato json 
					echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
				}else{
					echo 'error';
				}
				mysqli_close($conection);
			}
			exit;
		}


		//Anular venta
		if($_POST['action'] == 'anularVenta'){

			$id_user = $_SESSION['idUser'];
			$query_del = mysqli_query($conection,"DELETE FROM detalle_temp WHERE token_usuario = '$id_user'");
			mysqli_close($conection);
			if($query_del){
				echo 'ok';
			}else{
				echo 'error';
			}
			exit;
		}

		//Procesar venta
		if($_POST['action'] == 'procesarVenta'){
			
			if(empty($_POST['codcliente'])){
				$codcliente = 1;
			}else{
				$codcliente = $_POST['codcliente'];
			}

			$id_user = $_SESSION['idUser'];
			$usuario = $_SESSION['idUser'];

			$query = mysqli_query($conection,"SELECT * FROM detalle_temp WHERE token_usuario = '$id_user'");
			$result = mysqli_num_rows($query);
			if($result > 0){
				$query_procesar = mysqli_query($conection,"CALL procesar_venta($usuario,$codcliente,'$id_user')");
				$result_detalle = mysqli_num_rows($query_procesar);

				if($result_detalle > 0){
					$data = mysqli_fetch_assoc($query_procesar);
					echo json_encode($data,JSON_UNESCAPED_UNICODE);

				}else{
					echo "error";
				}	
			}else{
				echo "error";
			}
			mysqli_close($conection);
			exit;
		}

	}
exit;

?>