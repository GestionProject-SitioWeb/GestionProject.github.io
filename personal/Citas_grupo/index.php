<?php
// Inicia la sesión
session_start();

// Verifica si el usuario ha iniciado sesión
if (isset($_SESSION["nombre"]) && isset($_SESSION["apellido"])) {
	// Muestra el nombre y apellido del usuario
	$nombre_usuario = $_SESSION["nombre"];
	$apellido_usuario = $_SESSION["apellido"];

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

	// Consulta SQL para mostrar solo las citas del empleado que inició sesión
	$sql = "SELECT ce.Id_Cita, ce.motivo, ce.fecha, ce.hora, ce.comentario, ce.Id_Grupo as grupos,g.nombre, ce.Id_Personal as personales
			FROM citamaestro_estudiantes ce
			INNER JOIN grupos g ON ce.Id_Grupo = g.Id_Grupo
			INNER JOIN personales p ON ce.Id_Personal = p.Id_Personal
			where p.nombre = ? AND p.apellido = ? ";

	// Preparar y ejecutar la consulta para evitar inyecciones SQL
	$stmt = $conexion->prepare($sql);
	$stmt->bind_param("ss", $nombre_usuario, $apellido_usuario);
	$stmt->execute();
	$resultado = $stmt->get_result();

	// Verificar si la consulta fue exitosa
	if (!$resultado) {
		die("Error en la consulta: " . $conexion->error);
	}

	// Obtener el total de citas encontradas
	$total = $resultado->num_rows;
} else {
	// Si no hay sesión, redirigir al login

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
						<a class="nav-link" href="../index.php">Inicio</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="Cita_grupo.php">Crear Cita</a>
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
				<a class="nav-link" href="../index.php" onclick="hideSidebar()">Inicio</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="Cita_grupo.php" onclick="hideSidebar()">Crear Cita</a>
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
			<div class="container mt-5">
				<h2 align="center">Listado de Citas del Mtro <?php echo htmlspecialchars($nombre_usuario); ?> <?php echo htmlspecialchars($apellido_usuario); ?></h2>
				<br>
				<h2>Lista de Estudiantes <strong>(<?php echo $total; ?>)</strong></h2>
				<br>
				<div class="input-group mb-3">
					<input type="text" class="form-control" id="filtro" placeholder="Search..">
				</div>

				<!-- New Table -->
				<div class="w-full overflow-hidden rounded-lg shadow-xs">
					<div class="w-full overflow-x-auto">
						<table class="w-full whitespace-no-wrap" id="tabla">
							<thead>
								<tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
									<th class="px-4 py-3">Id_Cita</th>
									<th class="px-4 py-3">Motivo</th>
									<th class="px-4 py-3">Fecha</th>
									<th class="px-4 py-3">Hora</th>
									<th class="px-4 py-3">Comentario</th>
									<th class="px-4 py-3">Grupos</th>
									<th class="px-4 py-3">Estatus</th>
								</tr>
							</thead>
							<tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
								<?php if ($resultado->num_rows > 0): ?>
									<?php while ($fila = $resultado->fetch_assoc()): ?>
										<tr class="text-gray-700 dark:text-gray-400">
											<td class="px-4 py-3 text-sm">
												<?php echo $fila['Id_Cita']  ?>
											</td>

											<td class="px-4 py-3 text-sm">
												<?php echo $fila['motivo']; ?>
											</td>

											<td class="px-4 py-3 text-sm">
												<?php echo $fila['fecha']; ?>
											</td>

											<td class="px-4 py-3 text-sm">
												<?php echo $fila['hora']; ?>
											</td>

											<td class="px-4 py-3 text-sm">
												<?php echo $fila['comentario']; ?>
											</td>

											<td class="px-4 py-3 text-sm">
												<?php echo $fila['nombre']; ?>
											</td>

											<td class="px-4 py-3 text-center">
												<a href="modificarcitas.php?id=<?php echo $fila['Id_Cita']; ?>" class="btn btn-warning btn-sm me-2">
													<i class="fas fa-edit"></i>
												</a>
												<a href="#" class="btn btn-danger btn-sm" onclick="eliminarCita(<?php echo $fila['Id_Cita']; ?>)">
													<i class="fas fa-trash-alt"></i>
												</a>
											</td>
										</tr>
									<?php endwhile; ?>
								<?php else: ?>
									<tr>
										<td colspan="7" class="text-center text-gray-700 dark:text-gray-400">No se encontraron citas.</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>

				<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>

				<script>
					// Función para filtrar la tabla
					document.getElementById('filtro').addEventListener('input', function() {
						const filtro = this.value.toLowerCase();
						const filas = document.querySelectorAll('#tabla tbody tr');

						filas.forEach(fila => {
							const textoFila = fila.textContent.toLowerCase();
							fila.style.display = textoFila.includes(filtro) ? '' : 'none';
						});
					});

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
				<script>
					function eliminarCita(id) {
						if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
							fetch("eliminarcitas.php?id=" + id)
								.then(response => {
									if (!response.ok) {
										throw new Error("Error en la solicitud: " + response.status);
									}
									return response.text();
								})
								.then(data => {
									alert(data); // Muestra el mensaje del servidor
									location.reload(); // Recarga la página para reflejar los cambios
								})
								.catch(error => {
									alert("Error: " + error.message);
								});
						}
					}
				</script>

</body>

</html>