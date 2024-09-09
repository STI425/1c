<?php 
session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol adecuado
if (!isset($_SESSION['email']) || $_SESSION['rol'] != 1) {
    // Si no ha iniciado sesión o no es administrador, redirigir a la página de inicio de sesión
    header("Location: ../index.php");
    exit();


}
?>
<!DOCTYPE html>
<html lang="en">

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

/* Estilos para las noticias */
.noticias {
    background-color: rgba(255, 255, 255, 0.6); /* Fondo blanco con 60% de opacidad */
    border: 1px solid rgba(200, 200, 200, 0.5); /* Borde gris claro con 50% de opacidad */
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(8px);
}

/* Estilos para cada noticia individual */
.noticia {
    background-color: rgba(255, 255, 255, 0.6); /* Fondo blanco con 60% de opacidad */
    border: 1px solid rgba(200, 200, 200, 0.5); /* Borde gris claro con 50% de opacidad */
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
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
                    <a href="crud_usuarios/usuarios.php">
                        <ion-icon name="person-outline"></ion-icon>
                        <span>Instructores</span>
                    </a>
                </li>
                <li>
                    <a href="crud_ficha/fichas.php">
                        <ion-icon name="book-outline"></ion-icon>
                        <span>Ficha</span>
                    </a>
                </li>
                <li>
                    <a href="crud_programas/programa.php">
                        <ion-icon name="document-outline"></ion-icon>
                        <span>Programa</span>
                    </a>
                </li>
                <li>
                    <a href="crud_aprendices/aprendices.php">
                        <ion-icon name="people-outline"></ion-icon>
                        <span>Aprendices</span>
                    </a>
                </li>
                <li>
                    <a href="crud_ambiente/ambientes.php">
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
            <p>Estamos encantados de tenerte aquí. Este sistema está diseñado para facilitar la gestión y administración de los diferentes programas, fichas, instructores y ambientes del SENA.</p>
            <p>Con el menú lateral, puedes acceder fácilmente a las siguientes secciones:</p>
            <ul>
                <li><strong>Instructores:</strong> Administra la información de los instructores, incluyendo sus perfiles y asignaciones.</li>
                <li><strong>Ficha:</strong> Gestiona las fichas de los programas, incluyendo fechas, etapas y ambientes.</li>
                <li><strong>Programa:</strong> Visualiza y administra los programas disponibles, así como sus competencias y resultados.</li>
                <li><strong>Aprendices:</strong> Controla la información de los aprendices, incluyendo sus datos personales y asignaciones.</li>
                <li><strong>Ambiente:</strong> Configura y gestiona los ambientes disponibles para la realización de actividades y programas.</li>
            </ul>
            <p>Para una mejor experiencia, asegúrate de explorar cada sección y utilizar las herramientas disponibles para maximizar la eficiencia en la administración. Si necesitas ayuda o tienes alguna duda, no dudes en consultar el manual de usuario o contactar con el soporte.</p>
        </div>

        <div class="cuadro noticias">
            <h2>Últimas Noticias</h2>
            <div class="noticia">
                <h3>Actualización de Programas</h3>
                <p>Se han realizado importantes actualizaciones en los programas disponibles. Asegúrate de revisar los cambios y ajustes necesarios.</p>
                <a href="#">Leer más</a>
            </div>
            <div class="noticia">
                <h3>Nuevas Funcionalidades</h3>
                <p>Hemos añadido nuevas funcionalidades al sistema para mejorar la experiencia del usuario. Descubre lo nuevo en la sección de actualizaciones.</p>
                <a href="#">Leer más</a>
            </div>
        </div>
        <a href="../logout.php" class="boton-cerrar-sesion">Cerrar Sesión</a>
    </main>
    
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../script/js.js"></script>
</body>.

</html>
<?php 
