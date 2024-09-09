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
                    <a href="usuarios.php">
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
    header("Location: ../.../index.php");
    exit();
}



// Inicializar variables
$usuarios = [];
$mostrar_activos = true;

// Verificar si se ha enviado una búsqueda
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

// Verificar si se ha enviado una solicitud de desactivación
if (isset($_POST['desactivar'])) {
    $codigo_usu = $_POST['codigo_usu'];

    // Actualizar el estado del usuario a 'desactivado'
    $sql = "UPDATE Usuario SET estado = 'desactivado' WHERE codigo_usu = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codigo_usu);

    if (!$stmt->execute()) {
        echo "<p>Error al desactivar el usuario: " . $stmt->error . "</p>";
    } else {
        echo "<p>Usuario desactivado exitosamente.</p>";
    }

    $stmt->close();
}

// Verificar si se ha enviado una solicitud de activación
if (isset($_POST['activar'])) {
    $codigo_usu = $_POST['codigo_usu'];

    // Actualizar el estado del usuario a 'activo'
    $sql = "UPDATE Usuario SET estado = 'activo' WHERE codigo_usu = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codigo_usu);

    if (!$stmt->execute()) {
        echo "<p>Error al activar el usuario: " . $stmt->error . "</p>";
    } else {
        echo "<p>Usuario activado exitosamente.</p>";
    }

    $stmt->close();
}

// Verificar si se ha enviado una solicitud de eliminación
if (isset($_POST['eliminar'])) {
    $codigo_usu = $_POST['codigo_usu'];

    // Eliminar el usuario
    $sql = "DELETE FROM Usuario WHERE codigo_usu = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codigo_usu);

    if (!$stmt->execute()) {
        echo "<p>Error al eliminar el usuario: " . $stmt->error . "</p>";
    } else {
        echo "<p>Usuario eliminado exitosamente.</p>";
    }

    $stmt->close();
}

// Verificar si se debe mostrar usuarios activos o desactivados
if (isset($_GET['mostrar']) && $_GET['mostrar'] === 'desactivados') {
    $mostrar_activos = false;
}

// Consulta para obtener los usuarios activos o desactivados con filtro de búsqueda
$estado_usuario = $mostrar_activos ? 'activo' : 'desactivado';
$sql = "SELECT u.codigo_usu, u.documento, u.nombre, u.especialidad, u.apellido, u.email, u.password, u.rol, u.telefono, u.estado, r.nombre AS rol_nombre 
        FROM Usuario u 
        JOIN Rol r ON u.rol = r.id_rol 
        WHERE u.nombre LIKE ? AND u.estado = ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search_query%";
$stmt->bind_param("ss", $search_param, $estado_usuario);

if (!$stmt->execute()) {
    echo "<p>Error al ejecutar la consulta: " . $stmt->error . "</p>";
} else {
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
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
    <title>Usuarios</title>
    <link rel="stylesheet" href="../../estilos/styles1.css">
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
            <a href="crear_usuario.php" class="btn-add">Agregar Usuario</a>
            <form method="POST" action="" style="display: inline;">
                <input type="text" name="search" id="buscador" placeholder="Buscar usuario..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="btn-search">Buscar</button>
            </form>
            <div class="toggle-buttons">
                <a href="?mostrar=activos" class="btn-toggle <?php echo $mostrar_activos ? 'active' : ''; ?>">Activos</a>
                <a href="?mostrar=desactivados" class="btn-toggle <?php echo !$mostrar_activos ? 'active' : ''; ?>">Desactivados</a>
            </div>
        </div>
        
        <div class="table-container">
            <h1>Lista de Usuarios <?php echo $mostrar_activos ? 'Activos' : 'Desactivados'; ?></h1>
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Documento</th>
                        <th>Nombre</th>
                        <th>Especialidad</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="10">No se encontraron usuarios.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td>
                                <?php if ($usuario['estado'] == 'activo'): ?>
                                    <span class="status-dot active-dot"></span>
                                <?php else: ?>
                                    <span class="status-dot inactive-dot"></span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($usuario['codigo_usu']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($usuario['documento']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['especialidad']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['apellido']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['rol_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['estado']); ?></td>
                            <td>
                                <a href="editar_usuario.php?codigo_usu=<?php echo urlencode($usuario['codigo_usu']); ?>" class="btn-edit">✏️ Editar</a>
                                <?php if ($mostrar_activos): ?>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="codigo_usu" value="<?php echo htmlspecialchars($usuario['codigo_usu']); ?>">
                                        <button type="submit" name="desactivar" class="btn-disable" >🚫 Desactivar</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="codigo_usu" value="<?php echo htmlspecialchars($usuario['codigo_usu']); ?>">
                                        <button type="submit" name="activar" class="btn-enable">✅ Activar</button>
                                    </form>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="codigo_usu" value="<?php echo htmlspecialchars($usuario['codigo_usu']); ?>">
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
<a href="../logout.php" class="boton-cerrar-sesion">Cerrar Sesión</a>
    </main>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../../script/js.js"></script>
</body>

</html>