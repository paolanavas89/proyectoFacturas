<?php
	session_start();
	if($_SESSION['rol'] != 1 and $_SESSION['rol'] != 2){
		header("location: ./");
	} 

	include "../conexion.php";

	if(!empty($_POST)){

		if(empty($_POST['idproveedor'])){
			header("location: lista_proveedor.php");
			mysqli_close($conection);
		}

		$idproveedor = $_POST['idproveedor'];

		$query_delete = mysqli_query($conection,"UPDATE producto SET estatus = 0 WHERE codproducto = $idproducto");
		mysqli_close($conection);

		if($query_delete){
			header("location: lista_proveedor.php");
		}else{
			echo "Error al eliminar";
		}
	}


	if(empty($_REQUEST['id'])){
		header("location: lista_proveedor.php");
		mysqli_close($conection);
	}else{
		
		$idproveedor = $_REQUEST['id'];

		$query = mysqli_query($conection,"SELECT * FROM proveedor WHERE codproveedor =$idproveedor");

		mysqli_close($conection);
		$result = mysqli_num_rows($query);

		if($result > 0){
			while ($data = mysqli_fetch_array($query)){
				$proveedor = $data['proveedor'];
				
			}
		}else{
			header("location: lista_proveedor.php");
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Eliminar proveedor</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		<div class= "delete">
			<h2>¿Está seguro de eliminar el siguiente registro?</h2>
			<p>Nombre del Proveedor: <span><?php echo $proveedor; ?></span></p>

			<form method="post" action="">
				<input type="hidden" name="idproveedor" value="<?php echo $idproveedor; ?>">
				<a href= "lista_proveedor.php" class="btn_cancel">Cancelar</a>
				<button type="submit"class="btn_ok"> Eliminar </button>
			</form>
	</section>
</body>
</html>