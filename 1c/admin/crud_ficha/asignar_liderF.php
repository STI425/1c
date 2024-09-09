<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
    <link rel="stylesheet" href="../../estilos/despegable.css">
</head>

<body>
    <div class="menu">
        <ion-icon name="menu-outline"></ion-icon>
        <ion-icon name="close-outline"></ion-icon>
    </div>

    <div class="barra-lateral">
        <div>
            <div class="nombre-pagina">
                <img id="logo" src="../../imagenes/sena-logo.svg" alt="Logo">
                <span>SENA</span>
            </div>
        </div>

        <nav class="navegacion">
            <ul>
                <li>
                    <a href="../crud_usuarios/usuarios.php">
                        <ion-icon name="person-outline"></ion-icon>
                        <span>Instructores</span>
                    </a>
                </li>
                
                <li>
                    <a href="fichas.php">
                        <ion-icon name="book-outline"></ion-icon>
                        <span>Ficha</span>
                    </a>
                </li>
                <li>
                    <a href="../crud_programas/programa.php">
                        <ion-icon name="document-outline"></ion-icon>
                        <span>Programa</span>
                    </a>
                </li>
                <li>
                    <a href="../crud_aprendices/aprendices.php">
                        <ion-icon name="people-outline"></ion-icon>
                        <span>Aprendices</span>
                    </a>
                </li>
                <li>
                    <a href="../crud_ambiente/ambientes.php">
                        <ion-icon name="home-outline"></ion-icon>
                        <span>Ambiente</span>
                    </a>
                </li>
            </ul>
            
        </nav>

        <div>
            <div class="linea"></div>

            <div class="modo-oscuro">
                <div class="info">
                    <ion-icon name="moon-outline"></ion-icon>
                    <span>Dark Mode</span>
                </div>
                <div class="switch">
                    <div class="base">
                        <div class="circulo"></div>
                    </div>
                </div>
            </div>

            <div class="usuario">
                <img src="img/avatar-user-x640.jpg" alt="">
                <div class="info-usuario">
                    <div class="nombre-email">
                        <span class="nombre">User</span>
                        <span class="email">user@mail.com</span>
                    </div>
                    <ion-icon name="ellipsis-vertical-outline"></ion-icon>
                </div>
            </div>
        </div>
    </div>

    <main id="main-content">
    <?php
// Iniciar la sesión o reanudarla
session_start();

// Incluir el archivo de conexión
require_once('../../conexion.php');

// Verificar si se ha enviado el formulario
if (isset($_POST['asignar_lider'])) {
    $codigo_usu_lider = $_POST['codigo_usu_lider'];
    $id_ficha = $_GET['id_ficha'];

    if (!empty($codigo_usu_lider)) {
        // Actualizar el líder de la ficha
        $queryUpdateLider = "UPDATE Ficha SET id_lider = ? WHERE id_ficha = ?";
        $stmt = $conn->prepare($queryUpdateLider);
        $stmt->bind_param('ii', $codigo_usu_lider, $id_ficha);
        if ($stmt->execute()) {
            $mensaje = "Líder asignado correctamente.";
        } else {
            $mensaje = "Error al asignar el líder.";
        }
        $stmt->close();
    } else {
        $mensaje = "Debe seleccionar un líder.";
    }
}

// Obtener el ID de la ficha
$id_ficha = $_GET['id_ficha'];

// Obtener el líder actual
$queryLiderActual = "SELECT U.nombre, U.apellido FROM Usuario U 
                    INNER JOIN Ficha F ON U.codigo_usu = F.id_lider 
                    WHERE F.id_ficha = ?";
$stmt = $conn->prepare($queryLiderActual);
$stmt->bind_param('i', $id_ficha);
$stmt->execute();
$resultLiderActual = $stmt->get_result();
$liderActual = $resultLiderActual->fetch_assoc();
$stmt->close();

// Manejo del buscador
$searchQuery = "";
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}

