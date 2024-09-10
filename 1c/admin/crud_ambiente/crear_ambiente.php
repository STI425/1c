

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
                    <a href="../crud_aprendices/aprendices.php">
                        <ion-icon name="people-outline"></ion-icon>
                        <span>Aprendices</span>
                    </a>
                </li>
                <li>
                    <a href="ambientes.php">
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
include_once('../../conexion.php');
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $capacidad = $_POST['capacidad'];
    $ubicacion = $_POST['ubicacion'];
    $tipo = $_POST['tipo'];
    $municipio = $_POST['municipio'];
    $subsede = $_POST['subsede'];

    $sql = "INSERT INTO Ambiente (nombre, capacidad, ubicacion, tipo, Municipio, Subsede) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("sissss", $nombre, $capacidad, $ubicacion, $tipo, $municipio, $subsede);

    if ($stmt->execute()) {
        $mensaje = "Ambiente agregado exitosamente.";
    } else {
        $mensaje = "Error al agregar el ambiente: " . $stmt->error;
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
    <title>Agregar Ambiente y Mapa</title>
    <link rel="stylesheet" href="../../estilos/styles1.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <style>
        .map-container {
            width: 80%;
            max-width: 1200px;
            height: 70vh;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
            background-color: white;
            margin: 20px auto;
        }

        #map {
            width: 100%;
            height: 100%;
        }

      

       
    </style>
    <script>
        window.onload = function() {
            <?php if (!empty($mensaje)) : ?>
                alert("<?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>");
            <?php endif; ?>
        }

        function updateLocation(latlng, address) {
            document.getElementById('ubicacion').value = latlng.lat.toFixed(6) + ',' + latlng.lng.toFixed(6);
            document.getElementById('municipio').value = address;
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
        <h1>Agregar Ambiente</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="capacidad">Capacidad:</label>
                <input type="number" id="capacidad" name="capacidad" required>
            </div>

            <div class="form-group">
                <input type="hidden" id="ubicacion" name="ubicacion" readonly required>
            </div>

            <div class="form-group">
                <label for="tipo">Tipo:</label>
                <input type="text" id="tipo" name="tipo" required>
            </div>

            <div class="form-group">
                <label for="municipio">Ubicacion:</label>
                <input type="text" id="municipio" name="municipio" required>
            </div>

            <div class="form-group">
                <label for="subsede">Subsede:</label>
                <input type="text" id="subsede" name="subsede" required>
            </div>

            <button type="submit" class="btn-submit">Agregar Ambiente</button>
        </form>
    </div>

    <div class="map-container">
        <h1>Mapa con Buscador y Marcador Único</h1>
        <div id="map"></div>
    </div>

    <div class="info">
        <h2>Ubicación Seleccionada</h2>
        <p id="details">Haz clic en el mapa para seleccionar una ubicación.</p>
    </div>

    <footer>
        <a href="../../cerrar_sesion.php" class="boton-cerrar-sesion">Cerrar Sesión</a>
    </footer>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
        var map = L.map('map').setView([0, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var geocoder = L.Control.Geocoder.nominatim();
        var marker = L.marker([0, 0], { draggable: true }).addTo(map);

        var details = document.getElementById('details');

        L.Control.geocoder().on('markgeocode', function(e) {
            var latlng = e.geocode.center;
            marker.setLatLng(latlng);
            map.setView(latlng, 15);
            details.innerHTML = 'Dirección: ' + e.geocode.name;
            updateLocation(latlng, e.geocode.name);
        }).addTo(map);

        map.on('click', function(e) {
            var latlng = e.latlng;
            marker.setLatLng(latlng);
            map.setView(latlng, 15);
            details.innerHTML = 'Ubicación: ' + latlng.lat.toFixed(6) + ', ' + latlng.lng.toFixed(6);

            geocoder.reverse(latlng, map.options.crs.scale(map.getZoom()), function(results) {
                if (results.length > 0) {
                    var address = results[0].name;
                    updateLocation(latlng, address);
                }
            });
        });

        function updateLocation(latlng, address) {
            document.getElementById('ubicacion').value = latlng.lat.toFixed(6) + ',' + latlng.lng.toFixed(6);
            document.getElementById('municipio').value = address;
        }
    </script>
</body>
</html>

    </main>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../../script/js.js"></script>
</body>

</html>
