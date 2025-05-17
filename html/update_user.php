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

// Obtener los datos del formulario
$matricula = $_POST['matricula'];
$url = $_POST['url'];
$tipo_usuario = $_POST['tipo_usuario'];

// Actualizar el usuario en la base de datos
$sql = "UPDATE usuarios SET url='$url', tipo_usuario='$tipo_usuario' WHERE matricula='$matricula'";

if ($conn->query($sql) === TRUE) {
    echo "Usuario actualizado correctamente";
} else {
    echo "Error actualizando el usuario: " . $conn->error;
}

$conn->close();

// Redirigir de vuelta a la página de administración de usuarios
header("Location: admin_user.php");
exit;
?>