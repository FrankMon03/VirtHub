<?php
session_start();
$error_message = "";

// Habilitar la visualización de errores (esto mejor lo dejamos para ver si hay errores)
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

    // Consultar la base de datos (Aquí usaba consultas preparadas pero las comento para hacerla vulnerable)
    //$stmt = $conn->prepare("SELECT password, url, nombre, apellido, grupo FROM usuarios WHERE matricula = ?");
    //$stmt->bind_param("s", $user);
    //$stmt->execute();
    //$stmt->store_result();
    //$stmt->bind_result($hashed_password, $url, $nombre, $apellido, $grupo);
    //$stmt->fetch();

    // Ahora lo cambiamos por una consulta directa (100% vulnerable 🔥)
    $sql = "SELECT password, url, nombre, apellido, grupo FROM usuarios WHERE matricula = '$user'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    // Aquí comparamos la contraseña en SHA-256 sin protección para hacerla vulnerable 😈
    $hashed_input_password = hash('sha256', $pass);

    if ($result->num_rows > 0 && $hashed_input_password == $row['password']) { // Vulnerable con comparación directa
        $_SESSION["authenticated"] = true;
        $_SESSION["nombre"] = $row['nombre'];
        $_SESSION["apellido"] = $row['apellido'];
        $_SESSION["grupo"] = $row['grupo'];
        header("Location: fullscreen.php?url=" . urlencode($row['url']));
        exit;
    } else {
        $error_message = "Usuario o contraseña incorrectos";
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
        <title>Iniciar Sesion</title>
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
