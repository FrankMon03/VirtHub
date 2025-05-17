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

// Obtener los usuarios de la base de datos
$sql = "SELECT matricula, nombre, url, tipo_usuario FROM usuarios";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../pic/virthub_logo.png">
    <title>Administrar Usuarios</title>
    <link rel="stylesheet" type="text/css" href="../css/stilotest.css">
</head>
<body>
    <header>
        <img src="../pic/logo_empersa.png" alt="" class="d-inline-block align-text-top">
        <h1>Administrar Usuarios</h1>
        <img src="../pic/virthub_logo.png" alt="" class="d-inline-block align-text-top">
    </header>
    <div class="topnav">
        <a href="fullscreen.php?url=<?php echo urlencode($_SESSION['url']); ?>" class="btn-volver">Volver</a>
    </div>
    <div class="marco">
        <div class="contenedor">
            <table class="info-tabla">
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>Nombre</th>
                        <th>URL</th>
                        <th>Tipo de Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<form action='update_user.php' method='post' style='display:inline;'>";
                            echo "<tr>";
                            echo "<td>" . $row["matricula"] . "<input type='hidden' name='matricula' value='" . $row["matricula"] . "'></td>";
                            echo "<td>" . $row["nombre"] . "</td>";
                            echo "<td><input type='text' name='url' value='" . htmlspecialchars($row["url"]) . "' class='login-input' required></td>";
                            echo "<td><select name='tipo_usuario' class='login-input'>";
                            echo "<option value='user'" . ($row["tipo_usuario"] == 'user' ? ' selected' : '') . ">Usuario</option>";
                            echo "<option value='admin'" . ($row["tipo_usuario"] == 'admin' ? ' selected' : '') . ">Administrador</option>";
                            echo "</select></td>";
                            echo "<td>";
                            echo "<button type='submit' class='btn-volver'>Guardar</button>";
                            echo "</form>";

                            // Botón para resetear password
                            echo "<form action='reset_password.php' method='get' style='display:inline;'>";
                            echo "<input type='hidden' name='matricula' value='" . $row["matricula"] . "'>";
                            echo "<button type='submit' class='btn-volver' style='background:#f7b731;color:#222;'>Resetear Password</button>";
                            echo "</form>";

                            echo "<form action='delete_user.php' method='post' style='display:inline;'>";
                            echo "<input type='hidden' name='matricula' value='" . $row["matricula"] . "'>";
                            echo "<button type='submit' class='btn-volver'>Eliminar</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No hay usuarios registrados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>