<?php
// Verifica si ya se ha iniciado una sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!empty($_POST["btningresar"])){
     if (empty($_POST["correo"]) and empty($_POST["password3"])) {
         // Puedes agregar una acción aquí si es necesario
     } else {
        $usuario = $_POST["correo"];
        $clave = $_POST["password3"];

        $sql = $conexion->query("select * from administradores where correo = '$usuario' and clave = '$clave'");
        
        if ($datos = $sql->fetch_object()) {
            header("Location: backend/administrador/dashboard.php");
        } else {
        }
     }
}
?>
