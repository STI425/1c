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
include('../../conexion.php');
session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol adecuado
if (!isset($_SESSION['email']) || $_SESSION['rol'] != 1) {
    // Si no ha iniciado sesión o no es administrador, redirigir a la página de inicio de sesión
    header("Location: ../../index.php");
    exit();
}
// Inicializar variables para los errores y mensajes
$error = '';
$mensaje = '';

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $codigo_p = $_POST['codigo_p'];
    $nombre = $_POST['nombre'];
    $competencias = $_POST['competencias'];
    $resultados = $_POST['resultados'];

    // Validar los datos del formulario
    if (empty($codigo_p) || empty($nombre) || empty($competencias) || empty($resultados)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Consulta para insertar el nuevo programa con el estado 'activo'
        $sql = "INSERT INTO Programas (codigo_p, Nombre, competencias, resultados, estado) VALUES (?, ?, ?, ?, 'activo')";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssss", $codigo_p, $nombre, $competencias, $resultados);
            if ($stmt->execute()) {
                $mensaje = "Programa creado exitosamente.";
            } else {
                $error = "Error al crear el programa: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Error en la consulta SQL: " . $conn->error;
        }
    }

    // Cerrar la conexión
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Programa</title>
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
        <h1>Crear Programa</h1>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php elseif (!empty($mensaje)): ?>
            <div class="success"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="codigo_p">Código del Programa:</label>
            <input type="text" name="codigo_p" id="codigo_p" required>

            <label for="nombre">Nombre del Programa:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="competencias">Competencias:</label>
            <textarea name="competencias" id="competencias" required></textarea>

            <label for="resultados">Resultados:</label>
            <textarea name="resultados" id="resultados" required></textarea>

            <button type="submit" class="btn-submit">Crear Programa</button>
        </form>

        <div class="back-link">
            <a href="programa.php">Volver a la lista de programas</a>
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