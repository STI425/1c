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
                    <a href="aprendices.php">
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

$id_aprendiz = $_GET['id_aprendiz'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documento = $_POST['documento'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $codigo_p = $_POST['codigo_p'];
    
    // Actualizar el aprendiz
    $sql = "UPDATE Aprendiz SET documento = ?, nombre = ?, apellido = ?, email = ?, telefono = ?, codigo_p = ? WHERE id_aprendiz = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssii", $documento, $nombre, $apellido, $email, $telefono, $codigo_p, $id_aprendiz);

    if ($stmt->execute()) {
        $mensaje= "Aprendiz actualizado exitosamente.";
    } else {
        $mensaje= "Error al actualizar el aprendiz: " . $stmt->error ;
    }

    $stmt->close();
}

// Obtener los datos actuales del aprendiz
$sql = "SELECT * FROM Aprendiz WHERE id_aprendiz = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_aprendiz);
$stmt->execute();
$result = $stmt->get_result();
$aprendiz = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aprendiz</title>
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
        <h1>Editar Aprendiz</h1>
        <form method="POST" action="">
            <label for="documento">Documento:</label>
            <input type="number" name="documento" id="documento" value="<?php echo htmlspecialchars($aprendiz['documento']); ?>" required>
            
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($aprendiz['nombre']); ?>" required>
            
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" value="<?php echo htmlspecialchars($aprendiz['apellido']); ?>" required>
            
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($aprendiz['email']); ?>" required>
            
            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" value="<?php echo htmlspecialchars($aprendiz['telefono']); ?>" required>
            
            <label for="codigo_p">Código Programa:</label>
            <input type="number" name="codigo_p" id="codigo_p" value="<?php echo htmlspecialchars($aprendiz['codigo_p']); ?>" required>
            
            <button type="submit">Actualizar Aprendiz</button>
        </form>
        <a href="aprendices.php">Volver a la lista de aprendices</a>
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
