<?php
session_start();

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION["authenticated"]) || $_SESSION["tipo_usuario"] !== 'admin') {
    header("Location: login.php");
    exit;
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $container_name = escapeshellarg($_POST['container_name']);
    $port1 = escapeshellarg($_POST['port1']);
    $port2 = escapeshellarg($_POST['port2']);

    $command = "docker run -d --name=$container_name --security-opt seccomp=unconfined -e PUID=1000 -e PGID=1000 -e TZ=America/Mexico -e SUBFOLDER=/ -e KEYBOARD=es-es-qwerty -e AUTO_LOGIN=true -p $port1:3000 -p $port2:3001 -v /var/run/docker.sock:/var/run/docker.sock --device /dev/dri:/dev/dri --shm-size=\"2gb\" --memory=\"4g\" --restart unless-stopped virthub:virthub1";

    $output = shell_exec($command . " 2>&1");
    if (strpos($output, 'Error') !== false) {
        $error_message = "Error al ejecutar el comando Docker: " . htmlspecialchars($output);
    } else {
        $error_message = "Contenedor iniciado correctamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../pic/virthub_logo.png">
    <title>Crear Contenedor Docker</title>
    <link rel="stylesheet" type="text/css" href="../css/stilotest.css">
</head>
<body>
    <header>
        <img src="../pic/logo_empersa.png" alt="" class="d-inline-block align-text-top">
        <h1>Crear Contenedor Docker</h1>
        <img src="../pic/virthub_logo.png" alt="" class="d-inline-block align-text-top">
    </header>
    <div class="topnav">
        <a href="fullscreen.php?url=<?php echo urlencode($_SESSION['url']); ?>" class="btn-volver">Volver</a>
    </div>
    <div class="marco">
        <div class="contenedor">
            <?php if ($error_message): ?>
                <p style="color:red;"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="crear_cont.php" method="post" class="login-form">
                <label for="container_name">Nombre del Contenedor:</label>
                <input type="text" id="container_name" name="container_name" required class="login-input"><br>
                <label for="port1">Puerto Inseguro (xxxx:3000):</label>
                <input type="text" id="port1" name="port1" required class="login-input"><br>
                <label for="port2">Puerto Seguro (xxxx:3001):</label>
                <input type="text" id="port2" name="port2" required class="login-input"><br>
                <button type="submit" class="btn-volver">Crear Contenedor</button>
            </form>
        </div>
    </div>
</body>
</html>