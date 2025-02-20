<?php
session_start();

if (!isset($_SESSION["authenticated"]) || !isset($_GET["url"])) {
    header("Location: login.php");
    exit;
}

$url = $_GET["url"];
$nombre = $_SESSION["nombre"];
$apellido = $_SESSION["apellido"];
$grupo = $_SESSION["grupo"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido <?php echo $nombre?></title>
    <link rel="stylesheet" type="text/css" href="../css/stilotest.css">
</head>
<body>
    <header>
        <img src="../pic/logo_empersa.png" alt="" class="d-inline-block align-text-top">
        <div class="header-text">
            <h1 class="text-light"><?php echo $nombre . " " . $apellido; ?></h1>
            <p class="text-light grupo-text"><?php echo $grupo; ?></p>
        </div>
        <img src="../pic/virthub_logo.png" alt="" class="d-inline-block align-text-top">
    </header>
    <div class="topnav">
        <a href="logout.php" class="btn-volver">Cerrar Sesión</a>
        <a href="linktemporal" class="btn-volver">NextCloud</a>
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
    </script>
</body>
</html>