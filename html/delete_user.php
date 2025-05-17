<?php
session_start();

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION["authenticated"]) || $_SESSION["tipo_usuario"] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = "monika1155";
$dbname = "virthub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener la matrícula del usuario a eliminar
$matricula = $_POST['matricula'];

// Eliminar el usuario de la base de datos
$sql = "DELETE FROM usuarios WHERE matricula='$matricula'";

if ($conn->query($sql) === TRUE) {
    echo "Usuario eliminado correctamente";
} else {
    echo "Error eliminando el usuario: " . $conn->error;
}

$conn->close();

// Redirigir de vuelta a la página de administración de usuarios
header("Location: admin_user.php");
exit;
?>