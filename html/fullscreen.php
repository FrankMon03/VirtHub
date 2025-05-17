<?php
session_start();

if (!isset($_SESSION["authenticated"]) || !isset($_GET["url"])) {
    header("Location: login.php");
    exit;
}

// Conectar a la base de datos y obtener el tipo de usuario
$servername = "localhost";
$username = "root";
$password = "monika1155";
$dbname = "virthub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$matricula = $_SESSION["matricula"];
$sql = "SELECT tipo_usuario FROM usuarios WHERE matricula = '$matricula'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $tipo_usuario = $row["tipo_usuario"];
    $_SESSION["tipo_usuario"] = $tipo_usuario; // Guardar el tipo de usuario en la sesión
} else {
    $tipo_usuario = "user";
    $_SESSION["tipo_usuario"] = $tipo_usuario; // Guardar el tipo de usuario en la sesión
}

$conn->close();

$url = $_GET["url"];
$_SESSION["url"] = $url;
$nombre = $_SESSION["nombre"];
$apellido = $_SESSION["apellido"];
$grupo = $_SESSION["grupo"];
$nextcloud_url = "./nextcloud/index.php/login"; // URL de tu instancia de Nextcloud
$nextcloud_username = $_SESSION["matricula"]; // Nombre de usuario de Nextcloud (matricula)
$nextcloud_password = $_SESSION["password"]; // Contraseña de Nextcloud (password)

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../pic/virthub_logo.png">
    <title>Bienvenido <?php echo $nombre?></title>
    <link rel="stylesheet" type="text/css" href="../css/stilotest.css">
</head>
<body>
    <header>
        <div class="header-text">
            <!--<p class="text-light grupo-text">Tipo: <?php echo $tipo_usuario; ?></p>-->
        <img src="../pic/logo_empersa.png" alt="" class="d-inline-block align-text-top">
        <div class="header-text">
            <h1 class="text-light"><?php echo $nombre . " " . $apellido; ?></h1>
            <p class="text-light grupo-text"><?php echo $grupo; ?></p>
        </div>
        <img src="../pic/virthub_logo.png" alt="" class="d-inline-block align-text-top">
    </header>
    <div class="topnav">
        <a href="logout.php" class="btn-volver">Cerrar Sesión</a>
        <?php if ($tipo_usuario == 'admin'): ?>
            <a href="ag_user.php" class="btn-volver">Agregar Usuario</a>
            <a href="admin_user.php" class="btn-volver">Administrar Usuarios</a>
            <a href="admin_docker.php" class="btn-volver">Administrar Docker</a>
            <a href="crear_cont.php" class="btn-volver">Crear Contenedor</a>
        <?php endif; ?>
    </div>
    <div class="marco">
        <div class="contenedor">
            <div class="iframe-container">
                <iframe id="contentFrame" src="<?php echo htmlspecialchars($url); ?>"></iframe>
                <button class="fullscreen-button" onclick="toggleFullScreen()">Pantalla Completa</button>
            </div>
        </div>
    </div>

    <script>
        function toggleFullScreen() {
            var iframe = document.getElementById("contentFrame");
            if (iframe.requestFullscreen) {
                iframe.requestFullscreen();
            } else if (iframe.mozRequestFullScreen) { // Firefox
                iframe.mozRequestFullScreen();
            } else if (iframe.webkitRequestFullscreen) { // Chrome, Safari and Opera
                iframe.webkitRequestFullscreen();
            } else if (iframe.msRequestFullscreen) { // IE/Edge
                iframe.msRequestFullscreen();
            }
        }

        function openNextcloud() {
            var nextcloudWindow = window.open('', 'nextcloudWindow');
            document.getElementById('nextcloudForm').submit();
        }

         // Función para ocultar la parte de la URL después del signo de interrogación
        function hideUrlParams() {
        if (window.history.replaceState) {
            const url = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({path: url}, "", url);
        }
    }

    // Llamar a la función para ocultar los parámetros de la URL
    hideUrlParams();
    </script>
</body>
</html>