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
$jornadas = [];
$ambientes = [];
$programas = [];

// Consultar las jornadas disponibles
$sql_jornada = "SELECT id_jornada, nombre FROM Jornada";
$result_jornada = $conn->query($sql_jornada);

if (!$result_jornada) {
    die("Error en la consulta de jornadas: " . $conn->error);
}

while ($row = $result_jornada->fetch_assoc()) {
    $jornadas[] = $row;
}

// Consultar los ambientes disponibles
$sql_ambiente = "SELECT id_ambiente, nombre FROM Ambiente WHERE estado = 'activo'";
$result_ambiente = $conn->query($sql_ambiente);

if (!$result_ambiente) {
    die("Error en la consulta de ambientes: " . $conn->error);
}

while ($row = $result_ambiente->fetch_assoc()) {
    $ambientes[] = $row;
}

// Consultar los programas disponibles
$sql_programa = "SELECT codigo_p, Nombre FROM Programas WHERE estado = 'activo'";
$result_programa = $conn->query($sql_programa);

if (!$result_programa) {
    die("Error en la consulta de programas: " . $conn->error);
}

while ($row = $result_programa->fetch_assoc()) {
    $programas[] = $row;
}

// Inicializar variable para mostrar el mensaje de éxito
$mensaje_exito = "";

// Procesar el formulario de creación de ficha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_ficha'])) {
    $fecha_inicio = $_POST['Fecha_inicio'];
    $fecha_fin = $_POST['Fecha_fin'];
    $fechas_finalizacion = $_POST['Fechas_finalizacion'];
    $fecha_cierre = $_POST['Fecha_cierre'];
    $id_jornada = $_POST['id_jornada'];
    $etapa = $_POST['Etapa'];
    $oferta = $_POST['Oferta'];
    $id_ambiente = $_POST['id_ambiente'];
    $codigo_p = $_POST['codigo_p'];

    // Insertar nueva ficha
    $sql = "INSERT INTO Ficha (Fecha_inicio, Fecha_fin, Fechas_finalizacion, Fecha_cierre, id_jornada, Etapa, Oferta, id_ambiente, codigo_p, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo')";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("ssssissii", $fecha_inicio, $fecha_fin, $fechas_finalizacion, $fecha_cierre, $id_jornada, $etapa, $oferta, $id_ambiente, $codigo_p);

    if ($stmt->execute()) {
        $id_ficha = $conn->insert_id; // Obtener el ID de la ficha insertada
        $mensaje_exito = "Ficha creada exitosamente. <a href='asignar_instructores.php?id_ficha=" . $id_ficha . "' class='btn'>Asignar Instructores</a> <a href='asignar_aprendices.php?id_ficha=" . $id_ficha . "&codigo_p=" . $codigo_p . "' class='btn'>Asignar Aprendices</a>";
    } else {
        $mensaje= "Error al crear la ficha: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Ficha</title>
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
        <h1>Crear Ficha</h1>
        <form method="POST" action="">
            <label for="Fecha_inicio">Fecha de Inicio:</label>
            <input type="date" name="Fecha_inicio" id="Fecha_inicio" required>

            <label for="Fecha_fin">Fecha de Fin:</label>
            <input type="date" name="Fecha_fin" id="Fecha_fin" required>

            <label for="Fechas_finalizacion">Fecha de Finalización:</label>
            <input type="date" name="Fechas_finalizacion" id="Fechas_finalizacion" required>

            <label for="Fecha_cierre">Fecha de Cierre:</label>
            <input type="date" name="Fecha_cierre" id="Fecha_cierre" required>

            <label for="id_jornada">Jornada:</label>
            <select name="id_jornada" id="id_jornada" required>
                <?php foreach ($jornadas as $jornada): ?>
                    <option value="<?php echo htmlspecialchars($jornada['id_jornada']); ?>">
                        <?php echo htmlspecialchars($jornada['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="Etapa">Etapa:</label>
            <input type="text" name="Etapa" id="Etapa" maxlength="10" required>

            <label for="Oferta">Oferta:</label>
            <input type="text" name="Oferta" id="Oferta" maxlength="20" required>

            <label for="id_ambiente">Ambiente:</label>
            <select name="id_ambiente" id="id_ambiente" required>
                <?php foreach ($ambientes as $ambiente): ?>
                    <option value="<?php echo htmlspecialchars($ambiente['id_ambiente']); ?>">
                        <?php echo htmlspecialchars($ambiente['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="codigo_p">Programa:</label>
            <select name="codigo_p" id="codigo_p" required>
                <?php foreach ($programas as $programa): ?>
                    <option value="<?php echo htmlspecialchars($programa['codigo_p']); ?>">
                        <?php echo htmlspecialchars($programa['Nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="crear_ficha">Crear Ficha</button>
        </form>

        <!-- Mostrar mensaje de éxito y botones si hay éxito -->
        <?php if ($mensaje_exito): ?>
            <div class="mensaje-exito">
                <?php echo $mensaje_exito; ?>
            </div>
        <?php endif; ?>
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