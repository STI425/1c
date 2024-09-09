<?php
// Verificar que se han recibido los parámetros
if (isset($_GET['ubicacion']) && isset($_GET['municipio'])) {
    // Obtener los datos de ubicación y municipio
    $ubicacion = urlencode($_GET['ubicacion']);
    $municipio = urlencode($_GET['municipio']);
    
    // Crear la URL de Google Maps para buscar la ubicación
    $maps_url = "https://www.google.com/maps/search/?api=1&query={$ubicacion},+{$municipio}";
    
    // Redirigir a Google Maps
    header("Location: $maps_url");
    exit();
} else {
    // En caso de que no se reciban los datos, redirigir a una página de error o inicio
    header("Location: ../index.php");
    exit();
}
?>
