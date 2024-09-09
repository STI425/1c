<?php 
session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol adecuado
if (!isset($_SESSION['email']) || $_SESSION['rol'] != 2) {
    // Si no ha iniciado sesión o no es instructor, redirigir a la página de inicio de sesión
    header("Location: ../index.php");
    exit();
}

// Verificar si codigo_usu está definido en la sesión
if (isset($_SESSION['codigo_usu'])) {
    $codigo_usu = $_SESSION['codigo_usu'];
} else {
    // Manejar el caso en que codigo_usu no está definido
    $codigo_usu = 'No disponible'; // O cualquier valor predeterminado
    // O puedes mostrar un mensaje de error más explícito
    // die('El código de usuario no está disponible.');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
    <link rel="stylesheet" href="../estilos/despegable.css">
    <style>
    /* Estilos para los cuadros */
    .cuadro {
        background-color: rgba(255, 255, 255, 0.6); /* Fondo blanco con 60% de opacidad */
        border: 1px solid rgba(200, 200, 200, 0.5); /* Borde gris claro con 50% de opacidad */
        border-radius: 8px; /* Bordes redondeados */
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra sutil */
        backdrop-filter: blur(8px); /* Opcional: desenfoque de fondo */
    }

    /* Estilos para la bienvenida */
    .bienvenida {
        background-color: rgba(255, 255, 255, 0.6); /* Fondo blanco con 60% de opacidad */
        border: 1px solid rgba(200, 200, 200, 0.5); /* Borde gris claro con 50% de opacidad */
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(8px);
    }
    </style>
</head>

<body>
    <div class="menu">
        <ion-icon name="menu-outline"></ion-icon>
        <ion-icon name="close-outline"></ion-icon>
    </div>

    <div class="barra-lateral">
        <div>
            <div class="nombre-pagina">
                <img id="logo" src="../imagenes/sena-logo.svg" alt="Logo">
                <span>SENA</span>
            </div>
        </div>

        <nav class="navegacion">
            <ul>
                <li>
                    <a href="fichas.php">
                        <ion-icon name="book-outline"></ion-icon>
                        <span>Ficha</span>
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
                <img src="img/avatar-user-x640.jpg" alt="Avatar">
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
session_start();

// Incluir el archivo de conexión a la base de datos
include_once('../conexion.php');

// Inicializar variables
$fichas = [];
$search_query = ""; 
$codigo_usu = isset($_SESSION['codigo_usu']) ? $_SESSION['codigo_usu'] : 'No disponible';

// Verificar si se ha enviado una búsqueda
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

// Consulta para obtener las fichas activas asociadas al usuario con filtro de búsqueda
$sql = "SELECT f.id_ficha, f.Fecha_inicio, f.Fecha_fin, f.Fechas_finalizacion, f.Fecha_cierre, f.id_jornada, f.Etapa, f.Oferta, f.codigo_p, f.id_ambiente 
        FROM Ficha f
        INNER JOIN usuario_ficha uf ON f.id_ficha = uf.id_ficha
        WHERE uf.codigo_usu = ?
        AND CONCAT(f.Fecha_inicio, ' ', f.Fecha_fin, ' ', f.Fechas_finalizacion, ' ', f.Fecha_cierre, ' ', f.Etapa, ' ', f.Oferta, ' ', f.codigo_p, ' ', f.id_ambiente) LIKE ? 
        AND f.estado = 'activo'";

$stmt = $conn->prepare($sql);
$search_param = "%$search_query%";
$stmt->bind_param("is", $codigo_usu, $search_param);

$mensaje = ""; // Inicializar mensaje

if (!$stmt->execute()) {
    $mensaje = "Error al ejecutar la consulta: " . $stmt->error;
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
            <form method="POST" action="" style="display: inline;">
                <input type="text" name="search" id="buscador" placeholder="Buscar ficha..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="btn-search">Buscar</button>
            </form>
        </div>
        
        <div class="table-container">
            <h1>Lista de Fichas Activas</h1>
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
                                <span class="status-dot active-dot"></span>
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
    <script src="../script/js.js"></script>
</body>

</html>
