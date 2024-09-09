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
include_once('../../conexion.php');
session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol adecuado
if (!isset($_SESSION['email']) || $_SESSION['rol'] != 2) {
    // Si no ha iniciado sesión o no es administrador, redirigir a la página de inicio de sesión
    header("Location: ../../index.php");
    exit();
}
if (!isset($_GET['id_ficha']) || empty($_GET['id_ficha'])) {
    $mensaje= "Ficha no especificada.";
    exit;
}

$id_ficha = $_GET['id_ficha'];

// Consulta principal para obtener los detalles de la ficha
$sql = "SELECT * FROM Ficha WHERE id_ficha = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_ficha);

if (!$stmt->execute()) {
    $mensaje= "Error al ejecutar la consulta: " . $stmt->error ;
    exit;
}

$result = $stmt->get_result();
$ficha = $result->fetch_assoc();

$stmt->close();

// Obtener aprendices asociados a la ficha
$sql_aprendices = "SELECT a.id_aprendiz, a.nombre, a.apellido FROM Aprendiz a 
                   JOIN ficha_aprendiz fa ON a.id_aprendiz = fa.id_aprendiz 
                   WHERE fa.id_ficha = ?";
$stmt_aprendices = $conn->prepare($sql_aprendices);
$stmt_aprendices->bind_param("i", $id_ficha);
$stmt_aprendices->execute();
$aprendices_result = $stmt_aprendices->get_result();
$aprendices = $aprendices_result->fetch_all(MYSQLI_ASSOC);
$stmt_aprendices->close();

// Obtener instructores asociados a la ficha
$sql_instructores = "SELECT u.nombre, u.apellido FROM Usuario u 
                     JOIN usuario_ficha uf ON u.codigo_usu = uf.codigo_usu 
                     WHERE uf.id_ficha = ? AND u.rol = 2"; // rol = 2 para instructores
$stmt_instructores = $conn->prepare($sql_instructores);
$stmt_instructores->bind_param("i", $id_ficha);
$stmt_instructores->execute();
$instructores_result = $stmt_instructores->get_result();
$instructores = $instructores_result->fetch_all(MYSQLI_ASSOC);
$stmt_instructores->close();

// Obtener vocero, líder y suplente
$sql_vocero = "SELECT u.nombre, u.apellido FROM Usuario u 
               JOIN Ficha f ON u.codigo_usu = f.id_vocero 
               WHERE f.id_ficha = ?";
$stmt_vocero = $conn->prepare($sql_vocero);
$stmt_vocero->bind_param("i", $id_ficha);
$stmt_vocero->execute();
$vocero_result = $stmt_vocero->get_result();
$vocero = $vocero_result->fetch_assoc();
$stmt_vocero->close();

$sql_lider = "SELECT u.nombre, u.apellido FROM Usuario u 
              JOIN Ficha f ON u.codigo_usu = f.id_lider 
              WHERE f.id_ficha = ?";
$stmt_lider = $conn->prepare($sql_lider);
$stmt_lider->bind_param("i", $id_ficha);
$stmt_lider->execute();
$lider_result = $stmt_lider->get_result();
$lider = $lider_result->fetch_assoc();
$stmt_lider->close();

$sql_suplente = "SELECT u.nombre, u.apellido FROM Usuario u 
                 JOIN Ficha f ON u.codigo_usu = f.id_suplente 
                 WHERE f.id_ficha = ?";
$stmt_suplente = $conn->prepare($sql_suplente);
$stmt_suplente->bind_param("i", $id_ficha);
$stmt_suplente->execute();
$suplente_result = $stmt_suplente->get_result();
$suplente = $suplente_result->fetch_assoc();
$stmt_suplente->close();

$conn->close();

if (!$ficha) {
    $mensaje= "Ficha no encontrada.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Ficha</title>
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
    <header class="header-bar">
        <div class="logo-container">
            <img src="../../imagenes/sena-logo.svg" alt="Logo SENA">
        </div>
    </header>

    <div class="details-container">
        <h1>Detalles de la Ficha</h1>
        <table class="details-table">
            <tr>
                <th>ID</th>
                <td><?php echo htmlspecialchars($ficha['id_ficha']); ?></td>
            </tr>
            <tr>
                <th>Fecha Inicio</th>
                <td><?php echo htmlspecialchars($ficha['Fecha_inicio']); ?></td>
            </tr>
            <tr>
                <th>Fecha Fin</th>
                <td><?php echo htmlspecialchars($ficha['Fecha_fin']); ?></td>
            </tr>
            <tr>
                <th>Fecha Finalización</th>
                <td><?php echo htmlspecialchars($ficha['Fechas_finalizacion']); ?></td>
            </tr>
            <tr>
                <th>Fecha Cierre</th>
                <td><?php echo htmlspecialchars($ficha['Fecha_cierre']); ?></td>
            </tr>
            <tr>
                <th>Jornada</th>
                <td><?php echo htmlspecialchars($ficha['id_jornada']); ?></td>
            </tr>
            <tr>
                <th>Etapa</th>
                <td><?php echo htmlspecialchars($ficha['Etapa']); ?></td>
            </tr>
            <tr>
                <th>Oferta</th>
                <td><?php echo htmlspecialchars($ficha['Oferta']); ?></td>
            </tr>
            <tr>
                <th>Código Programa</th>
                <td><?php echo htmlspecialchars($ficha['codigo_p']); ?></td>
            </tr>
            <tr>
                <th>Ambiente</th>
                <td><?php echo htmlspecialchars($ficha['id_ambiente']); ?></td>
            </tr>
        </table>

        <div class="info-item">
            <h2>Aprendices Asociados</h2>
            <table class="details-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($aprendices)): ?>
                        <tr>
                            <td colspan="3">No hay aprendices asociados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($aprendices as $aprendiz): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($aprendiz['id_aprendiz']); ?></td>
                            <td><?php echo htmlspecialchars($aprendiz['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($aprendiz['apellido']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="info-item">
            <h2>Instructores Asociados</h2>
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($instructores)): ?>
                        <tr>
                            <td colspan="2">No hay instructores asociados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($instructores as $instructor): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($instructor['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($instructor['apellido']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="info-item">
            <h2>Vocero</h2>
            <p><?php echo htmlspecialchars($vocero['nombre'] . ' ' . $vocero['apellido']); ?></p>
        </div>

        <div class="info-item">
            <h2>Líder</h2>
            <p><?php echo htmlspecialchars($lider['nombre'] . ' ' . $lider['apellido']); ?></p>
        </div>

        <div class="info-item">
            <h2>Suplente</h2>
            <p><?php echo htmlspecialchars($suplente['nombre'] . ' ' . $suplente['apellido']); ?></p>
        </div>

        <a href="fichas.php" class="btn-back">Volver a la lista</a>
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