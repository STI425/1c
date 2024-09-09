<?php
// Configuración de la base de datos
define('DB_SERVER', 'localhost'); // o la dirección de tu servidor de base de datos
define('DB_USERNAME', 'root'); // tu nombre de usuario de MySQL
define('DB_PASSWORD', ''); // tu contraseña de MySQL
define('DB_DATABASE', 'proyecto'); // el nombre de tu base de datos

// Crear una conexión a la base de datos
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("La conexión a la base de datos falló: " . $conn->connect_error);
}

?>
