<?php
session_start();
if (!isset($_SESSION["authenticated"])) {
    header("Location: login.php");
    exit;
}

$matricula = $_SESSION["matricula"];
if (isset($_FILES["nueva_foto"]) && $_FILES["nueva_foto"]["error"] == 0) {
    $ext = pathinfo($_FILES["nueva_foto"]["name"], PATHINFO_EXTENSION);
    $filename = $matricula . "_" . time() . "." . $ext;
    $destino = __DIR__ . "/profile/" . $filename;
    if (move_uploaded_file($_FILES["nueva_foto"]["tmp_name"], $destino)) {
        // Actualiza la base de datos
        $conn = new mysqli("localhost", "root", "monika1155", "virthub");
        $conn->query("UPDATE usuarios SET foto_perfil='$filename' WHERE matricula='$matricula'");
        $conn->close();
        $_SESSION["foto_perfil"] = $filename;
    }
}
header("Location: fullscreen.php?url=" . urlencode($_SESSION["url"]));
exit;
?>