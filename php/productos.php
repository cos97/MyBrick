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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Productos</title>
</head>
    <nav>
        <a href="paginaPerfil.php"><i></i> Home</a>
        <a href="productos.php"><i></i> Productos</a>
        <a href="minifigs.php"><i></i> Minifigs</a>
        <a href="mybrick.php"><i></i> MyBrick</a>
        <a href="mybrickMinifigs.php"><i></i> MyMinifigs</a>
        <a href="sesionCerrada.php"><i></i> Cerrar Sesión</a>
    </nav>
    <form action="productos.php" method="post" id="filterForm">
        <input type="text" name="search" id="search" placeholder="Buscar por nombre:">
        <label for="search">Buscar por nombre:</label>
        <input type="text" name="set_num_search" id="set_num_search" placeholder="Buscar por set_num:">
        <label for="set_num_search">Buscar por set_num:</label>
        <select name="order" id="order">
            <option value="piezas_desc">Piezas (desc)</option>
            <option value="piezas_asc">Piezas (asc)</option>
        </select>
        <button type="submit">Aplicar Filtros</button>
    </form>
    <div>
        <!-- Aquí se mostrarán los resultados -->
        <?php
        // Inicializar los filtros
        $order_sql = "";
        $productos_por_pagina = isset($_POST["paginas"]) ? intval($_POST['paginas']) : 100;
        $total_productos = 22340;
        // Página actual (obtener de parámetro GET, si no está establecido, por defecto será la página 1)
        

        // Página actual (obtener de parámetro GET, si no está establecido, por defecto será la página 1)
        $pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
        if (isset($_POST["order"])) {
            $order = $conexion->real_escape_string($_POST["order"]);
            switch ($order) {
                case "piezas_asc":
                    $order_sql = " ORDER BY num_parts ASC";
                    break;
                case "piezas_desc":
                    $order_sql = " ORDER BY num_parts DESC";
                    break;
            }
        }
        // Construir la consulta SQL
        
        $sql = "SELECT * FROM sets";
        // Aplicar el filtro de búsqueda por nombre
        
        if (isset($_POST["search"]) && !empty($_POST["search"])) {
            // Obtener el término de búsqueda
        
            $search_term = $conexion->real_escape_string($_POST["search"]);
            // Añadir el filtro a la consulta SQL
        
            $sql .= " WHERE name LIKE '%$search_term%'";
            // Construir la consulta SQL para contar el número total de productos después de aplicar el filtro de búsqueda
        
            $sql_count_filtered = "SELECT COUNT(*) AS total FROM sets WHERE name LIKE '%$search_term%'";
            $result_count_filtered = $conexion->query($sql_count_filtered);
            $row_count_filtered = $result_count_filtered->fetch_assoc();
            $total_productos = $row_count_filtered['total'];
        }
        // Aplicar el filtro de búsqueda por set_num
        
        if (isset($_POST["set_num_search"]) && !empty($_POST["set_num_search"])) {
            // Obtener el set_num de búsqueda
        
            $set_num_search = $conexion->real_escape_string($_POST["set_num_search"]);
            // Agregar el filtro a la consulta SQL
        
            $sql .= (strpos($sql, 'WHERE') !== false) ? " AND set_num LIKE '%$set_num_search%'" : " WHERE set_num LIKE '%$set_num_search%'";
        }
        // Añadir la cláusula ORDER BY si hay un criterio de ordenamiento
        
        if (!empty($order_sql)) {
            $sql .= $order_sql;
        }
        // Añadir la cláusula LIMIT
        
        $sql .= " LIMIT $productos_por_pagina";
        // Imprimir la consulta SQL final para verificar su estructura
        
        echo "Consulta SQL: $sql<br>";
        // Realizar la consulta SQL para obtener los productos de la página actual
        
        $resultado = $conexion->query($sql);
        // Mostrar el número de productos encontrados
        
        echo "Se encontraron $total_productos productos.";
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                // Mostrar productos como lo estás haciendo en tu código actual
        
                echo '<div>';
                echo '<div id="product-added"></div>';
                echo '<a href="' . $fila['img_url'] . '" target="_blank"><img src="' . $fila['img_url'] . '" width="100" height="100"></a>';
                echo '<div>';
                echo '<p>' . $fila['name'] . '</p>';
                echo '<div>';
                echo '<p>' . $fila['set_num'] . ', ' . $fila['year'] . ', ' . $fila['tema'] . ', ' . $fila['num_parts'] . 'pzs</p>';
                echo '</div>';
                echo '</div>';
                echo '<div>';
                echo '<form action="addColeccion.php" method="post">';
                echo '<input type="hidden" name="set_num" value="' . $fila['set_num'] . '">';
                echo '<input type="hidden" name="user_nick" value="' . $username . '">';
                echo '<button type="submit">Añadir a Mi Colección</button>';
                echo '</form>';
                echo '<br><br>';
                echo '</div>';
                echo '</div>';
            }
            // Calcular el número total de páginas
        
            $total_paginas = ceil($total_productos / $productos_por_pagina);
            // Mostrar enlaces de paginación
        
            echo '<div class="pagination">';
            for ($i = 1; $i <= $total_paginas; $i++) {
                if ($i == $pagina_actual) {
                    echo '<span class="current">' . $i . '</span>';
                }
            }
            echo '</div>';
        } else {
            echo "No se encontraron productos.";
        }
        // Cerrar la conexión a la base de datos
        
        cerrarConexion($conexion);
        ?>
    </div>
</body>
</html>