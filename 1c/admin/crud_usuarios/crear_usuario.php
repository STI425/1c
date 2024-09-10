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
    header("Location: ../../index.php");
    exit();
}

// Inicializar variables
$roles = [];

// Consultar los roles activos
$sql = "SELECT id_rol, nombre FROM Rol";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta de roles: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {
    $roles[] = $row;
}

// Procesar el formulario de creación
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documento = $_POST['documento'];
    $nombre = $_POST['nombre'];
    $especialidad = $_POST['especialidad'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];
    $telefono = $_POST['telefono'];

    // Insertar nuevo usuario
    $sql = "INSERT INTO Usuario (documento, nombre, especialidad, apellido, email, password, rol, telefono, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'activo')";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("ssssssis", $documento, $nombre, $especialidad, $apellido, $email, $password, $rol, $telefono);

    if (!$stmt->execute()) {
        $mensaje = "Error al crear el usuario: " . $stmt->error;
    } else {
        $mensaje = "Usuario creado exitosamente.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="../../estilos/crear.css">
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
        <h1>Crear Usuario</h1>
        <form method="POST" action="">
            <label for="documento">Documento:</label>
            <input type="text" name="documento" id="documento" required>

            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="especialidad">Especialidad:</label>
            <input type="text" name="especialidad" id="especialidad" required>

            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?php echo htmlspecialchars($rol['id_rol']); ?>">
                        <?php echo htmlspecialchars($rol['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" required>

            <button type="submit">Crear Usuario</button>
        </form>
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
