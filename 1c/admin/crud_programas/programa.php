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
                    <a href="../crud_ficha/fichas.php">
                        <ion-icon name="book-outline"></ion-icon>
                        <span>Ficha</span>
                    </a>
                </li>
                <li>
                    <a href="programa.php">
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
// Incluir el archivo de conexión a la base de datos
include_once('../../conexion.php');
session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol adecuado
if (!isset($_SESSION['email']) || $_SESSION['rol'] != 1) {
    // Si no ha iniciado sesión o no es administrador, redirigir a la página de inicio de sesión
    header("Location: ../../index.php");
    exit();
}
// Inicializar variables
$programas = [];
$mostrar_activos = true;

// Verificar si se ha enviado una búsqueda
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

// Verificar si se ha enviado una solicitud de desactivación
if (isset($_POST['desactivar'])) {
    $codigo_p = $_POST['codigo_p'];

    // Actualizar el estado del programa a 'desactivado'
    $sql = "UPDATE Programas SET estado = 'desactivado' WHERE codigo_p = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigo_p);

    if (!$stmt->execute()) {
        $mensaje= "Error al desactivar el programa: " . $stmt->error ;
    } else {
        $mensaje= "Programa desactivado exitosamente.";
    }

    $stmt->close();
}

// Verificar si se ha enviado una solicitud de activación
if (isset($_POST['activar'])) {
    $codigo_p = $_POST['codigo_p'];

    // Actualizar el estado del programa a 'activo'
    $sql = "UPDATE Programas SET estado = 'activo' WHERE codigo_p = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigo_p);

    if (!$stmt->execute()) {
        $mensaje= "Error al activar el programa: " . $stmt->error;
    } else {
        $mensaje= "Programa activado exitosamente.";
    }

    $stmt->close();
}

// Verificar si se ha enviado una solicitud de eliminación
if (isset($_POST['eliminar'])) {
    $codigo_p = $_POST['codigo_p'];

    // Eliminar el programa
    $sql = "DELETE FROM Programas WHERE codigo_p = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigo_p);

    if (!$stmt->execute()) {
        $mensaje= "Error al eliminar el programa: " . $stmt->error ;
    } else {
        $mensaje= "Programa eliminado exitosamente.";
    }

    $stmt->close();
}

// Verificar si se debe mostrar programas activos o desactivados
if (isset($_GET['mostrar']) && $_GET['mostrar'] === 'desactivados') {
    $mostrar_activos = false;
}

// Consulta para obtener los programas activos o desactivados con filtro de búsqueda
$estado_programa = $mostrar_activos ? 'activo' : 'desactivado';
$sql = "SELECT codigo_p, Nombre, competencias, resultados, estado FROM Programas WHERE Nombre LIKE ? AND estado = ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search_query%";
$stmt->bind_param("ss", $search_param, $estado_programa);

if (!$stmt->execute()) {
    $mensaje= "Error al ejecutar la consulta: " . $stmt->error ;
} else {
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $programas[] = $row;
        }
    } 
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programas</title>
    <link rel="stylesheet" href="../../estilos/styles1.css">
    <style>
        .status-dot {
            height: 10px;
            width: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        .active-dot {
            background-color: green;
        }
        .inactive-dot {
            background-color: red;
        }
    </style>
    <script>
        window.onload = function() {
            <?php if (!empty($mensaje)) : ?>
                alert("<?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>");
            <?php endif; ?>
        }
    </script>
</head>
<body>
    <header>
        <div class="header-bar">
            <div class="logo-container">
                <img src="../../imagenes/sena-logo.svg" alt="Logo SENA">
            </div>
        </div>
    </header>

    <div class="container">
        <div class="header">
            <a href="crear_programa.php" class="btn-add">Agregar Programa</a>
            <form method="POST" action="" style="display: inline;">
                <input type="text" name="search" id="buscador" placeholder="Buscar programa..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="btn-search">Buscar</button>
            </form>
            <div class="toggle-buttons">
                <a href="?mostrar=activos" class="btn-toggle <?php echo $mostrar_activos ? 'active' : ''; ?>">Activos</a>
                <a href="?mostrar=desactivados" class="btn-toggle <?php echo !$mostrar_activos ? 'active' : ''; ?>">Desactivados</a>
            </div>
        </div>
        
        <div class="table-container">
            <h1>Lista de Programas <?php echo $mostrar_activos ? 'Activos' : 'Desactivados'; ?></h1>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Competencias</th>
                        <th>Resultados</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($programas)): ?>
                        <tr>
                            <td colspan="5">No se encontraron programas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($programas as $programa): ?>
                        <tr>
                            <td>
                                <?php if ($programa['estado'] == 'activo'): ?>
                                    <span class="status-dot active-dot"></span>
                                <?php else: ?>
                                    <span class="status-dot inactive-dot"></span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($programa['codigo_p']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($programa['Nombre']); ?></td>
                            <td><?php echo htmlspecialchars($programa['competencias']); ?></td>
                            <td><?php echo htmlspecialchars($programa['resultados']); ?></td>
                            <td>
                                <a href="editar_programa.php?codigo_p=<?php echo urlencode($programa['codigo_p']); ?>" class="btn-edit">✏️ Editar</a>
                                <?php if ($mostrar_activos): ?>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="codigo_p" value="<?php echo htmlspecialchars($programa['codigo_p']); ?>">
                                        <button type="submit" name="desactivar" class="btn-disable">🚫 Desactivar</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="codigo_p" value="<?php echo htmlspecialchars($programa['codigo_p']); ?>">
                                        <button type="submit" name="activar" class="btn-enable">✅ Activar</button>
                                    </form>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="codigo_p" value="<?php echo htmlspecialchars($programa['codigo_p']); ?>">
                                        <button type="submit" name="eliminar" class="btn-delete">🗑️ Eliminar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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
