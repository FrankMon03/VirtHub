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
                            
                            echo "</form>";
                            echo "<button type='button' class='btn-volver btn-eliminar' data-nombre='" . htmlspecialchars($row["nombre"]) . "' data-matricula='" . $row["matricula"] . "'>Eliminar</button>";
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

    <!-- Modal de confirmación -->
    <div id="modal-eliminar" style="display:none; position:fixed; top:0; right:0; width:350px; height:100vh; background:#fff; box-shadow:-4px 0 20px rgba(0,0,0,0.2); z-index:9999; transform:translateX(100%); transition:transform 0.4s;">
        <div style="padding:32px;">
            <h3>Confirmar eliminación</h3>
            <p id="modal-text"></p>
            <div style="margin-top:24px; display:flex; gap:16px; justify-content:flex-end;">
                <button id="btn-si" class="btn-volver" style="background:#e74c3c; color:#fff;">Sí</button>
                <button id="btn-no" class="btn-volver">No</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('modal-eliminar');
        var modalText = document.getElementById('modal-text');
        var btnSi = document.getElementById('btn-si');
        var btnNo = document.getElementById('btn-no');
        var matriculaEliminar = null;

        // Mostrar modal al hacer click en eliminar
        document.querySelectorAll('.btn-eliminar').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var nombre = this.getAttribute('data-nombre');
                matriculaEliminar = this.getAttribute('data-matricula');
                modalText.textContent = "¿Está seguro de eliminar al usuario " + nombre + "?";
                modal.style.display = 'block';
                setTimeout(function() {
                    modal.style.transform = 'translateX(0)';
                }, 10);
            });
        });

        // Botón NO
        btnNo.addEventListener('click', function() {
            modal.style.transform = 'translateX(100%)';
            setTimeout(function() {
                modal.style.display = 'none';
            }, 400);
        });

        // Botón SÍ
        btnSi.addEventListener('click', function() {
            // Crear y enviar formulario por POST
            var form = document.createElement('form');
            form.method = 'post';
            form.action = 'delete_user.php';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'matricula';
            input.value = matriculaEliminar;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        });
    });
    </script>
</body>
</html>

<?php
$conn->close();
?>