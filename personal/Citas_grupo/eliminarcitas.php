<?php
// eliminar_cita.php
// Configuración de la conexión
$db = "gestionproject";
$servidor = "127.0.0.1";
$usuario = "root";
$clave = "1234";

// Crear la conexión
$conexion = new mysqli($servidor, $usuario, $clave, $db);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Verificar si se pasó un ID en la URL
if (isset($_GET['id'])) {
    $idCita = intval($_GET['id']); // Asegúrate de que el ID sea un entero

    // Preparar la consulta de eliminación
    $query = "DELETE FROM citamaestro_estudiantes WHERE Id_Cita = ?"; // Asegúrate de usar el nombre correcto de la columna
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $idCita);

    if ($stmt->execute()) {
        echo "Registro eliminado correctamente";
    } else {
        echo "Error al eliminar el registro: " . $conexion->error;
    }

    $stmt->close();
} else {
    echo "ID no especificado.";
}

$conexion->close();
?>
