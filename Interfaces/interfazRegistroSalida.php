<?php
session_start();
if (empty($_SESSION["id"])) {
    header("Location: Interfazlogin.php");
    exit();
}
if ($_SESSION['rol'] == 'administrador') {
    header("Location: interfazRegistroDocente.php");
    exit();
}

function registrarAsistencia($tipo)
{
    require_once ('../Controladores/resgistro_asistencia.php');
    $registroAsitencia = new RegistroAsistencia();
    $mensaje = $registroAsitencia->registrarAsistencia($_SESSION['id']);
    return $mensaje;
}

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['registrarEntrada'])) {
        $mensaje = registrarAsistencia('entrada');
    } else if (isset($_POST['registrarSalida'])) {
        $mensaje = registrarAsistencia('salida');
    }
}

require_once ('../Controladores/resgistro_asistencia.php');

$idEmpleado = $_SESSION['id'];
$fecha = date('Y-m-d');
$registroAsistencia = new RegistroAsistencia();
$horasRegistradas = $registroAsistencia->obtenerHorasRegistradas($idEmpleado, $fecha);
$registros = [];

if ($horasRegistradas) {
    $registro = [
        'entradaM' => '',
        'salidaM' => '',
        'entradaV' => '',
        'salidaV' => '',
    ];

    foreach ($horasRegistradas as $hora) {
        if ($hora['jornada'] == 'MAT') {
            $registro['entradaM'] = $hora['hora_ingreso'];
            $registro['salidaM'] = $hora['hora_salida'];
        } elseif ($hora['jornada'] == 'VES') {
            $registro['entradaV'] = $hora['hora_ingreso'];
            $registro['salidaV'] = $hora['hora_salida'];
        }
    }

    $registros[] = $registro;
}

require_once ('../Controladores/resgistro_asistencia.php');
$controladorReportes = new RegistroAsistencia();
$cedulaE = $_SESSION['cedula'];
$reporteMensual = $controladorReportes->reporteMensual($cedulaE);
$reporteSemanal = $controladorReportes->reporteSemanal($cedulaE);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<style>
    .navbar-vino {
    background-color: #800020; /* Color vino */
}

.navbar-vino .navbar-nav .nav-link {
    color: white;
}

.navbar-vino .navbar-brand {
    color: white;
}

.navbar-vino .navbar-nav .nav-link:hover,
.navbar-vino .navbar-brand:hover {
    color: #f8f9fa; /* Color blanco más claro al pasar el ratón */
}