// Consulta para obtener los instructores activos no asignados a la ficha
$queryInstructores = "SELECT codigo_usu, nombre, apellido 
                      FROM Usuario 
                      WHERE rol = 2 AND estado = 'activo' 
                      AND (nombre LIKE ? OR apellido LIKE ?)";
$searchPattern = '%' . $searchQuery . '%';
$stmt = $conn->prepare($queryInstructores);
$stmt->bind_param('ss', $searchPattern, $searchPattern);
$stmt->execute();
$resultInstructores = $stmt->get_result();
$stmt->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Líder a la Ficha</title>
    <link rel="stylesheet" href="../../estilos/styles1.css">
    <style>
        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }

        h1 {
            color: #333;
            font-family: 'Arial', sans-serif;
        }

        .user-list-container {
            position: relative;
            width: 100%;
            margin-bottom: 30px;
        }

        .user-list {
            display: flex;
            flex-direction: column;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            box-sizing: border-box;
            background-color: #fff;
        }

        .user-list-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            font-size: 16px;
        }

        .user-list-item:hover {
            background-color: #f1f1f1;
        }

        .user-list-item input[type="radio"] {
            margin-right: 10px;
        }

        .actions {
            margin-top: 20px;
        }

        .actions input[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }

        .actions input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        window.onload = function () {
            <?php if (!empty($mensaje)) : ?>
                alert("<?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>");
            <?php endif; ?>
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>Asignar Líder a la Ficha</h1>

        <!-- Mostrar el líder actual -->
        <h2>Líder Actual: <?php echo !empty($liderActual) ? htmlspecialchars($liderActual['nombre']) . ' ' . htmlspecialchars($liderActual['apellido']) : 'Ninguno'; ?></h2>

        <!-- Mostrar los instructores asignados a la ficha -->
        <h2>Instructores Asignados:</h2>
        <ul>
            <?php
            // Consulta para obtener los instructores asignados a la ficha actual
            $queryInstructoresAsignados = "SELECT U.codigo_usu, U.nombre, U.apellido
                                           FROM Usuario U
                                           INNER JOIN usuario_ficha UF ON U.codigo_usu = UF.codigo_usu
                                           WHERE UF.id_ficha = ? AND U.rol = 2 AND U.estado = 'activo'";
            $stmt = $conn->prepare($queryInstructoresAsignados);
            $stmt->bind_param('i', $id_ficha);
            $stmt->execute();
            $resultInstructoresAsignados = $stmt->get_result();

            if ($resultInstructoresAsignados->num_rows > 0) {
                while ($instructorAsignado = $resultInstructoresAsignados->fetch_assoc()) {
                    echo '<li>' . htmlspecialchars($instructorAsignado['nombre']) . ' ' . htmlspecialchars($instructorAsignado['apellido']) . '</li>';
                }
            } else {
                echo '<li>No hay instructores asignados a esta ficha.</li>';
            }
            $stmt->close();
            ?>
        </ul>

        <!-- Formulario de búsqueda -->
        <form method="post" action="" class="search-form">
            <label for="search">Buscar Instructores:</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Buscar por nombre o apellido">
            <input type="submit" value="Buscar">
        </form>

        <!-- Formulario para seleccionar un nuevo líder -->
        <form method="post" action="">
            <label for="codigo_usu_lider">Seleccionar Líder:</label>
            <select id="codigo_usu_lider" name="codigo_usu_lider">
                <option value="">Seleccione un instructor</option>
                <?php while ($instructor = $resultInstructores->fetch_assoc()) : ?>
                    <option value="<?php echo $instructor['codigo_usu']; ?>">
                        <?php echo htmlspecialchars($instructor['nombre']) . ' ' . htmlspecialchars($instructor['apellido']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="submit" name="asignar_lider" value="Asignar Líder">
        </form>

    </div>
</body>

</html>



<a href="../../logout.php" class="boton-cerrar-sesion">Cerrar Sesión</a>
    </main>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../../script/js.js"></script>
</body>

</html>