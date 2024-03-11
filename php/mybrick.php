<?php
include 'connect.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
} else {
    header("Location: ../index.php?error=noAutenticado");
    exit;
}
$conexion = conectar();
// Procesar la actualización de los datos de precio y cantidad
if(isset($_POST['submit'])) {
    if(isset($_POST['precio']) && isset($_POST['cantidad']) && isset($_POST['set_num'])) {
        $precios = $_POST['precio'];
        $cantidades = $_POST['cantidad'];
        $set_nums = $_POST['set_num'];
        foreach($set_nums as $key => $set_num) {
            $precio = $conexion->real_escape_string($precios[$key]);
            $cantidad = $conexion->real_escape_string($cantidades[$key]);
            // Verificar si ya existe una fila para este set_num y usuario
            $sql = "SELECT * FROM usuario_coleccion WHERE username = '$username' AND set_num = '$set_num'";
            $result = $conexion->query($sql);
            if ($result->num_rows > 0) {
                // Si existe, actualizar la fila
                $sql_update = "UPDATE usuario_coleccion SET precio = '$precio', cantidad = '$cantidad' WHERE username = '$username' AND set_num = '$set_num'";
                $conexion->query($sql_update);
            }
        }
        echo "Datos actualizados correctamente.";
        // Después de actualizar, redirige a la misma página
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}
// Procesar la eliminación de sets
if(isset($_POST['eliminar'])) {
    $set_num_eliminar = $_POST['eliminar'];
    deleteSet($set_num_eliminar, $username);
}
// Función para eliminar sets
function deleteSet($set_num_eliminar, $username) {
    $conexion = conectar();
    $query = "DELETE FROM usuario_coleccion WHERE set_num = '$set_num_eliminar' AND username = '$username'";
    $result = $conexion->query($query);
    if ($result) {
        echo "Set eliminado correctamente.";
    } else {
        echo "Error al eliminar el set: " . $conexion->error;
    }
    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Sets</title>
</head>
<body>
    <nav>
        <a href="paginaPerfil.php">Home</a>
        <a href="productos.php">Productos</a>
        <a href="minifigs.php">Minifigs</a>
        <a href="mybrick.php">MyBrick</a>
        <a href="mybrickMinifigs.php">MyMinifigs</a>
        <a href="sesionCerrada.php">Cerrar Sesión</a>
    </nav>
    <?php
    // Consulta para seleccionar los campos de la tabla sets para el usuario actual
    $sql = "SELECT usuario_coleccion.*, sets.img_url, sets.name, sets.year, sets.tema, sets.num_parts FROM usuario_coleccion 
            INNER JOIN sets ON usuario_coleccion.set_num = sets.set_num 
            WHERE usuario_coleccion.username = '$username'
            ORDER BY usuario_coleccion.id DESC"; // Ordenar por el tiempo de inserción descendente
    $resultado = $conexion->query($sql);
    // Comprobar si hay resultados
    if ($resultado->num_rows > 0) {
        // Mostrar los resultados en una tabla
        echo '<form method="post" action="">';
        echo '<table border="1">';
        echo '<tr><th>Set Num</th><th>Name</th><th>Year</th><th>Tema</th><th>Num Parts</th><th>Imagen</th><th>Precio</th><th>Cantidad</th><th>Eliminar</th></tr>';
        
        $total_precio = 0;
        $total_piezas = 0;
        while ($fila = $resultado->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $fila['set_num'] . '</td>';
            echo '<td>' . $fila['name'] . '</td>';
            echo '<td>' . $fila['year'] . '</td>';
            echo '<td>' . $fila['tema'] . '</td>';
            echo '<td>' . $fila['num_parts'] . '</td>';
            echo '<td><a href="' . $fila['img_url'] . '" target="_blank"><img src="' . $fila['img_url'] . '" width="100" height="100"></a></td>';
            echo '<td><input type="text" name="precio[]" value="' . $fila['precio'] . '"></td>';
            echo '<td><input type="text" name="cantidad[]" value="' . $fila['cantidad'] . '"></td>';
            echo '<input type="hidden" name="set_num[]" value="' . $fila['set_num'] . '">';
            echo '<td><button type="submit" name="eliminar" value="' . $fila['set_num'] . '">Eliminar</button></td>';
            echo '</tr>';
            // Calcular el precio total
            $total_precio += $fila['precio'] * $fila['cantidad'];
            // Calcular las piezas totales
            $total_piezas += $fila['num_parts'] * $fila['cantidad'];
        }
        // Agregar fila para mostrar el total
        echo '<tr>';
        echo '<td>Total:</td>';
        echo '<td>' . $total_precio . '€</td>';
        echo '<td>' . $total_piezas . ' Pzs</td>';
        echo '<td></td>'; // Celda vacía para mantener la estructura de la tabla
        echo '</tr>';
        echo '</table>';
        echo '<button type="submit" name="submit">Actualizar</button>';
        echo '</form>';
        // Crear un enlace para descargar como CSV
        echo '<a href="descargar_csv.php">Descargar MyBrick.csv</a>';
    } else {
        echo "No se encontraron resultados.";
    }
    ?>
</body>
</html>
