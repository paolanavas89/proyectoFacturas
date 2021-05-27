<?php
	$alert = '';
	session_start();
	if(!empty($_SESSION['active'])){
		header('location: facturacion/');
	}else{

		if(!empty($_POST)){

			if(empty($_POST['usuario']) || empty($_POST['clave'])){

				$alert = 'Ingrese su usuario y su contraseña';
				
			}else{

					require_once "conexion.php";

					$user = $_POST['usuario'];
					$pass = $_POST['clave'];

					$query = mysqli_query($conection,"SELECT *FROM usuario WHERE usuario= '$user' AND clave='$pass'");
					$result = mysqli_num_rows($query);

					if($result >0){
						
						$data = mysqli_fetch_array($query);
						$_SESSION['active'] = true;
						$_SESSION['idUser'] = $data['idusuario'];
						$_SESSION['nombre'] = $data['nombre'];
						$_SESSION['email'] = $data['correo'];
						$_SESSION['user'] = $data['usuario'];
						$_SESSION['rol'] = $data['rol'];

						header('location: facturacion/');

		}else{
				$alert = 'El usuario o la clave son incorrectos';
				session_destroy();
		}


			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Login | Sistema de facturación</title>
	<link rel="stylesheet" type="text/css" href="./css/style2.css">

</head>
<body>
	 <div class="login-box" >
      <img src="./imagenes/login.png"  class="avatar" class="avatar" alt="Avatar Image">
      <h1>Iniciar Sesión</h1>
		<form action="" method="post">
			<label for="usuario">Username</label>
			<input type="text" name="usuario" placeholder="Usuario">
			<label for="password">Password</label>
			<input type="password" name="clave" placeholder="Contraseña">
			<div class="alert" ><?php echo isset ($alert) ? $alert : ''; ?></div>
			<input type="submit" value="INGRESAR">
		</form>
	</div>
</body>
</html>
