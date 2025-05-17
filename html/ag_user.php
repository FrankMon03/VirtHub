<?php
session_start();

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION["authenticated"]) || $_SESSION["tipo_usuario"] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Inicializar variables de error
$error_message = "";

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conectar a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "monika1155";
    $dbname = "virthub";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener los datos del formulario
    $matricula = $_POST['matricula'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $grupo = $_POST['grupo'];
    $url = $_POST['url'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $password = hash('sha256', $_POST['password']); // Encriptar la contraseña con SHA-256

    // Insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO usuarios (matricula, nombre, apellido, grupo, url, tipo_usuario, password) VALUES ('$matricula', '$nombre', '$apellido', '$grupo', '$url', '$tipo_usuario', '$password')";

    if ($conn->query($sql) === TRUE) {
        $error_message = "Usuario agregado correctamente";
    } else {
        $error_message = "Error agregando el usuario: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../pic/virthub_logo.png">
    <title>Agregar Usuario</title>
    <link rel="stylesheet" type="text/css" href="../css/stilotest.css"> <!-- Usar el CSS de login -->
</head>
<body>
    <header>
        <img src="../pic/logo_empersa.png" alt="" class="d-inline-block align-text-top">
        <h1>Agregar Usuario</h1>
        <img src="../pic/virthub_logo.png" alt="" class="d-inline-block align-text-top">
    </header>
    <div class="topnav">
        <a href="fullscreen.php?url=<?php echo urlencode($_SESSION['url']); ?>" class="btn-volver">Volver</a>
    </div>
    <div class="marco">
        <div class="login-container">
            <?php if ($error_message): ?>
                <p style="color:red;"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="ag_user.php" method="post" class="login-form">
                <label for="matricula">Matrícula:</label>
                <input type="text" id="matricula" name="matricula" required class="login-input"><br>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required class="login-input"><br>
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required class="login-input"><br>
                <label for="grupo">Grupo:</label>
                <input type="text" id="grupo" name="grupo" required class="login-input"><br>
                <label for="url">URL:</label>
                <input type="text" id="url" name="url" required class="login-input"><br>
                <label for="tipo_usuario">Tipo de Usuario:</label>
                <select id="tipo_usuario" name="tipo_usuario" class="login-input">
                    <option value="user">Usuario</option>
                    <option value="admin">Administrador</option>
                </select><br>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required class="login-input"><br>
                <button type="submit" class="btn-volver">Agregar Usuario</button>
            </form>
        </div>
    </div>
</body>
</html>