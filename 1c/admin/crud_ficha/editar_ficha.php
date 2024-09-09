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
$mensaje = "";
$error = "";

// Verificar si se ha enviado un ID de ficha
if (isset($_GET['id_ficha'])) {
    $id_ficha = $_GET['id_ficha'];

    // Inicializar variables para los datos de la ficha
    $ficha = [];
    $estado_actual = ''; // Inicializar variable de estado

    // Obtener los datos de la ficha para mostrar en el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $sql = "SELECT * FROM Ficha WHERE id_ficha = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_ficha);
        
        if (!$stmt->execute()) {
            $error = "Error al obtener los datos de la ficha: " . $stmt->error;
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $ficha = $result->fetch_assoc();
                $estado_actual = $ficha['estado']; // Obtener el estado actual
                // Asignar valores actuales para los campos que no se mostrarán en el HTML
                $id_vocero = $ficha['id_vocero'];
                $id_suplente = $ficha['id_suplente'];
                $id_lider = $ficha['id_lider'];
            } else {
                $error = "Ficha no encontrada.";
            }
        }
        $stmt->close();
    }

    // Actualizar los datos de la ficha
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $Fecha_inicio = $_POST['Fecha_inicio'];
        $Fecha_fin = $_POST['Fecha_fin'];
        $Fechas_finalizacion = $_POST['Fechas_finalizacion'];
        $Fecha_cierre = $_POST['Fecha_cierre'];
        $id_jornada = $_POST['id_jornada'];
        $Etapa = $_POST['Etapa'];
        $Oferta = $_POST['Oferta'];
        $codigo_p = $_POST['codigo_p'];
        $id_ambiente = $_POST['id_ambiente'];
        $estado = $estado_actual; // Mantener el estado actual sin modificar

        // Preparar la consulta SQL para actualizar los datos
        $sql = "UPDATE Ficha SET Fecha_inicio = ?, Fecha_fin = ?, Fechas_finalizacion = ?, Fecha_cierre = ?, id_jornada = ?, Etapa = ?, Oferta = ?, codigo_p = ?, id_ambiente = ? WHERE id_ficha = ?";
        $stmt = $conn->prepare($sql);
        // Definir los tipos de los parámetros: s = string, i = integer
        $stmt->bind_param("ssssissiii", $Fecha_inicio, $Fecha_fin, $Fechas_finalizacion, $Fecha_cierre, $id_jornada, $Etapa, $Oferta, $codigo_p, $id_ambiente, $id_ficha);

        if ($stmt->execute()) {
            $mensaje = "Ficha actualizada exitosamente.";
        } else {
            $error = "Error al actualizar la ficha: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    die("ID de ficha no especificado.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ficha</title>
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
            <a href="fichas.php" class="btn-back">Volver</a>
            <h1>Editar Ficha</h1>
        </div>

        <div class="form-container">
            <?php if ($mensaje): ?>
                <p class="success-message"><?php echo $mensaje; ?></p>
            <?php endif; ?>

            <?php if ($error): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="Fecha_inicio">Fecha Inicio:</label>
                <input type="date" name="Fecha_inicio" id="Fecha_inicio" value="<?php echo htmlspecialchars($ficha['Fecha_inicio']); ?>" required>

                <label for="Fecha_fin">Fecha Fin:</label>
                <input type="date" name="Fecha_fin" id="Fecha_fin" value="<?php echo htmlspecialchars($ficha['Fecha_fin']); ?>" required>

                <label for="Fechas_finalizacion">Fecha Finalización:</label>
                <input type="date" name="Fechas_finalizacion" id="Fechas_finalizacion" value="<?php echo htmlspecialchars($ficha['Fechas_finalizacion']); ?>">

                <label for="Fecha_cierre">Fecha Cierre:</label>
                <input type="date" name="Fecha_cierre" id="Fecha_cierre" value="<?php echo htmlspecialchars($ficha['Fecha_cierre']); ?>">

                <label for="id_jornada">Jornada:</label>
                <input type="number" name="id_jornada" id="id_jornada" value="<?php echo htmlspecialchars($ficha['id_jornada']); ?>" required>

                <label for="Etapa">Etapa:</label>
                <input type="text" name="Etapa" id="Etapa" value="<?php echo htmlspecialchars($ficha['Etapa']); ?>" required>

                <label for="Oferta">Oferta:</label>
                <input type="text" name="Oferta" id="Oferta" value="<?php echo htmlspecialchars($ficha['Oferta']); ?>" required>

                <!-- Los campos id_vocero, id_suplente, e id_lider no están incluidos en el HTML -->
                
                <label for="codigo_p">Código Programa:</label>
                <input type="number" name="codigo_p" id="codigo_p" value="<?php echo htmlspecialchars($ficha['codigo_p']); ?>" required>

                <label for="id_ambiente">Ambiente:</label>
                <input type="number" name="id_ambiente" id="id_ambiente" value="<?php echo htmlspecialchars($ficha['id_ambiente']); ?>" required>

                <!-- No incluir campo estado en el formulario -->
               
                <button type="submit" class="btn-submit">Actualizar Ficha</button>
            </form>
            <a href='asignar_instructores.php?id_ficha=<?php echo $id_ficha; ?>' class='btn'>Asignar Instructores</a>
            <a href='asignar_aprendices.php?id_ficha=<?php echo $id_ficha; ?>' class='btn'>Asignar Aprendices</a>
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