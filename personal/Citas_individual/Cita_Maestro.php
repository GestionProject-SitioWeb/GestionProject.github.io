<?php
session_start();

if (isset($_SESSION["nombre"])) {
    $nombre_usuario = $_SESSION["nombre"];
    $db = "gestionproject";
    $servidor = "127.0.0.1";
    $usuario = "root";
    $clave = "1234";

    // Conexión a la base de datos
    $conexion = new mysqli($servidor, $usuario, $clave, $db);

    if ($conexion->connect_error) {
        die(json_encode(['error' => "Connection failed: " . $conexion->connect_error]));
    }

    // Consulta para obtener los datos del personal
    $consulta_personal = "SELECT Id_Personal, nombre FROM personales WHERE nombre = ?";
    $stmt = $conexion->prepare($consulta_personal);
    $stmt->bind_param("s", $nombre_usuario);
    $stmt->execute();
    $resultado_personal = $stmt->get_result();

    if ($resultado_personal->num_rows > 0) {
        $fila_personal = $resultado_personal->fetch_assoc();
        $Id_Personal = $fila_personal['Id_Personal'];
        $nombre_personal = $fila_personal['nombre'];
    } else {
        die("Error: No se encontró el docente.");
    }

    // Consulta para obtener todos los estudiantes
    $resultado_estudiantes = $conexion->query("SELECT Id_Estudiante, nombre AS nombre_estudiante FROM estudiantes");
    if ($resultado_estudiantes === false) {
        die("Error en la consulta de estudiantes: " . $conexion->error);
    }

    // Procesamiento del formulario y inserción en la base de datos
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $motivo = $_POST['motivo'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $comentario = $_POST['comentario'];
        $Id_Estudiante = intval($_POST['Id_Estudiante']);  // Aseguramos que sea entero

        // Intento de inserción
        $sql = "INSERT INTO citamaestro (Id_Estudiante, Id_Personal, motivo, fecha, hora, comentario) 
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }

        $stmt->bind_param("iissss", $Id_Estudiante, $Id_Personal, $motivo, $fecha, $hora, $comentario);

        if ($stmt->execute()) {
            // Envía el correo y redirige solo después del envío exitoso
            echo "
                <script src='https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js'></script>
                <script>
                    emailjs.init('dKhMKjQEEIR9BdroO');
                    emailjs.send('default_service', 'template_qatzuy1', {
                        student_name: '$nombre_personal',
                        motivo: '$motivo',
                        fecha: '$fecha',
                        hora: '$hora',
                        comentario: '$comentario'
                    }).then(function() {
                        alert('Mensaje Enviado Correctamente!');
                        window.location.href = 'index.php';
                    }, function(error) {
                        alert('Error al enviar el correo: ' + JSON.stringify(error));
                    });
                </script>";
        } else {
            echo "<script>alert('Error al crear la cita: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }
}
$conexion->close();

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

        /* Sustituir las viejas reglas de alto contraste por forced-colors */
        @media (forced-colors: active) {
            body {
                background-color: black;
                color: white;
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
                        <a class="nav-link" href="index.php">inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../../conexiones/logout.php">Cerrar sesión</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <!-- Barra lateral -->
    <div class="sidebar bg-light" id="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="index.php" onclick="hideSidebar()">inicio</a>
            </li>
        </ul>
    </div>


    <!-- Contenido principal -->
    <div class="container-fluid" id="main-content" style="margin-top: 60px;">
        <div class="container-fluid">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                <div class="mb-3 mb-md-0">
                </div>
            </div>

            <!-- Main Content -->
            <div class="container mt-5">
                <h2 align="center">Crear Cita</h2>
                <br>

                <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
                <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>


                <form method="post" action="" id="form">
                    <div class="mb-3">
                        <label for="Id_Estudiante" class="form-label">Estudiante:</label>
                        <select name="Id_Estudiante" class="custom-select" id="estudianteSelect" required>
                            <option value="">Seleccione un estudiante</option>
                            <?php while ($row = $resultado_estudiantes->fetch_assoc()) { ?>
                                <option value="<?php echo $row['Id_Estudiante']; ?>">
                                    <?php echo $row['nombre_estudiante']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <input type="hidden" name="nombre_personal" class="form-control" value="<?php echo $nombre_personal; ?>">

                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo:</label>
                        <input type="text" name="motivo" placeholder="motivo" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha:</label>
                        <input type="date" name="fecha" placeholder="fecha" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="hora" class="form-label">Hora:</label>
                        <input type="time" name="hora" placeholder="hora" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="comentario" class="form-label">Comentario:</label>
                        <textarea name="comentario" rows="4" placeholder="comentario" class="form-control"></textarea>
                    </div>

                    <button type="submit" id="button" value="crear cita" class="btn btn-primary w-100">crear cita</button>
                </form>


            </div>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                // Función para mostrar/ocultar la barra lateral en pantallas pequeñas
                function toggleSidebar() {
                    const sidebar = document.getElementById('sidebar');
                    sidebar.classList.toggle('show');
                }

                // Ocultar la barra lateral al seleccionar un enlace en pantallas pequeñas
                function hideSidebar() {
                    const sidebar = document.getElementById('sidebar');
                    if (window.innerWidth < 992) {
                        sidebar.classList.remove('show');
                    }
                }
            </script>

</body>

</html>