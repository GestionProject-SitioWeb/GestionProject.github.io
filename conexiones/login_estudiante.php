<?php
// Verifica si ya se ha iniciado una sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica que se haya presionado el botón de ingresar
if (!empty($_POST["btningresar"])) {
    if (empty($_POST["n_control"]) || empty($_POST["password1"])) {
        echo "Debes ingresar ambos campos"; // Mensaje en caso de que falten campos
    } else {
        $usuario = $_POST["n_control"];
        $clave = $_POST["password1"];

        // Conexión a la base de datos
        $db = "gestionproject";
        $servidor = "127.0.0.1";
        $usuario_db = "root";
        $clave_db = "1234";
        
        $conexion = new mysqli($servidor, $usuario_db, $clave_db, $db);

        // Verifica la conexión a la base de datos
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Usa una consulta preparada para evitar SQL injection
        $stmt = $conexion->prepare("SELECT * FROM estudiantes WHERE n_control = ? AND clave = ?");
        $stmt->bind_param("ss", $usuario, $clave); // "ss" significa que ambos parámetros son strings
        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica si la consulta devuelve resultados
        if ($result->num_rows > 0) {
            // Si hay resultados, guarda los datos necesarios en la sesión
            $datos = $result->fetch_object();
            $_SESSION["nombre"] = $datos->nombre;
            $_SESSION["apellido"] = $datos->apellido;
            $_SESSION["carrera"] = $datos->carrera;
            $_SESSION["Id_Grupo"] = $datos->Id_Grupo;
            $_SESSION["Id_Estudiante"] = $datos->Id_Estudiante; // Asegúrate de que el campo exista en la base de datos

            // Redirige al backend
            header("Location: backend/estudiantes/index.php");
            exit();
        } else {
            echo "Usuario o contraseña incorrectos"; // Mensaje si no se encuentran resultados
        }

        // Cierra la conexión
        $stmt->close();
        $conexion->close();
    }
}
?>
