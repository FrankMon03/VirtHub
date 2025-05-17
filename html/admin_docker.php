<?php
session_start();

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION["authenticated"]) || $_SESSION["tipo_usuario"] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Funciones para interactuar con Docker
function getDockerContainers() {
    $output = shell_exec('docker ps -a --format "{{.ID}} {{.Names}} {{.Image}} {{.Status}} {{.Ports}}" 2>&1');
    if (strpos($output, 'Error') !== false) {
        return ['error' => $output];
    }
    $containers = explode("\n", trim($output));
    $containerList = [];
    foreach ($containers as $container) {
        $details = preg_split('/\s+/', $container, 5);
        if (count($details) == 5) {
            $containerList[] = [
                'id' => $details[0],
                'name' => $details[1],
                'image' => $details[2],
                'status' => $details[3],
                'ports' => $details[4]
            ];
        }
    }
    return $containerList;
}

$containers = getDockerContainers();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../pic/virthub_logo.png">
    <title>Administrar Docker</title>
    <link rel="stylesheet" type="text/css" href="../css/stilotest.css">
</head>
<body>
    <header>
        <img src="../pic/logo_empersa.png" alt="" class="d-inline-block align-text-top">
        <h1>Administrar Docker</h1>
        <img src="../pic/virthub_logo.png" alt="" class="d-inline-block align-text-top">
    </header>
    <div class="topnav">
        <a href="fullscreen.php?url=<?php echo urlencode($_SESSION['url']); ?>" class="btn-volver">Volver</a>
    </div>
    <div class="marco">
        <div class="contenedor">
            <?php if (isset($containers['error'])): ?>
                <div class="error">
                    <p>Error: <?php echo htmlspecialchars($containers['error']); ?></p>
                </div>
            <?php else: ?>
                <table class="info-tabla">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Imagen</th>
                            <th>Estado</th>
                            <th>Puertos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($containers) > 0) {
                            foreach ($containers as $container) {
                                echo "<tr>";
                                echo "<td data-label='ID'>" . $container['id'] . "</td>";
                                echo "<td data-label='Nombre'>" . $container['name'] . "</td>";
                                echo "<td data-label='Imagen'>" . $container['image'] . "</td>";
                                echo "<td data-label='Estado'>" . $container['status'] . "</td>";
                                echo "<td data-label='Puertos'>" . $container['ports'] . "</td>";
                                echo "<td data-label='Acciones'>";
                                echo "<form action='docker_actions.php' method='post' style='display:inline;'>";
                                echo "<input type='hidden' name='id' value='" . $container['id'] . "'>";
                                echo "<select name='action' required style='margin-bottom:6px;'>";
                                echo "<option value='' disabled selected>Selecciona acción</option>";
                                echo "<option value='start'>Iniciar</option>";
                                echo "<option value='stop'>Detener</option>";
                                echo "<option value='restart'>Reiniciar</option>";
                                echo "<option value='delete'>Eliminar</option>";
                                echo "</select> ";
                                echo "<button type='submit' class='btn-volver' style='padding:4px 16px;font-size:14px;'>Ejecutar</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            // Fila para acciones globales
                            echo "<tr>";
                            echo "<td colspan='6' style='text-align:center;'>";
                            echo "<form action='docker_actions.php' method='post' style='display:inline;'>";
                            echo "<ul style='list-style:none; padding:0; margin:0; display:inline-flex; gap:10px;'>";
                            echo "<li><button type='submit' name='action' value='start' class='btn-volver' style='padding:4px 16px;font-size:14px;'>Iniciar Todos</button></li>";
                            echo "<li><button type='submit' name='action' value='stop' class='btn-volver' style='padding:4px 16px;font-size:14px;'>Detener Todos</button></li>";
                            echo "<li><button type='submit' name='action' value='restart' class='btn-volver' style='padding:4px 16px;font-size:14px;'>Reiniciar Todos</button></li>";
                            echo "<li><button type='submit' name='action' value='delete' class='btn-volver' style='padding:4px 16px;font-size:14px;'>Eliminar Todos</button></li>";
                            echo "</ul>";
                            echo "<input type='hidden' name='id' value='all'>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        } else {
                            echo "<tr><td colspan='6'>No hay contenedores</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>