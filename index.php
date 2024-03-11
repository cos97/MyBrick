<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>MyBrick</title>
		
	</head>
	<body>
		<div>
			<div>
				<form action="../php/iniciar.php" method="post">
					<header>
						<h1>MyBrick</h1>
						<h3>Iniciar Sesión</h3>
					</header>
					<div>
						<input type="text" name="username" required placeholder="Usuario">
						<label for="usuario">Usuario</label>
					</div>
					<div>
						<input type="password" name="password" required placeholder="Contraseña">
						<label for="contraseña">Contraseña</label>
					</div>
					<div>
						<input type="submit" value="Iniciar sesión">
					</div>
				</form>
			</div>
		</div>
		<div>
          <p>¿Todavía no estás registrado? <a href="/php/crear.php">Crea una nueva cuenta</a></p>
        </div>


		<script>
		// Comprobar si se redirige con un error
		const params = new URLSearchParams(window.location.search);
		const error = params.get("error");
		if (error === "user_exists") {
		alert("Ya existe un usuario con ese nombre");
		}
		if (error === "invalid") {
		alert("Datos incorrectos");
		}
		if (error === "noEncontrado") {
		alert("Usuario no encontrado");
		}
		if (error === "PassNoEncontrado") {
		alert("Password incorrecta");
		}
		if (error === "sesionCerrada") {
		alert("Sesión cerrada por inactividad");
		}
		if (error === "noAutentificado") {
		alert("Tienes que autentificarte");
		}
		if (error === "despedida") {
		alert("Esperamos verte pronto, que tengas un buen día");
		}
		</script>
</body>
</html>