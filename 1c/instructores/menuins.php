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

// Incluir el archivo de conexión a la base de datos
include_once('../conexion.php');

// Consulta para verificar si el usuario es líder de alguna ficha
$sql_leader_check = "SELECT COUNT(*) AS total_fichas FROM Ficha WHERE id_lider = ?";
$stmt_leader_check = $conn->prepare($sql_leader_check);
$stmt_leader_check->bind_param("i", $codigo_usu);
$stmt_leader_check->execute();
$result_leader_check = $stmt_leader_check->get_result();
$row = $result_leader_check->fetch_assoc();

$is_leader = $row['total_fichas'] > 0;

$stmt_leader_check->close();
$conn->close();
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
                    <a href="crud_ficha/fichas.php">
                        <ion-icon name="book-outline"></ion-icon>
                        <span>Ficha</span>
                    </a>
                </li>
            </ul>
            <?php if ($is_leader): ?>
        <li>
            <a href="crear_novedad.php" class="btn-novedad">
                <ion-icon name="add-circle-outline"></ion-icon>
                <span> Novedad</span>
            </a>
        </li>
        <?php endif; ?>
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
        
        <div class="cuadro bienvenida">
            <h1>Bienvenido al Sistema SENA</h1>
            <p>Estamos encantados de tenerte aquí. Este sistema está diseñado para facilitar la gestión y administración de las fichas del SENA.</p>
            <p><strong>Tu código de usuario es: </strong></p>
            <p>Con el menú lateral, puedes acceder fácilmente a la sección de:</p>
            <ul>
                <li><strong>Ficha:</strong> Gestiona las fichas de los programas, incluyendo fechas, etapas y ambientes.</li>
            </ul>
            <p>Para una mejor experiencia, asegúrate de explorar la sección y utilizar las herramientas disponibles para maximizar la eficiencia en la administración. Si necesitas ayuda o tienes alguna duda, no dudes en consultar el manual de usuario o contactar con el soporte.</p>
        </div>

        <a href="../logout.php" class="boton-cerrar-sesion">Cerrar Sesión</a>
    </main>
    
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../script/js.js"></script>
</body>

</html>
