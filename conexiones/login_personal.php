<?php
// Verifica si ya se ha iniciado una sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica que se haya presionado el botón de ingresar
if (!empty($_POST["btningresar"])) {
    if (empty($_POST["usuario"]) || empty($_POST["password2"])) {
        echo "Debes ingresar ambos campos"; // Mensaje en caso de que falten campos
    } else {
        $usuario = $_POST["usuario"];
        $clave = $_POST["password2"];
        
        // Configuración de la conexión
        $db = "gestionproject";
        $servidor = "127.0.0.1";
        $db_usuario = "root";
        $db_clave = "1234";

        // Crear la conexión
        $conexion = new mysqli($servidor, $db_usuario, $db_clave, $db);

        // Verifica la conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Consulta SQL usando consultas preparadas
        $sql = "SELECT * FROM personales WHERE usuario = ? AND clave = ?";
        
        // Preparar la consulta
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ss", $usuario, $clave); // 's' para string

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener resultados
        $resultado = $stmt->get_result();

        // Verifica si la consulta devuelve resultados
        if ($resultado->num_rows > 0) {
            // Si hay resultados, guarda el nombre y el ID en la sesión
            if ($datos = $resultado->fetch_object()) {
                $_SESSION["nombre"] = $datos->nombre; // Suponiendo que el campo en la base de datos se llama "nombre"
                $_SESSION["id_personal"] = $datos->Id_Personal; // Guardar el ID del personal
                $_SESSION["apellido"] = $datos -> apellido;
                
                // Redirige al backend
                header("Location: backend/personal/index.php");
                exit();
            }
        } else {
            echo "Usuario o contraseña incorrectos"; // Mensaje si no se encuentran resultados
        }

        // Cierra el statement
        $stmt->close();
    }
}
?>
