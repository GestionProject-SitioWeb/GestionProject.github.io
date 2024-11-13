<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Inicio</title>
    <link rel="shortcut icon" href="ico/escudo.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/estilos.css">
    <link rel="stylesheet" href="assets/css/tailwind.output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <script src="assets/js/init-alpine.js"></script>
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
                <img src="img/escudo.png" alt="Escudo" width="30" height="30" class="d-inline-block align-text-top">
                Gestion de Turnos
            </a>
            <!-- Enlaces de navegación en la barra superior -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
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
                        <a class="nav-link" href="../../conexiones/logout.php" onclick="hideSidebar()">Cerrar sesión</a>
                    </li>
			
		</ul>
	</div>

    <!-- Barra lateral -->
    <div class="container-fluid" id="main-content" style="margin-top: 60px;">
        <div class="container-fluid d-flex justify-content-center" style="margin-top: 44vh;">
            <div class="d-flex flex-column align-items-center">
                <button class="button mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"></path>
                    </svg>
                    <div class="text"><a class="nav-link" href="Citas_grupo/index.php" style="color: #000000;"> Citas por Grupo</a></div>
                </button>

                <button class="button">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"></path>
                    </svg>
                    <div class="text"><a class="nav-link" href="Citas_individual/index.php" style="color: #000000;"> Citas individuales</a></div>
                </button>
            </div>
        </div>
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

    <script>
        $(document).ready(function() {
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();

                // Filtrar filas de la tabla basándose en el valor de entrada
                $("#myTable tr").filter(function() {
                    var rowText = $(this).text().toLowerCase();
                    $(this).toggle(rowText.indexOf(value) > -1);
                });

                // Mostrar u ocultar la tabla completa si no hay filas visibles
                $('table').each(function(index, table) {
                    if ($(table).find('tbody tr:visible').length > 0) {
                        $(table).css('display', 'table');
                    } else {
                        $(table).css('display', 'none');
                    }
                });
            });
        });
    </script>


</body>

</html>