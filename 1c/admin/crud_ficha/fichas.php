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
$fichas = [];
$mostrar_activos = true;

// Verificar si se ha enviado una búsqueda
$search_query = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

// Verificar si se ha enviado una solicitud de desactivación
if (isset($_POST['desactivar'])) {
    $id_ficha = $_POST['id_ficha'];

    // Actualizar el estado de la ficha a 'desactivado'
    $sql = "UPDATE Ficha SET estado = 'desactivado' WHERE id_ficha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_ficha);

    if (!$stmt->execute()) {
        $mensaje= "Error al desactivar la ficha: " . $stmt->error;
    } else {
        $mensaje= "Ficha desactivada exitosamente.";
    }

    $stmt->close();
}

// Verificar si se ha enviado una solicitud de activación
if (isset($_POST['activar'])) {
    $id_ficha = $_POST['id_ficha'];

    // Actualizar el estado de la ficha a 'activo'
    $sql = "UPDATE Ficha SET estado = 'activo' WHERE id_ficha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_ficha);

    if (!$stmt->execute()) {
        $mensaje= "Error al activar la ficha: " . $stmt->error ;
    } else {
       $mensaje="Ficha activada exitosamente.";
    }

    $stmt->close();
}

// Verificar si se ha enviado una solicitud de eliminación
if (isset($_POST['eliminar'])) {
    $id_ficha = $_POST['id_ficha'];

    // Eliminar la ficha
    $sql = "DELETE FROM Ficha WHERE id_ficha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_ficha);

    if (!$stmt->execute()) {
        $mensaje= "Error al eliminar la ficha: " . $stmt->error;
    } else {
        $mensaje= "Ficha eliminada exitosamente.";
    }

    $stmt->close();
}

// Verificar si se debe mostrar fichas activas o desactivadas
if (isset($_GET['mostrar']) && $_GET['mostrar'] === 'desactivadas') {
    $mostrar_activos = false;
}

// Consulta para obtener las fichas activas o desactivadas con filtro de búsqueda
$estado_ficha = $mostrar_activos ? 'activo' : 'desactivado';
$sql = "SELECT id_ficha, Fecha_inicio, Fecha_fin, Fechas_finalizacion, Fecha_cierre, id_jornada, Etapa, Oferta, id_vocero, id_suplente, id_lider, codigo_p, id_ambiente, estado 
        FROM Ficha 
        WHERE CONCAT(Fecha_inicio, ' ', Fecha_fin, ' ', Fechas_finalizacion, ' ', Fecha_cierre, ' ', Etapa, ' ', Oferta, ' ', id_vocero, ' ', id_suplente, ' ', id_lider, ' ', codigo_p, ' ', id_ambiente) LIKE ? 
        AND estado = ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search_query%";
$stmt->bind_param("ss", $search_param, $estado_ficha);

if (!$stmt->execute()) {
    $mensaje= "Error al ejecutar la consulta: " . $stmt->error ;
} else {
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $fichas[] = $row;
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
    <title>Fichas</title>
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
            <a href="crear_ficha.php" class="btn-add">Agregar Ficha</a>
            <form method="POST" action="" style="display: inline;">
                <input type="text" name="search" id="buscador" placeholder="Buscar ficha..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="btn-search">Buscar</button>
            </form>
            <div class="toggle-buttons">
                <a href="?mostrar=activos" class="btn-toggle <?php echo $mostrar_activos ? 'active' : ''; ?>">Activas</a>
                <a href="?mostrar=desactivadas" class="btn-toggle <?php echo !$mostrar_activos ? 'active' : ''; ?>">Desactivadas</a>
            </div>
        </div>
        
        <div class="table-container">
            <h1>Lista de Fichas <?php echo $mostrar_activos ? 'Activas' : 'Desactivadas'; ?></h1>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Fecha Finalización</th>
                        <th>Fecha Cierre</th>
                        <th>Jornada</th>
                        <th>Etapa</th>
                        <th>Oferta</th>
                        <th>Código Programa</th>
                        <th>Ambiente</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fichas)): ?>
                        <tr>
                            <td colspan="11">No se encontraron fichas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($fichas as $ficha): ?>
                        <tr>
                            <td>
                                <?php if ($ficha['estado'] == 'activo'): ?>
                                    <span class="status-dot active-dot"></span>
                                <?php else: ?>
                                    <span class="status-dot inactive-dot"></span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($ficha['id_ficha']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($ficha['Fecha_inicio']); ?></td>
                            <td><?php echo htmlspecialchars($ficha['Fecha_fin']); ?></td>
                            <td><?php echo htmlspecialchars($ficha['Fechas_finalizacion']); ?></td>
                            <td><?php echo htmlspecialchars($ficha['Fecha_cierre']); ?></td>
                            <td><?php echo htmlspecialchars($ficha['id_jornada']); ?></td>
                            <td><?php echo htmlspecialchars($ficha['Etapa']); ?></td>
                            <td><?php echo htmlspecialchars($ficha['Oferta']); ?></td>
                            <td><?php echo htmlspecialchars($ficha['codigo_p']); ?></td>
                            <td><?php echo htmlspecialchars($ficha['id_ambiente']); ?></td>
                            <td>
                                <a href="ver_detalles.php?id_ficha=<?php echo urlencode($ficha['id_ficha']); ?>" class="btn-details">👁️ Ver Detalles</a>
                                <a href="editar_ficha.php?id_ficha=<?php echo urlencode($ficha['id_ficha']); ?>" class="btn-edit">✏️ Editar</a>
                                <?php if ($mostrar_activos): ?>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="id_ficha" value="<?php echo htmlspecialchars($ficha['id_ficha']); ?>">
                                        <button type="submit" name="desactivar" class="btn-disable">Desactivar</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="id_ficha" value="<?php echo htmlspecialchars($ficha['id_ficha']); ?>">
                                        <button type="submit" name="activar" class="btn-enable">Activar</button>
                                    </form>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="id_ficha" value="<?php echo htmlspecialchars($ficha['id_ficha']); ?>">
                                        <button type="submit" name="eliminar" class="btn-delete">Eliminar</button>
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