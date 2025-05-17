<?php
session_start();
$error_message = "";

// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = "monika1155";
$dbname = "virthub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST["matricula"];
    $pass = $_POST["password"];

    // Consultar la base de datos
    $stmt = $conn->prepare("SELECT password, url, nombre, apellido, grupo FROM usuarios WHERE matricula = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hashed_password, $url, $nombre, $apellido, $grupo);
    $stmt->fetch();

    // Comparar la contraseña en SHA-256
    $hashed_input_password = hash('sha256', $pass);

    if ($stmt->num_rows > 0 && $hashed_input_password == $hashed_password) {
        // Establecer la sesión
        $_SESSION["authenticated"] = true;
        $_SESSION["nombre"] = $nombre;
        $_SESSION["apellido"] = $apellido;
        $_SESSION["grupo"] = $grupo;
        $_SESSION["matricula"] = $user;
        $_SESSION["password"] = $pass;

        // URL de Nextcloud y credenciales
        $nextcloud_url = "http://localhost/html/nextcloud/index.php/login";
        $nextcloud_username = $user;
        $nextcloud_password = $pass;

        // Preparar cURL para autenticar en Nextcloud
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $nextcloud_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            "user" => $nextcloud_username,
            "password" => $nextcloud_password,
            "direct" => 1 // Esto podría ser necesario para que no redirija
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Para seguir redirecciones

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch); // Si hay error en cURL, mostrarlo
        } else {
            // Aquí deberíamos verificar si la respuesta contiene algún tipo de error
            if (strpos($response, 'Incorrect username or password') !== false) {
                $error_message = "Usuario o contraseña incorrectos en Nextcloud.";
            } else {
                // Si la autenticación es exitosa, podemos proceder con la redirección
                header("Location: fullscreen.php?url=" . urlencode($url));
                exit;
            }
        }

        curl_close($ch);
    } else {
        $error_message = "Usuario o contraseña incorrectos en la base de datos.";
    }
}

$conn->close();
?>

<link rel="stylesheet" type="text/css" href="../css/stilotest.css">

<body>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="shortcut icon" href="../pic/virthub_logo.png">
        <title>Iniciar Sesión</title>
    </head>
    <header>
        <img src="../pic/logo_empersa.png" alt="" class="d-inline-block align-text-top">
        <h1 class="text-light">VirtHub</h1>
        <img src="../pic/virthub_logo.png" alt="" class="d-inline-block align-text-top">
    </header>
    <div class="marco">
        <div class="login-container">
            <form method="post" class="login-form">
                <input type="text" name="matricula" placeholder="Matrícula" required class="login-input" pattern="\d{10}" title="Debe ser un número de 10 dígitos">
                <input type="password" name="password" placeholder="Contraseña" required class="login-input">
                <?php if ($error_message): ?>
                    <p style="color:red;"> <?php echo $error_message; ?></p>
                <?php endif; ?>
                <button type="submit" class="btn-volver">Ingresar</button>
            </form>
            <a href="../index.html" class="btn-volver">Volver al Inicio</a>
        </div>
    </div>
</body>
</html>
