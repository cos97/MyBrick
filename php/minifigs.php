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

<body>
    <nav>
        <a href="paginaPerfil.php"><i></i> Home</a>
        <a href="productos.php"><i></i> Productos</a>
        <a href="minifigs.php"><i></i> Minifigs</a>
        <a href="mybrick.php"><i></i> MyBrick</a>
        <a href="mybrickMinifigs.php"><i></i> MyMinifigs</a>
        <a href="sesionCerrada.php"><i></i> Cerrar Sesión</a>
    </nav>

    <form action="minifigs.php" method="post" id="filterForm">
        <label for="search">Buscar por nombre:</label>
        <input type="text" name="search" id="search" placeholder="Buscar por nombre:">

        <label for="set_num_search">Buscar por tema:</label>
        <input type="text" name="tema_search" id="tema_search" placeholder="Buscar por tema:">

        <label for="search">Buscar por set_num:</label>
        <input type="text" name="set_num_search" id="search" placeholder="Buscar por set_num:">

        <select name="order" id="order">
            <option value="year_asc">Year (asc)</option>
            <option value="year_desc">Year (desc)</option>
        </select>

        <button type="submit">Aplicar Filtros</button>
    </form>

    <div>
        <!-- Aquí se mostrarán los resultados -->
        <?php
        // Inicializar los filtros
        $order_sql = "";
        $productos_por_pagina = isset($_POST["paginas"]) ? intval($_POST['paginas']) : 100;
        $total_productos = 0;

        // Página actual (obtener de parámetro GET, si no está establecido, por defecto será la página 1)
        $pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

        if (isset($_POST["order"])) {
            $order = $conexion->real_escape_string($_POST["order"]);
            switch ($order) {
                case "year_asc":
                    $order_sql = " ORDER BY year ASC";
                    break;
                case "year_desc":
                    $order_sql = " ORDER BY year DESC";
                    break;
            }
        }

        // Construir la consulta SQL
        $sql = "SELECT * FROM relacion";

        // Recuperar parámetros de búsqueda del formulario si existen
        $search_term = isset($_POST["search"]) ? $conexion->real_escape_string($_POST["search"]) : "";
        $tema_search = isset($_POST["tema_search"]) ? $conexion->real_escape_string($_POST["tema_search"]) : "";
        $set_num_search = isset($_POST["set_num_search"]) ? $conexion->real_escape_string($_POST["set_num_search"]) : "";

        // Aplicar los filtros de búsqueda
        if (!empty($search_term) || !empty($tema_search) || !empty($set_num_search)) {
            $sql .= " WHERE 1=1"; // Utilizamos "1=1" para evitar problemas de concatenación de cláusulas WHERE
            if (!empty($search_term)) {
                $sql .= " AND name LIKE '%$search_term%'";
            }
            if (!empty($tema_search)) {
                $sql .= " AND tema LIKE '%$tema_search%'";
            }
            if (!empty($set_num_search)) {
                $sql .= " AND set_num LIKE '%$set_num_search%'";
            }
        }

        // Añadir la cláusula ORDER BY si hay un criterio de ordenamiento
        if (!empty($order_sql)) {
            $sql .= $order_sql;
        }

        // Añadir la cláusula LIMIT
        $sql .= " LIMIT $productos_por_pagina";

        // Realizar la consulta SQL para obtener los productos de la página actual
        $resultado = $conexion->query($sql);

        // Contar el número total de productos después de aplicar los filtros
        $total_productos = $resultado->num_rows;

        // Mostrar el número de productos encontrados
        echo "Se encontraron $total_productos productos.";

        if ($total_productos > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                // Mostrar productos como lo estás haciendo en tu código actual
                echo '<div>';
                echo '<div id="product-added"></div>';
                echo '<a href="' . $fila['img_url'] . '" target="_blank"><img src="' . $fila['img_url'] . '" width="100" height="100"></a>';
                echo '<div>';
                echo '<p>Name: ' . $fila['name'] . '</p>';
                echo '<p>Fig Num: ' . $fila['fig_num'] . '</p>';
                echo '<p>Tema: ' . $fila['tema'] . '</p>';
                echo '<p>Year: ' . $fila['year'] . '</p>';
                echo '<p>Aparece en: ' . $fila['set_num'] . '   ' . $fila['name_set'] . '</p>';
                echo '</div>';
                echo '</div>';
                echo '<div>';
                echo '<form action="addminifigs.php" method="post" class="add-to-collection">';
                echo '<input type="hidden" name="fig_num" value="' . $fila['fig_num'] . '">';
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
                } else {
                    echo '<a href="?pagina=' . $i . '">' . $i . '</a>';
                }
            }
            echo '</div>';
        } else {
            echo "No se encontraron productos.";
        }

        // Cerrar la conexión a la base de datos
        $conexion->close();
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>

        $(document).ready(function () {

            $('form.add-to-collection').submit(function (event) {
                event.preventDefault(); // Evitar el envío del formulario por defecto

                var form = $(this);
                var formData = form.serialize(); // Obtener los datos del formulario

                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: formData,

                    success: function (response) {
                        // Manejar la respuesta del servidor
                        console.log(response); // Puedes mostrar un mensaje de éxito o hacer otras acciones
                    },

                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Error al enviar la solicitud: ' + textStatus);
                    }
                });
            });
        });

    </script>
</body>

</html>