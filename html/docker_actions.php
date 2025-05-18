<?php
session_start();

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION["authenticated"]) || $_SESSION["tipo_usuario"] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($id === 'all') {
        // Obtener todos los IDs de contenedores
        $ids = shell_exec("docker ps -a -q");
        $ids = trim($ids);
        if ($ids !== '') {
            $ids_array = preg_split('/\s+/', $ids);
            foreach ($ids_array as $cid) {
                switch ($action) {
                    case 'start':
                        shell_exec("docker start $cid");
                        break;
                    case 'stop':
                        shell_exec("docker stop $cid");
                        break;
                    case 'restart':
                        shell_exec("docker restart $cid");
                        break;
                    case 'delete':
                        shell_exec("docker rm -f $cid");
                        break;
                }
            }
        }
    } else {
        switch ($action) {
            case 'start':
                shell_exec("docker start $id");
                break;
            case 'stop':
                shell_exec("docker stop $id");
                break;
            case 'restart':
                shell_exec("docker restart $id");
                break;
            case 'delete':
                shell_exec("docker rm -f $id");
                break;
        }
    }
}

header("Location: admin_docker.php");
exit;
?>