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
            <form action="../php/add_user.php" method="post">
                <header>
                    <h1>MyBrick</h1>
                    <h3>Crear Cuenta</h3>
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
                    <input type="password" name="passwordRepeat" required placeholder="Repetir contraseña">
                    <label for="contraseña">Repetir contraseña</label>
                </div>
                <div>
                    <input type="submit" value="Registrar">
                </div>
            </form>
        </div>
    </div>
    <div>
        <p>¿Ya tienes cuenta? <a href="../index.php">Inicia sesión</a></p>
    </div>
</body>
</html>