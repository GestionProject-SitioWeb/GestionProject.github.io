<?php
session_start();

if (isset($_SESSION["nombre"])) {
    $nombre_usuario = $_SESSION["nombre"];
    $db = "gestionproject";
    $servidor = "127.0.0.1";
    $usuario = "root";
    $clave = "1234";

    $conexion = new mysqli($servidor, $usuario, $clave, $db);

    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }
    // Obtener el ID de la cita desde la URL
    if (isset($_GET['id'])) {
        $Id_Cita = intval($_GET['id']); // Asegúrate de que sea un entero

        // Consultar los datos de la cita
        $sql = "SELECT 
                    ce.Id_Cita, ce.motivo, ce.fecha, ce.hora, ce.comentario, 
                    g.nombre AS grupo_nombre, p.nombre AS personal_nombre,
                    p.Id_Personal
                FROM citamaestro_estudiantes ce
                INNER JOIN grupos g ON ce.Id_Grupo = g.Id_Grupo
                INNER JOIN personales p ON ce.Id_Personal = p.Id_Personal
                WHERE ce.Id_Cita = ?";

        // Preparar la consulta
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $Id_Cita);  // Aquí hemos cambiado el bind_param
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            $grupo_nombre = $row['grupo_nombre'];
            $motivo = $row['motivo'];
            $fecha = $row['fecha'];
            $hora = $row['hora'];
            $comentario = $row['comentario'];
        } else {
            $error = "No se encontró información para la cita.";
        }
        $stmt->close();
    } else {
        $error = "ID de cita no proporcionado.";
    }

    // Actualizar la información cuando se envía el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener los datos del formulario
        $motivo = $_POST['motivo'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $comentario = $_POST['comentario'];

        // Consulta para actualizar la cita
        $sql_update = "UPDATE citamaestro_estudiantes SET motivo=?, fecha=?, hora=?, comentario=? WHERE Id_Cita=?";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->bind_param("ssssi", $motivo, $fecha, $hora, $comentario, $Id_Cita);


        if ($stmt_update->execute()) {
            $success = "Cita actualizada exitosamente.";
            // Redireccionar a otra página después de la actualización
            header("Location: index.php"); // Cambia 'citas.php' por la página a la que desees redirigir
            exit();
        } else {
            $error = "Error al actualizar la cita: " . $conexion->error;
        }
    }
} else {
    // Manejo de sesión no iniciada
    header("Location: login.php"); // Redirigir si no hay sesión
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Inicio</title>
    <link rel="shortcut icon" href="../ico/escudo.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <link rel="stylesheet" href="../assets/css/tailwind.output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <script src="../assets/js/init-alpine.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" defer=""></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Estilos para la barra lateral */
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #f8f9fa;
            padding-top: 60px;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        /* Ocultar la barra lateral en pantallas grandes */
        @media (min-width: 992px) {
            .sidebar {
                display: none;
            }
        }

        /* Mostrar la barra lateral en pantallas pequeñas */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-250px);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            /* Ajustar el contenido principal cuando la barra lateral está oculta */
            #main-content {
                margin-left: 0;
                margin-top: 70px;
                /* Ajuste para evitar solapamiento con el navbar en pantallas pequeñas */
            }
        }

        /* Espacio para el contenido principal en pantallas grandes */
        #main-content {
            margin-left: 0;
        }

        /* Ocultar el botón de barra lateral en pantallas grandes */
        .toggle-sidebar-btn {
            display: block;
        }

        @media (min-width: 992px) {
            .toggle-sidebar-btn {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar superior -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <!-- Botón para mostrar la barra lateral en pantallas pequeñas -->
            <button class="btn toggle-sidebar-btn" type="button" onclick="toggleSidebar()">
                <span class="navbar-toggler-icon"></span> Menú
            </button>

            <!-- Logo y nombre del sitio -->
            <a class="navbar-brand ms-2" href="#">
                <img src="../img/escudo.png" alt="Escudo" width="30" height="30" class="d-inline-block align-text-top">
                Gestion de Turnos
            </a>

            <!-- Enlaces de navegación en la barra superior -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="Cita_Maestro.php">Crear Citas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../conexiones/logout.php">Cerrar sesión</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <!-- Barra lateral -->
    <div class="sidebar bg-light" id="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="Cita_Maestro.php" onclick="hideSidebar()">Crear Cita</a>
            </li>
        </ul>
    </div>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="py-5 text-center">
                    <h2>Modificar Cita</h2>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>


                <form method="POST" action="">
                    <h4>Datos de tu cita</h4>
                    
                    <div class="form-group">
                        <label for="motivo">Motivo</label>
                        <input type="text" name="motivo" class="form-control" id="motivo" value="<?php echo htmlspecialchars($motivo); ?>" required >
                    </div>
                    <div class="form-group">
                        <label for="fecha">Fecha</label>
                        <input type="text" name="fecha" class="form-control" id="fecha" value="<?php echo htmlspecialchars($fecha); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="hora">Hora</label>
                        <input type="text" name="hora" class="form-control" id="hora" value="<?php echo htmlspecialchars($hora); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="comentario">Comentario</label>
                        <input type="text" name="comentario" class="form-control" id="comentario" value="<?php echo htmlspecialchars($comentario); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nombre">Id Grupos</label>
                        <input type="text" name="nombre" class="form-control" id="nombre" value="<?php echo htmlspecialchars($grupo_nombre); ?>" readonly>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Actualizar Estatus de Cita</button>
                    </div>
                    <br>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }

        function hideSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth < 992) {
                sidebar.classList.remove('show');
            }
        }
    </script>
</body>

</html>