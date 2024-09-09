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
// Incluir la configuración de la base de datos
include_once('../../conexion.php'); // Ajusta la ruta si es necesario
session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol adecuado
if (!isset($_SESSION['email']) || $_SESSION['rol'] != 1) {
    // Si no ha iniciado sesión o no es administrador, redirigir a la página de inicio de sesión
    header("Location: ../../index.php");
    exit();
}

// Obtener el ID de la ficha de alguna manera
$id_ficha = isset($_GET['id_ficha']) ? intval($_GET['id_ficha']) : 0;

if ($id_ficha === 0) {
    die("ID de ficha no válido.");
}

// Obtener los usuarios activos que son instructores (rol 2)
$searchQuery = '';
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}

// Obtener los usuarios ya asignados a la ficha
$assignedQuery = "SELECT codigo_usu FROM usuario_ficha WHERE id_ficha = ?";
$stmt = $conn->prepare($assignedQuery);
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->bind_param('i', $id_ficha);
$stmt->execute();
$resultAssigned = $stmt->get_result();
$assignedUsers = [];
while ($row = $resultAssigned->fetch_assoc()) {
    $assignedUsers[] = $row['codigo_usu'];
}
$stmt->close();

// Preparar la consulta para obtener usuarios disponibles que no están asignados
$inClause = '';
$params = [];
$types = '';

if (!empty($assignedUsers)) {
    // Crear una lista de parámetros para el IN clause
    $inClause = implode(',', array_fill(0, count($assignedUsers), '?'));
    $types = str_repeat('i', count($assignedUsers));
    $params = $assignedUsers;
}

// Añadir los parámetros de búsqueda
$params[] = "%$searchQuery%";
$params[] = "%$searchQuery%";
$types .= 'ss';

$queryUsuarios = "SELECT codigo_usu, nombre, apellido FROM Usuario 
                  WHERE estado = 'activo' AND rol = 2 " .
                  ($inClause ? "AND codigo_usu NOT IN ($inClause) " : "") .
                  "AND (nombre LIKE ? OR apellido LIKE ?)";

$stmt = $conn->prepare($queryUsuarios);
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Enlazar parámetros
$stmt->bind_param($types, ...$params);
$stmt->execute();
$resultUsuarios = $stmt->get_result();

// Verificar la acción de agregar usuarios
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['usuarios'])) {
    $usuariosSeleccionados = $_POST['usuarios'];

    // Agregar usuarios seleccionados a la ficha
    foreach ($usuariosSeleccionados as $codigo_usu) {
        // Verificar si ya existe la relación en la tabla
        $queryCheck = "SELECT * FROM usuario_ficha WHERE codigo_usu = ? AND id_ficha = ?";
        $stmtCheck = $conn->prepare($queryCheck);
        if ($stmtCheck === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmtCheck->bind_param('ii', $codigo_usu, $id_ficha);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $mensaje = "El usuario con código $codigo_usu ya está asignado a la ficha.";
        } else {
            // Si no existe, proceder a la inserción
            $queryInsert = "INSERT INTO usuario_ficha (codigo_usu, id_ficha) VALUES (?, ?)";
            $stmtInsert = $conn->prepare($queryInsert);
            if ($stmtInsert === false) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmtInsert->bind_param('ii', $codigo_usu, $id_ficha);
            if ($stmtInsert->execute()) {
                $mensaje = "Usuario con código $codigo_usu agregado correctamente.";
            } else {
                $mensaje = "Error al agregar usuario con código $codigo_usu: " . $stmtInsert->error;
            }
            $stmtInsert->close();
        }

        $stmtCheck->close();
    }
}

// Verificar la acción de desasignar usuarios
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['desasignar'])) {
    $usuariosDesasignados = $_POST['desasignar'];

    // Desasignar usuarios seleccionados de la ficha
    foreach ($usuariosDesasignados as $codigo_usu) {
        $queryDelete = "DELETE FROM usuario_ficha WHERE codigo_usu = ? AND id_ficha = ?";
        $stmt = $conn->prepare($queryDelete);
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        $stmt->bind_param('ii', $codigo_usu, $id_ficha);
        if ($stmt->execute()) {
            $mensaje = "Usuario con código $codigo_usu desasignado correctamente.";
        } else {
            $mensaje = "Error al desasignar usuario con código $codigo_usu: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Obtener los datos de los usuarios agregados a la ficha
$queryAgregados = "SELECT U.codigo_usu, U.nombre, U.apellido 
                   FROM Usuario U
                   INNER JOIN usuario_ficha UF ON U.codigo_usu = UF.codigo_usu
                   WHERE UF.id_ficha = ? AND U.rol = 2"; // Rol 2 es Instructor
$stmt = $conn->prepare($queryAgregados);
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->bind_param('i', $id_ficha);
$stmt->execute();
$resultAgregados = $stmt->get_result();
$usuariosMostrados = [];
while ($usuario = $resultAgregados->fetch_assoc()) {
    $usuariosMostrados[] = $usuario;
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Usuarios a Ficha</title>
    <link rel="stylesheet" href="estilos/styles.css">
    <style>
        /* Estilos adicionales para mejorar la interfaz */
        .search-form {
            margin-bottom: 20px;
        }

        .search-form input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }

        .search-form input[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-form input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .user-list-container {
            position: relative;
            width: 100%;
        }

        .user-list {
            display: flex;
            flex-direction: column;
            border: 1px solid #ccc;
            border-radius: 4px;
            max-height: 300px;
            overflow-y: auto;
            padding: 5px;
            box-sizing: border-box;
            background-color: #fff;
        }

        .user-list-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }

        .user-list-item:hover {
            background-color: #f1f1f1;
        }

        .user-form input[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #28a745;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }

        .user-form input[type="submit"]:hover {
            background-color: #218838;
        }

        .added-users {
            list-style: none;
            padding: 0;
        }

        .added-users li {
            background-color: #e9ecef;
            margin: 5px 0;
            padding: 10px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
        }

        .added-users li button {
            background-color: #dc3545;
            border: none;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .added-users li button:hover {
            background-color: #c82333;
        }

        .asignar-lider-btn {
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
        }

        .asignar-lider-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Agregar Usuarios a la Ficha</h1>

        <?php if (isset($mensaje)) : ?>
            <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form method="post" action="" class="search-form">
            <input type="text" name="search" placeholder="Buscar usuarios..." value="<?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="submit" value="Buscar">
        </form>

        <form method="post" action="">
            <div class="user-list-container">
                <div class="user-list">
                    <?php while ($usuario = $resultUsuarios->fetch_assoc()) : ?>
                        <div class="user-list-item">
                            <input type="checkbox" name="usuarios[]" value="<?php echo $usuario['codigo_usu']; ?>">
                            <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <input type="submit" value="Agregar Usuarios">
        </form>

        <h2>Usuarios Asignados</h2>
        <ul class="added-users">
            <?php foreach ($usuariosMostrados as $usuario) : ?>
                <li>
                    <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido'], ENT_QUOTES, 'UTF-8'); ?>
                    <form method="post" action="" style="display:inline;">
                        <input type="hidden" name="desasignar[]" value="<?php echo $usuario['codigo_usu']; ?>">
                        <input type="hidden" name="id_ficha" value="<?php echo $id_ficha; ?>">
                        <input type="submit" value="Desasignar">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <form method="get" action="asignar_liderF.php" style="margin-top: 20px;">
            <input type="hidden" name="id_ficha" value="<?php echo $id_ficha; ?>">
            <input type="submit" class="asignar-lider-btn" value="Asignar Líder">
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