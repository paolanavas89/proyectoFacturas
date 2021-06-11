<?php 
	session_start();
	include "../conexion.php";	

 ?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Lista de productos</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<?php 
			//convierte la variable en minuscula 
			$busqueda = strtolower($_REQUEST['busqueda']);
			if(empty($busqueda))
			{
				header("location: lista_productos.php");
				mysqli_close($conection);
			}


		 ?>
		
		<h1><i class="far fa-building"></i>Lista de productos</h1>
		<a href="registro_producto.php" class="btn_new">Crear proveedor</a>
		
		<form action="buscar_productos.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar" value="<?php echo $busqueda; ?>">
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
		</form>

		<table>
			<tr>
				<th>Código</th>
				<th>Descripción</th>
				<th>Precio</th>
				<th>Existencia</th>
				<th>Proveedor</th>
				<th>Foto</th>
				<th>Acciones</th>
			</tr>
		<?php 
			//Paginador
			$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM producto 
																WHERE ( codproducto LIKE '%$busqueda%' OR 
																		descripcion LIKE '%$busqueda%' 
																	   ) 
																AND estatus = 1  ");

			$result_register = mysqli_fetch_array($sql_registe);
			$total_registro = $result_register['total_registro'];

			$por_pagina = 5;

			if(empty($_GET['pagina']))
			{
				$pagina = 1;
			}else{
				$pagina = $_GET['pagina'];
			}

			$desde = ($pagina-1) * $por_pagina;
			$total_paginas = ceil($total_registro / $por_pagina);

			$query = mysqli_query($conection,"SELECT * FROM producto WHERE 
											( codproducto LIKE '%$busqueda%' OR 
											  descripcion LIKE '%$busqueda%' 
											  ) 
										AND
										estatus = 1 ORDER BY codproducto ASC LIMIT $desde,$por_pagina 
				");
			mysqli_close($conection);
			$result = mysqli_num_rows($query);
			if($result > 0){

				while ($data = mysqli_fetch_array($query)) {
					if($data["foto"] != 'img_producto.png'){
							$foto = 'img/'.$data['foto'];
						}else{
							$foto = 'img/'.$data['foto'];
						}
					
			?>
				<tr class="row<?php echo $data['codproducto']; ?>">
					<td><?php echo $data['codproducto']; ?></td>
					<td><?php echo $data['descripcion']; ?></td>
					<td class="celPrecio"><?php echo $data['precio']; ?></td>
					<td class="celExistencia"><?php echo $data['existencia']; ?></td>
					<td><?php echo $data['proveedor']; ?></td>
					<td class="img_producto"><img src="<?php echo $foto; ?>" alt="<?php echo $data['descripcion']; ?>"></td>
						
				<?php if($_SESSION['rol']==1 || $_SESSION['rol']==2){ ?>
					<td>	
						<a class="link_add add_producto" product="<?php echo $data['codproducto']; ?>" href="#">Agregar</a>				|
						<a class="link_edit" href="editar_producto.php?id=<?php echo $data['codproducto']; ?>">Editar</a>
							|
						<a class="link_delete del_product" href="#" product="<?php echo $data['codproducto']; ?>">Eliminar</a>
					</td>	
				<?php } ?>
							
				</tr>
			
		<?php 
				}

			}
		 ?>


		</table>
<?php 
	
	if($total_registro != 0)
	{
 ?>
		<div class="paginador">
			<ul>
			<?php 
				if($pagina != 1)
				{
			 ?>
				<li><a href="?pagina=<?php echo 1; ?>&busqueda=<?php echo $busqueda; ?>">|<</a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>&busqueda=<?php echo $busqueda; ?>"><<</a></li>
			<?php 
				}
				for ($i=1; $i <= $total_paginas; $i++) { 
					# code...
					if($i == $pagina)
					{
						echo '<li class="pageSelected">'.$i.'</li>';
					}else{
						echo '<li><a href="?pagina='.$i.'&busqueda='.$busqueda.'">'.$i.'</a></li>';
					}
				}

				if($pagina != $total_paginas)
				{
			 ?>
				<li><a href="?pagina=<?php echo $pagina + 1; ?>&busqueda=<?php echo $busqueda; ?>">>></a></li>
				<li><a href="?pagina=<?php echo $total_paginas; ?>&busqueda=<?php echo $busqueda; ?> ">>|</a></li>
			<?php } ?>
			</ul>
		</div>
<?php } ?>


	</section>
	
</body>
</html>