</style>
<body>
<nav class="navbar navbar-expand-lg navbar-vino ">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Control de Asistencia</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-3">
                        <a class="nav-link" href="#">Registro</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link" href="reportes.php">Reportes</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link btn btn-secondary text-white" href="../Controladores/cerrar_sesion_controller.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5">
            <h1>Bienvenido/a <?php echo $_SESSION["nombre"] . ' ' . $_SESSION["apellido"]; ?></h1>
            
        </div>

        <div class="card text-center mt-3">
            <div class="card-header">
                Fecha y Hora Actual
            </div>
            <div class="card-body">
                <h5 class="card-title" id="liveClock">Cargando...</h5>
                <script>
                    function updateClock() {
                        var now = new Date();
                        var days = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
                        var day = days[now.getDay()];
                        var date = now.toLocaleDateString();
                        var hours = now.getHours().toString().padStart(2, '0');
                        var minutes = now.getMinutes().toString().padStart(2, '0');
                        var seconds = now.getSeconds().toString().padStart(2, '0');
                        document.getElementById('liveClock').textContent = day + ', ' + date + ' - ' + hours + ':' + minutes + ':' + seconds;
                    }
                    setInterval(updateClock, 1000);
                </script>
                <form method="POST">
                    <button type="submit" name="registrarEntrada" class="btn btn-primary me-2 mt-3">Registrarse</button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card-title">Tus Horarios de Trabajo</h4>
                <?php
                require_once ("../Controladores/obtener_horarios.php");
                ?>
                <p>Jornada Matutina: <?php echo $jornadaMatutina; ?></p>
                <p>Jornada Vespertina: <?php echo $jornadaVespertina; ?></p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body text-center">

            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Verificar Registro de Hoy</h5>
                <div id="verificarResultado" class="mt-3"></div>
            </div>
        </div>

        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            var registros = <?php echo json_encode($registros); ?>;

            function validarFecha() {
                const mesReporte = document.getElementById('mesReporte').value;
                const fechaSeleccionada = new Date(mesReporte);
                const fechaActual = new Date();

                fechaSeleccionada.setHours(0, 0, 0, 0);
                fechaActual.setHours(0, 0, 0, 0);

                if (fechaSeleccionada > fechaActual) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No puede seleccionar un mes en el futuro.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return false;
                }
                return true; // Permite que el formulario se envíe
            }

            function validarFechaSemana() {
                const semanaReporte = document.getElementById('semanaReporte').value;
                if (!semanaReporte) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Debe seleccionar una semana.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return false; // Evita que el formulario se envíe
                }

                const year = parseInt(semanaReporte.substring(0, 4));
                const week = parseInt(semanaReporte.substring(6));
                const fechaInicioSemana = new Date(year, 0, (1 + (week - 1) * 7));
                const dayOfWeek = fechaInicioSemana.getDay();
                const diffToMonday = fechaInicioSemana.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
                const fechaInicio = new Date(fechaInicioSemana.setDate(diffToMonday));

                const fechaActual = new Date();
                fechaInicio.setHours(0, 0, 0, 0);
                fechaActual.setHours(0, 0, 0, 0);

                if (fechaInicio > fechaActual) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No puede seleccionar una semana en el futuro.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return false; // Evita que el formulario se envíe
                }
                return true; // Permite que el formulario se envíe
            }

            function validarSalida() {
                const mesReporte = document.getElementById('semanaReporte').value;
                const fechaSeleccionada = new Date(mesReporte);
                const fechaActual = new Date();

                fechaSeleccionada.setHours(0, 0, 0, 0);
                fechaActual.setHours(0, 0, 0, 0);

                if (fechaSeleccionada > fechaActual) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No puede seleccionar un mes en el futuro.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return false; // Evita que el formulario se envíe
                }
                return true; // Permite que el formulario se envíe
            }

            document.addEventListener('DOMContentLoaded', function () {
                <?php
                if (isset($_SESSION['success'])) {
                    echo "Swal.fire({
                    title: 'Éxito',
                    text: '" . $_SESSION['success'] . "',
                    icon: 'success'
                });";
                    unset($_SESSION['success']);
                } elseif (isset($_SESSION['error'])) {
                    echo "Swal.fire({
                    title: 'Error',
                    text: '" . $_SESSION['error'] . "',
                    icon: 'error'
                });";
                    unset($_SESSION['error']);
                }
                ?>
            });


            window.onload = function () {
                verificarRegistroHoy();
            };

            <?php if (!empty($mensaje)) { ?>
                Swal.fire({
                    icon: 'info', // Icono de la alerta, en este caso 'success' que muestra un ícono de verificación.
                    title: 'OK', // Título de la alerta.
                    text: "<?php echo $mensaje; ?>", // Texto de la alerta, muestra el contenido de la variable $mensaje.
                    showConfirmButton: false, // No muestra el botón de confirmar para que se cierre automáticamente.
                    timer: 2500, // Duración de la alerta antes de cerrarse automáticamente, 2000 milisegundos (2 segundos).
                    timerProgressBar: true,
                })
            <?php } ?>

            function verificarRegistroHoy() {
                //la consulta que hice, ya llama con la fecha de hoy asi que aqui no hay que buscar todos
                //el sql ya devuelve la fecha de hoy
                var resultado = document.getElementById('verificarResultado');
                if (registros.length > 0) {
                    var registro = registros[0];
                    resultado.innerHTML = `
                    <p><strong>Entrada Matutina:</strong> ${registro.entradaM || 'No registrada'}</p>
                    <p><strong>Salida Matutina:</strong> ${registro.salidaM || 'No registrada'}</p>
                    <p><strong>Entrada Vespertina:</strong> ${registro.entradaV || 'No registrada'}</p>
                    <p><strong>Salida Vespertina:</strong> ${registro.salidaV || 'No registrada'}</p>
                `;
                } else {
                    resultado.textContent = 'No se encontraron registros para el día de hoy.';
                }
            }

            function mostrarReporteSemanal() {
                var resultado = document.getElementById('reporteSemanal');
                resultado.innerHTML = '';
                var registrosSemana = registros.slice(-7);

                registrosSemana.forEach(function (r) {
                    resultado.innerHTML += `
                    <tr>
                        <td>${r.dia}</td>
                        <td>${r.entradaM}</td>
                        <td>${r.salidaM}</td>
                        <td>${r.entradaV}</td>
                        <td>${r.salidaV || 'No registrada'}</td>
                        <td>${r.horasTrabajadas}</td>
                    </tr>
                `;
                });
            }

            function mostrarReporteMensual() {
                var mesSeleccionado = document.getElementById('mesReporte').value;
                var resultado = document.getElementById('reporteMensual');
                resultado.innerHTML = '';

                if (mesSeleccionado) {
                    var registrosMes = registros.filter(r => r.fecha.startsWith(mesSeleccionado));

                    if (registrosMes.length > 0) {
                        registrosMes.forEach(function (r) {
                            resultado.innerHTML += `
                            <tr>
                                <td>${r.dia}</td>
                                <td>${r.entradaM}</td>
                                <td>${r.salidaM}</td>
                                <td>${r.entradaV}</td>
                                <td>${r.salidaV || 'No registrada'}</td>
                                <td>${r.horasTrabajadas}</td>
                            </tr>
                        `;
                        });
                    } else {
                        resultado.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron registros para el mes seleccionado.</td></tr>';
                    }
                } else {
                    resultado.innerHTML = '<tr><td colspan="6" class="text-center">Por favor, selecciona un mes válido.</td></tr>';
                }
            }
        </script>
</body>

</html>