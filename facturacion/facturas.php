<?php 
	session_start();	
	include "../conexion.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Lista de Facturas</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<h1>Lista de Facturas</h1>
		<a href="nueva_factura.php" class="btn_new">Nueva factura</a>
		<table>
			<tr>
				<th>No.</th>
				<th>Fecha / Hora</th>
				<th>Cliente</th>
				<th>Vendedor</th>
				<th>Estado</th>
				<th class="textcenter">Total Factura</th>
				<th class="textcenter">Acciones</th>
			</tr>

			<?php

					//Paginador
				$sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM factura WHERE estatus != 10 ");
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

				$query = mysqli_query($conection,"SELECT f.nofactura, f.fecha,f.totalfactura,f.codcliente,f.estatus,u.nombre
												  as vendedor, cl.nombre as cliente FROM factura f INNER JOIN usuario u ON f.usuario = u.idusuario INNER JOIN cliente cl ON f.codcliente = cl.idcliente WHERE f.estatus !=10 ORDER BY f.fecha DESC LIMIT $desde,$por_pagina");

				mysqli_close($conection);

				$result = mysqli_num_rows($query);

				if($result > 0){
					while($data = mysqli_fetch_array($query)){
						if($data['estatus'] == 1){
							$estado = '<span class="pagada">Pagada</span>';

						}else{
							$estado = '<span class="anulada">Anulada</span>';
						}
						
			?>
						<tr id="row <?php echo $data["nofactura"]; ?>">
							<td><?php echo $data['nofactura']; ?></td>
							<td><?php echo $data['fecha']; ?></td>
							<td><?php echo $data['cliente']; ?></td>
							<td><?php echo $data['vendedor']; ?></td>
							<td><?php echo $estado; ?></td>
							<td class="textcenter totalfactura"><?php echo $data["totalfactura"]; ?><span>€</span></td>
							<td>
								<div class="div_acciones">
									<div>
										<button class="btn_view view_factura" type="button" cl="<?php echo $data["codcliente"]; ?>" f="<?php echo $data["nofactura"];?>"><i class="fas fa-eye"></i></button>
									</div>

				
								</div>
							</td>
						</tr>
			<?php
				}

				}
			?>
			
		</table>

		<div class="paginador">
			<ul>
			<?php 
				if($pagina != 1)
				{
			 ?>
				<li><a href="?pagina=<?php echo 1; ?>">|<</a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>"><<</a></li>
			<?php 
				}
				for ($i=1; $i <= $total_paginas; $i++) { 
					# code...
					if($i == $pagina)
					{
						echo '<li class="pageSelected">'.$i.'</li>';
					}else{
						echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
					}
				}

				if($pagina != $total_paginas)
				{
			 ?>
				<li><a href="?pagina=<?php echo $pagina + 1; ?>">>></a></li>
				<li><a href="?pagina=<?php echo $total_paginas; ?> ">>|</a></li>
			<?php } ?>
			</ul>
		</div>
	</section>
</body>
</html>