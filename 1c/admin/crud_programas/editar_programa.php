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
// Inicializar variables para los detalles del programa
$codigo_p = "";
$nombre = "";
$competencias = "";
$resultados = "";
$estado = ""; // Esta variable se usará para mantener el estado actual del programa

// Verificar si se ha enviado el código del programa
if (isset($_GET['codigo_p'])) {
    $codigo_p = $_GET['codigo_p'];

    // Preparar una consulta para obtener los detalles del programa
    $sql = "SELECT Nombre, competencias, resultados, estado FROM Programas WHERE codigo_p = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigo_p);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si el programa existe
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombre = $row['Nombre'];
        $competencias = $row['competencias'];
        $resultados = $row['resultados'];
        $estado = $row['estado']; // Obtener el estado actual del programa
    } else {
        $mensaje= "Programa no encontrado.";
        exit();
    }

    // Cerrar la declaración
    $stmt->close();
}

// Verificar si se ha enviado el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $competencias = $_POST['competencias'];
    $resultados = $_POST['resultados'];

    // Mantener el estado original del programa al actualizar
    // Preparar una consulta para actualizar los detalles del programa
    $sql = "UPDATE Programas SET Nombre = ?, competencias = ?, resultados = ?, estado = ? WHERE codigo_p = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nombre, $competencias, $resultados, $estado, $codigo_p);

    if ($stmt->execute()) {
        header("Location: programa.php"); // Redirige a la lista de programas después de la actualización
        exit();
    } else {
        $mensaje= "Error al actualizar el programa.";
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Programa</title>
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
        <h1>Editar Programa</h1>
        <form method="POST" action="">
            <input type="hidden" name="codigo_p" value="<?php echo htmlspecialchars($codigo_p); ?>">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            </div>
            <div class="form-group">
                <label for="competencias">Competencias:</label>
                <textarea name="competencias" id="competencias" required><?php echo htmlspecialchars($competencias); ?></textarea>
            </div>
            <div class="form-group">
                <label for="resultados">Resultados:</label>
                <textarea name="resultados" id="resultados" required><?php echo htmlspecialchars($resultados); ?></textarea>
            </div>
            <button type="submit" class="btn-save">Guardar Cambios</button>
            <a href="programa.php" class="btn-cancel">Cancelar</a>
        </form>
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