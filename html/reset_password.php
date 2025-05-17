<?php
session_start();
if (!isset($_SESSION["authenticated"]) || $_SESSION["tipo_usuario"] !== 'admin') {
    header("Location: login.php");
    exit;
}

$matricula = $_GET['matricula'] ?? $_POST['matricula'] ?? '';

// Obtener el nombre del usuario para el título
$nombre_usuario = '';
if ($matricula) {
    $servername = "localhost";
    $username = "root";
    $password = "monika1155";
    $dbname = "virthub";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if (!$conn->connect_error) {
        $stmt = $conn->prepare("SELECT nombre FROM usuarios WHERE matricula = ?");
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $stmt->bind_result($nombre_usuario);
        $stmt->fetch();
        $stmt->close();
        $conn->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["matricula"], $_POST["new_password"])) {
    $matricula = $_POST["matricula"];
    $new_password = $_POST["new_password"];

    $servername = "localhost";
    $username = "root";
    $password = "monika1155";
    $dbname = "virthub";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Usar SHA-256 para la contraseña
    $hashed = hash('sha256', $new_password);
    $sql = "UPDATE usuarios SET password='$hashed' WHERE matricula='$matricula'";
    $conn->query($sql);
    $conn->close();

    header("Location: admin_user.php?reset=ok");
    exit;
}

// Mostrar formulario si hay matrícula
if ($matricula):
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña de <?php echo htmlspecialchars($nombre_usuario ?: $matricula); ?></title>
    <link rel="stylesheet" href="../css/stilotest.css">
</head>
<body>
    <header>
        <img src="../pic/logo_empersa.png" alt="" class="d-inline-block align-text-top">
        <h1>Restablecer contraseña de <?php echo htmlspecialchars($nombre_usuario ?: $matricula); ?></h1>
        <img src="../pic/virthub_logo.png" alt="" class="d-inline-block align-text-top">
    </header>
    <div class="marco">
        <div class="login-container" style="max-width:400px;margin:40px auto;">
            <form method="post" class="login-form">
                <input type="hidden" name="matricula" value="<?php echo htmlspecialchars($matricula); ?>">
                <label for="new_password">Nueva contraseña:</label>
                <input type="password" name="new_password" id="new_password" required class="login-input"><br>
                <button type="submit" class="btn-volver" style="width:100%;">Guardar</button>
            </form>
            <div style="text-align:center;margin-top:15px;">
                <a href="admin_user.php" class="btn-volver" style="background:#eee;color:#222;">Cancelar</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php
else:
    header("Location: admin_user.php");
    exit;
endif;
?>