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
// Incluir la configuración de la base de datos
include_once('../../conexion.php'); // Ajusta la ruta si es necesario
session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol adecuado
if (!isset($_SESSION['email']) || $_SESSION['rol'] != 1) {
    // Si no ha iniciado sesión o no es administrador, redirigir a la página de inicio de sesión
    header("Location: ../../index.php");
    exit();
}
// Obtener el ID de la ficha desde la URL
$id_ficha = isset($_GET['id_ficha']) ? intval($_GET['id_ficha']) : 0;
if ($id_ficha <= 0) {
    die("ID de ficha inválido.");
}

// Obtener los datos de la ficha
$queryFicha = "SELECT * FROM Ficha WHERE id_ficha = ?";
$stmt = $conn->prepare($queryFicha);
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->bind_param('i', $id_ficha);
$stmt->execute();
$resultFicha = $stmt->get_result();
$fichaData = $resultFicha->fetch_assoc();
$stmt->close();

// Obtener los aprendices ya asignados a la ficha
$queryAgregados = "SELECT A.id_aprendiz, A.nombre, A.apellido 
                   FROM Aprendiz A
                   INNER JOIN ficha_aprendiz FA ON A.id_aprendiz = FA.id_aprendiz
                   WHERE FA.id_ficha = ?";
$stmt = $conn->prepare($queryAgregados);
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->bind_param('i', $id_ficha);
$stmt->execute();
$resultAgregados = $stmt->get_result();
$aprendicesMostrados = [];
while ($aprendiz = $resultAgregados->fetch_assoc()) {
    $aprendicesMostrados[] = $aprendiz;
}
$stmt->close();

// Verificar la acción de seleccionar vocero o suplente
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_aprendiz = isset($_POST['id_aprendiz']) ? intval($_POST['id_aprendiz']) : 0;
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    if ($id_aprendiz > 0 && in_array($accion, ['vocero', 'suplente'])) {
        // Validar que el ID del aprendiz esté en la lista de aprendices asignados
        $valido = false;
        foreach ($aprendicesMostrados as $aprendiz) {
            if ($aprendiz['id_aprendiz'] == $id_aprendiz) {
                $valido = true;
                break;
            }
        }

        if ($valido) {
            // Verificar el estado actual de vocero y suplente
            $current_vocero = $fichaData['id_vocero'];
            $current_suplente = $fichaData['id_suplente'];

            if ($accion == 'vocero') {
                if ($current_vocero == $id_aprendiz) {
                    $mensaje= "El aprendiz ya es el vocero actual.";
                } elseif ($current_suplente == $id_aprendiz) {
                    $mensaje= "El aprendiz es el suplente actual. No puede ser asignado como vocero.";
                } else {
                    // Actualizar el vocero en la ficha
                    $queryUpdateVocero = "UPDATE Ficha SET id_vocero = ? WHERE id_ficha = ?";
                    $stmt = $conn->prepare($queryUpdateVocero);
                    if ($stmt === false) {
                        die("Error en la preparación de la consulta: " . $conn->error);
                    }
                    $stmt->bind_param('ii', $id_aprendiz, $id_ficha);
                    if ($stmt->execute()) {
                        $mensaje= "Vocero actualizado correctamente.";
                    } else {
                        $mensaje= "Error al actualizar el vocero: " . $stmt->error ;
                    }
                    $stmt->close();
                }
            } elseif ($accion == 'suplente') {
                if ($current_suplente == $id_aprendiz) {
                    $mensaje= "El aprendiz ya es el suplente actual.";
                } elseif ($current_vocero == $id_aprendiz) {
                    $mensaje= "El aprendiz es el vocero actual. No puede ser asignado como suplente.";
                } else {
                    // Actualizar el suplente en la ficha
                    $queryUpdateSuplente = "UPDATE Ficha SET id_suplente = ? WHERE id_ficha = ?";
                    $stmt = $conn->prepare($queryUpdateSuplente);
                    if ($stmt === false) {
                        die("Error en la preparación de la consulta: " . $conn->error);
                    }
                    $stmt->bind_param('ii', $id_aprendiz, $id_ficha);
                    if ($stmt->execute()) {
                        $mensaje= "Suplente actualizado correctamente.";
                    } else {
                        $mensaje= "Error al actualizar el suplente: " . $stmt->error ;
                    }
                    $stmt->close();
                }
            }
        } else {
            $mensaje= "ID de aprendiz no válido.";
        }
    } else {
        $mensaje="Acción no válida.";
    }
}

// Obtener los nombres de vocero y suplente
$voceroNombre = '';
$suplenteNombre = '';

if ($fichaData['id_vocero'] > 0) {
    $queryVocero = "SELECT nombre, apellido FROM Aprendiz WHERE id_aprendiz = ?";
    $stmt = $conn->prepare($queryVocero);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param('i', $fichaData['id_vocero']);
    $stmt->execute();
    $resultVocero = $stmt->get_result();
    $vocero = $resultVocero->fetch_assoc();
    $voceroNombre = htmlspecialchars($vocero['nombre']) . ' ' . htmlspecialchars($vocero['apellido']);
    $stmt->close();
}

if ($fichaData['id_suplente'] > 0) {
    $querySuplente = "SELECT nombre, apellido FROM Aprendiz WHERE id_aprendiz = ?";
    $stmt = $conn->prepare($querySuplente);
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param('i', $fichaData['id_suplente']);
    $stmt->execute();
    $resultSuplente = $stmt->get_result();
    $suplente = $resultSuplente->fetch_assoc();
    $suplenteNombre = htmlspecialchars($suplente['nombre']) . ' ' . htmlspecialchars($suplente['apellido']);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Vocero y Suplente</title>
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
    <div class="container">
        <h1>Asignar Vocero y Suplente</h1>

        <!-- Mostrar el vocero y suplente actuales -->
        <h2>Vocero Actual: <?php echo !empty($voceroNombre) ? htmlspecialchars($voceroNombre) : 'Ninguno'; ?></h2>
        <h2>Suplente Actual: <?php echo !empty($suplenteNombre) ? htmlspecialchars($suplenteNombre) : 'Ninguno'; ?></h2>

        <!-- Mostrar la lista de aprendices -->
        <form method="post" action="">
            <div class="user-list-container">
                <div class="user-list">
                    <?php foreach ($aprendicesMostrados as $aprendiz): ?>
                        <div class="user-list-item">
                            <input type="radio" name="id_aprendiz" value="<?php echo htmlspecialchars($aprendiz['id_aprendiz']); ?>">
                            <?php echo htmlspecialchars($aprendiz['nombre']) . ' ' . htmlspecialchars($aprendiz['apellido']); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Botones para asignar vocero o suplente -->
            <div class="actions">
                <input type="submit" name="accion" value="vocero">
                <input type="submit" name="accion" value="suplente">
            </div>
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