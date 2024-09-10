

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
                    <a href="ambientes.php">
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
// Verificar si el usuario ha iniciado sesión y tiene el rol adecuado
session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol adecuado
if (!isset($_SESSION['email']) || $_SESSION['rol'] != 1) {
    // Si no ha iniciado sesión o no es administrador, redirigir a la página de inicio de sesión
    header("Location: ../../index.php");
    exit();
}
// Inicializar variables
$ambientes = [];
$mostrar_activos = true;

// Verificar si se ha enviado una búsqueda
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

// Verificar si se ha enviado una solicitud de desactivación
if (isset($_POST['desactivar'])) {
    $id_ambiente = $_POST['id_ambiente'];

    // Actualizar el estado del ambiente a 'desactivado'
    $sql = "UPDATE Ambiente SET estado = 'desactivado' WHERE id_ambiente = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $id_ambiente);

    if (!$stmt->execute()) {
        $mensaje="Error al desactivar el ambiente: " . $stmt->error ;
    } else {
        $mensaje= "Ambiente desactivado exitosamente.";
    }

    $stmt->close();
}

// Verificar si se ha enviado una solicitud de activación
if (isset($_POST['activar'])) {
    $id_ambiente = $_POST['id_ambiente'];

    // Actualizar el estado del ambiente a 'activo'
    $sql = "UPDATE Ambiente SET estado = 'activo' WHERE id_ambiente = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $id_ambiente);

    if (!$stmt->execute()) {
        $mensaje= "Error al activar el ambiente: " . $stmt->error ;
    } else {
        $mensaje="Ambiente activado exitosamente.";
    }

    $stmt->close();
}

// Verificar si se ha enviado una solicitud de eliminación
if (isset($_POST['eliminar'])) {
    $id_ambiente = $_POST['id_ambiente'];

    // Eliminar el ambiente
    $sql = "DELETE FROM Ambiente WHERE id_ambiente = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $id_ambiente);

    if (!$stmt->execute()) {
       $mensaje= "Error al eliminar el ambiente: " . $stmt->error ;
    } else {
        $mensaje= "Ambiente eliminado exitosamente.";
    }

    $stmt->close();
}

// Verificar si se debe mostrar ambientes activos o desactivados
if (isset($_GET['mostrar']) && $_GET['mostrar'] === 'desactivados') {
    $mostrar_activos = false;
}

// Consulta para obtener los ambientes activos o desactivados con filtro de búsqueda
$estado_ambiente = $mostrar_activos ? 'activo' : 'desactivado';
$sql = "SELECT id_ambiente, nombre, capacidad, ubicacion, tipo, Municipio, Subsede, estado FROM Ambiente WHERE nombre LIKE ? AND estado = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$search_param = "%$search_query%";
$stmt->bind_param("ss", $search_param, $estado_ambiente);

if (!$stmt->execute()) {
    $mensaje="Error al ejecutar la consulta: " . $stmt->error ;
} else {
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $ambientes[] = $row;
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
    <title>Ambientes</title>
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
            <a href="crear_ambiente.php" class="btn-add">Agregar Ambiente</a>
            <form method="POST" action="" style="display: inline;">
                <input type="text" name="search" id="buscador" placeholder="Buscar ambiente..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="btn-search">Buscar</button>
            </form>
            <div class="toggle-buttons">
                <a href="?mostrar=activos" class="btn-toggle <?php echo $mostrar_activos ? 'active' : ''; ?>">Activos</a>
                <a href="?mostrar=desactivados" class="btn-toggle <?php echo !$mostrar_activos ? 'active' : ''; ?>">Desactivados</a>
            </div>
        </div>
        
        <div class="table-container">
            <h1>Lista de Ambientes <?php echo $mostrar_activos ? 'Activos' : 'Desactivados'; ?></h1>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Capacidad</th>
                        <th>Tipo</th>
                        <th>ubicacion</th>
                        <th>Subsede</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ambientes)): ?>
                        <tr>
                            <td colspan="9">No se encontraron ambientes.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ambientes as $ambiente): ?>
                        <tr>
                            <td>
                                <?php if ($ambiente['estado'] == 'activo'): ?>
                                    <span class="status-dot active-dot"></span>
                                <?php else: ?>
                                    <span class="status-dot inactive-dot"></span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($ambiente['id_ambiente']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($ambiente['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($ambiente['capacidad']); ?></td>
                            <td><?php echo htmlspecialchars($ambiente['tipo']); ?></td>
                            <td><?php echo htmlspecialchars($ambiente['Municipio']); ?><button class="btn-address" onclick="window.location.href='direccion.php?id_ambiente=<?php echo urlencode($ambiente['id_ambiente']); ?>&ubicacion=<?php echo urlencode($ambiente['ubicacion']); ?>&municipio=<?php echo urlencode($ambiente['Municipio']); ?>'">📍</button>
                            </td>
                            <td><?php echo htmlspecialchars($ambiente['Subsede']); ?></td>
                            <td>
                                <a href="editar_ambiente.php?id_ambiente=<?php echo urlencode($ambiente['id_ambiente']); ?>" class="btn-edit">✏️ Editar</a>
                                <?php if ($mostrar_activos): ?>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="id_ambiente" value="<?php echo htmlspecialchars($ambiente['id_ambiente']); ?>">
                                        <button type="submit" name="desactivar" class="btn-disable">🚫 Desactivar</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="id_ambiente" value="<?php echo htmlspecialchars($ambiente['id_ambiente']); ?>">
                                        <button type="submit" name="activar" class="btn-enable">✅ Activar</button>
                                    </form>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="id_ambiente" value="<?php echo htmlspecialchars($ambiente['id_ambiente']); ?>">
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
