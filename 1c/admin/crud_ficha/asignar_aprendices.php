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
    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Aprendices a Ficha</title>
    <link rel="stylesheet" href="../../estilos/styles1.css">
</head>
<body>
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

    // Obtener el ID de la ficha desde la URL
    $id_ficha = isset($_GET['id_ficha']) ? intval($_GET['id_ficha']) : 0;
    if ($id_ficha <= 0) {
        die("ID de ficha inválido.");
    }

    // Obtener el código del programa asociado a la ficha
    $queryFicha = "SELECT codigo_p FROM Ficha WHERE id_ficha = ?";
    $stmt = $conn->prepare($queryFicha);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param('i', $id_ficha);
    $stmt->execute();
    $resultFicha = $stmt->get_result();
    $fichaData = $resultFicha->fetch_assoc();
    $codigo_p = $fichaData['codigo_p'];
    $stmt->close();

    // Obtener los aprendices activos
    $searchQuery = '';
    if (isset($_POST['search'])) {
        $searchQuery = $_POST['search'];
    }

    // Obtener los aprendices ya asignados a la ficha
    $assignedQuery = "SELECT id_aprendiz FROM ficha_aprendiz WHERE id_ficha = ?";
    $stmt = $conn->prepare($assignedQuery);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param('i', $id_ficha);
    $stmt->execute();
    $resultAssigned = $stmt->get_result();
    $assignedUsers = [];
    while ($row = $resultAssigned->fetch_assoc()) {
        $assignedUsers[] = $row['id_aprendiz'];
    }
    $stmt->close();

    // Preparar la consulta para obtener aprendices disponibles que no están asignados
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

    $queryAprendices = "SELECT id_aprendiz, nombre, apellido, codigo_p 
                        FROM Aprendiz 
                        WHERE estado = 'activo' " .
                        ($inClause ? "AND id_aprendiz NOT IN ($inClause) " : "") .
                        "AND (nombre LIKE ? OR apellido LIKE ?)";

    $stmt = $conn->prepare($queryAprendices);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    // Enlazar parámetros
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $resultAprendices = $stmt->get_result();

    // Verificar la acción de agregar aprendices
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['aprendices'])) {
        $aprendicesSeleccionados = $_POST['aprendices'];

        // Agregar aprendices seleccionados a la ficha
        foreach ($aprendicesSeleccionados as $id_aprendiz) {
            // Verificar si el aprendiz ya está asignado a la ficha
            $checkQuery = "SELECT COUNT(*) as count FROM ficha_aprendiz WHERE id_aprendiz = ? AND id_ficha = ?";
            $stmt = $conn->prepare($checkQuery);
            $stmt->bind_param('ii', $id_aprendiz, $id_ficha);
            $stmt->execute();
            $resultCheck = $stmt->get_result();
            $row = $resultCheck->fetch_assoc();

            if ($row['count'] == 0) {
                // Si no está asignado, agregarlo
                $queryInsert = "INSERT INTO ficha_aprendiz (id_aprendiz, id_ficha) VALUES (?, ?)";
                $stmt = $conn->prepare($queryInsert);
                if ($stmt === false) {
                    die("Error en la preparación de la consulta: " . $conn->error);
                }
                $stmt->bind_param('ii', $id_aprendiz, $id_ficha);
                if ($stmt->execute()) {
                    $mensaje = "Aprendiz con ID $id_aprendiz agregado correctamente.<br>";
                } else {
                    $mensaje = "Error al agregar aprendiz con ID $id_aprendiz: " . $stmt->error . "<br>";
                }
                $stmt->close();
            } else {
                $mensaje = "El aprendiz con ID $id_aprendiz ya está asignado a esta ficha.<br>";
            }
        }
    }

    // Verificar la acción de desasignar aprendices
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['desasignar'])) {
        $aprendicesDesasignados = $_POST['desasignar'];

        // Desasignar aprendices seleccionados de la ficha
        foreach ($aprendicesDesasignados as $id_aprendiz) {
            $queryDelete = "DELETE FROM ficha_aprendiz WHERE id_aprendiz = ? AND id_ficha = ?";
            $stmt = $conn->prepare($queryDelete);
            if ($stmt === false) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }
            $stmt->bind_param('ii', $id_aprendiz, $id_ficha);
            if ($stmt->execute()) {
                $mensaje = "Aprendiz con ID $id_aprendiz desasignado correctamente.<br>";
            } else {
                $mensaje = "Error al desasignar aprendiz con ID $id_aprendiz: " . $stmt->error . "<br>";
            }
            $stmt->close();
        }
    }

    // Obtener los datos de los aprendices agregados a la ficha
    $queryAgregados = "SELECT A.id_aprendiz, A.nombre, A.apellido 
                       FROM Aprendiz A
                       INNER JOIN ficha_aprendiz FA ON A.id_aprendiz = FA.id_aprendiz
                       WHERE FA.id_ficha = ?";
    $stmt = $conn->prepare($queryAgregados);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param('i', $id_ficha);
    $stmt->execute();
    $resultAgregados = $stmt->get_result();
    $aprendicesMostrados = [];
    while ($aprendiz = $resultAgregados->fetch_assoc()) {
        $aprendicesMostrados[] = $aprendiz;
    }
    $stmt->close();
    ?>

    <div class="container">
        <h1>Agregar Aprendices a la Ficha</h1>
        <form class="search-form" method="post">
            <label for="search">Buscar Aprendices:</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <input type="submit" value="Buscar">
        </form>

        <form method="post" class="user-form">
            <h2>Aprendices Disponibles</h2>
            <div class="user-list-container">
                <div class="user-list">
                    <?php while ($row = $resultAprendices->fetch_assoc()) { ?>
                        <div class="user-list-item">
                            <input type="checkbox" name="aprendices[]" value="<?php echo $row['id_aprendiz']; ?>">
                            <?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <input type="hidden" name="codigo_p" value="<?php echo htmlspecialchars($codigo_p); ?>">
            <input type="submit" value="Agregar Aprendices">
        </form>

        <h2>Aprendices Agregados a la Ficha</h2>
        <form method="post" class="user-form">
            <div class="user-list-container">
                <div class="user-list">
                    <?php foreach ($aprendicesMostrados as $aprendiz) { ?>
                        <div class="user-list-item">
                            <input type="checkbox" name="desasignar[]" value="<?php echo $aprendiz['id_aprendiz']; ?>">
                            <?php echo htmlspecialchars($aprendiz['nombre'] . ' ' . $aprendiz['apellido']); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <input type="submit" value="Desasignar Aprendices">
        </form>

        <?php
        if (isset($mensaje)) {
            echo "<div class='mensaje'>" . $mensaje . "</div>";
        }
        ?>

       <form method="get" action="asignar_vocero.php" style="margin-top: 20px;">
            <input type="hidden" name="id_ficha" value="<?php echo $id_ficha; ?>">
            <input type="submit" class="asignar-lider-btn" value="Asignar vocero">
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