<?php
session_start();

if (!isset($_SESSION["authenticated"]) || !isset($_GET["url"])) {
    header("Location: login.php");
    exit;
}

// Conectar a la base de datos y obtener el tipo de usuario
$servername = "localhost";
$username = "root";
$password = "monika1155";
$dbname = "virthub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$matricula = $_SESSION["matricula"];
$sql = "SELECT tipo_usuario, foto_perfil FROM usuarios WHERE matricula = '$matricula'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $tipo_usuario = $row["tipo_usuario"];
    $_SESSION["tipo_usuario"] = $tipo_usuario;
    $_SESSION["foto_perfil"] = $row["foto_perfil"];
} else {
    $tipo_usuario = "user";
    $_SESSION["tipo_usuario"] = $tipo_usuario;
}

$conn->close();

$url = $_GET["url"];
$_SESSION["url"] = $url;
$nombre = $_SESSION["nombre"];
$apellido = $_SESSION["apellido"];
$grupo = $_SESSION["grupo"];

// Obtener la foto de perfil del usuario
$foto_perfil = isset($_SESSION["foto_perfil"]) ? $_SESSION["foto_perfil"] : "default.png";
$foto_path = "./profile/" . $foto_perfil;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../pic/virthub_logo.png">
    <title>Bienvenido <?php echo $nombre?></title>
    <link rel="stylesheet" type="text/css" href="../css/stilotest.css">
</head>
<body>
    <header>
        <div class="header-text">
            <img src="../pic/logo_empersa.png" alt="" class="d-inline-block align-text-top">
            <div class="header-text">
                <h1 class="text-light"><?php echo $nombre . " " . $apellido; ?></h1>
                <p class="text-light grupo-text"><?php echo $grupo; ?></p>
            </div>
            <img src="../pic/virthub_logo.png" alt="" class="d-inline-block align-text-top">
            <!-- Foto de perfil alineada a la derecha y menú a la par -->
            <div style="position:absolute; right:30px; top:20px; display:inline-flex; align-items:center; flex-direction:row;">
                <div class="profile-pic-frame" style="position:relative;">
                    <img src="<?php echo $foto_path; ?>" alt="Foto de perfil" id="profile-pic">
                    <div id="profile-menu">
                        <form id="form-foto" action="update_foto.php" method="post" enctype="multipart/form-data" style="padding:12px; text-align:center;">
                            <label for="nueva_foto" style="cursor:pointer; color:#174C7F; font-weight:bold;">Cambiar foto</label>
                            <input type="file" name="nueva_foto" id="nueva_foto" accept="image/*" style="display:none;" onchange="this.form.submit();">
                            <p><label for="logout" style="cursor:pointer; color:#174C7F; font-weight:bold;">Cerrar Sesión</label>
                            <input type="button" id="logout" style="display:none;" onclick="window.location.href='logout.php';">
                        </form>
                        <div style="border-top:1px solid #eee;"></div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <button id="menu-toggle" class="btn-volver" style="display:none; margin:10px auto;">☰ Menú</button>

    
        <?php if ($tipo_usuario == 'admin'): ?>
            <div class="topnav" id="mainTopnav" style="position:relative;">
            <a href="ag_user.php" class="btn-volver">Agregar Usuario</a>
            <a href="admin_user.php" class="btn-volver">Administrar Usuarios</a>
            <a href="admin_docker.php" class="btn-volver">Administrar Docker</a>
            <a href="crear_cont.php" class="btn-volver">Crear Contenedor</a>
        <?php endif; ?>
    </div>
    <div class="marco">
        <div class="contenedor">
            <div class="iframe-container">
                <iframe id="contentFrame" src="<?php echo htmlspecialchars($url); ?>" style="position:relative; z-index:1;"></iframe>
                <div style="display:flex; gap:8px; margin-top:8px; justify-content:center;">
                    <button class="fullscreen-button" onclick="toggleFullScreen()">Pantalla Completa</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleFullScreen() {
            var iframe = document.getElementById("contentFrame");
            if (iframe.requestFullscreen) {
                iframe.requestFullscreen();
            } else if (iframe.mozRequestFullScreen) { // Firefox
                iframe.mozRequestFullScreen();
            } else if (iframe.webkitRequestFullscreen) { // Chrome, Safari and Opera
                iframe.webkitRequestFullscreen();
            } else if (iframe.msRequestFullscreen) { // IE/Edge
                iframe.msRequestFullscreen();
            }
        }

         // Función para ocultar la parte de la URL después del signo de interrogación
        function hideUrlParams() {
        if (window.history.replaceState) {
            const url = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({path: url}, "", url);
        }
    }

    // Llamar a la función para ocultar los parámetros de la URL
    hideUrlParams();
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Menú hamburguesa responsivo
    var menuToggle = document.getElementById('menu-toggle');
    var topnav = document.getElementById('mainTopnav');
    function checkMobile() {
        if (topnav) { // Solo si existe topnav
            if (window.innerWidth <= 768) {
                menuToggle.style.display = 'block';
                topnav.style.display = 'none';
            } else {
                menuToggle.style.display = 'none';
                topnav.style.display = 'block';
            }
        } else {
            menuToggle.style.display = 'none';
        }
    }
    if (menuToggle && topnav) {
        menuToggle.addEventListener('click', function() {
            if (topnav.style.display === 'block') {
                topnav.style.display = 'none';
            } else {
                topnav.style.display = 'block';
            }
        });
    }
    window.addEventListener('resize', checkMobile);
    checkMobile();

    // Menú de foto de perfil
    var pic = document.getElementById('profile-pic');
    var menu = document.getElementById('profile-menu');
    var marco = document.querySelector('.marco');

    if (pic && menu && marco) {
        pic.addEventListener('click', function(e) {
            e.stopPropagation();
            var isOpen = menu.classList.contains('open');
            if (!isOpen) {
                menu.classList.add('open');
                marco.classList.add('blur');
            } else {
                menu.classList.remove('open');
                marco.classList.remove('blur');
            }
        });
        menu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        document.addEventListener('click', function() {
            menu.classList.remove('open');
            marco.classList.remove('blur');
        });
    }
});
</script>
</body>
</html>