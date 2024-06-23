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
    require_once('../Controladores/resgistro_asistencia.php');
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

require_once('../Controladores/resgistro_asistencia.php');

$idEmpleado = $_SESSION['id'];
$fecha = date('Y-m-d');
$registroAsistencia = new RegistroAsistencia();
$horasRegistradas = $registroAsistencia->obtenerHorasRegistradas($idEmpleado, $fecha );
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

<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5">
            <h1>Bienvenido/a <?php echo $_SESSION["nombre"] . ' ' . $_SESSION["apellido"]; ?></h1>
            <a href="../Controladores/cerrar_sesion_controller.php" class="btn btn-danger">Cerrar Sesión</a>
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
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card-title">Tus Horarios de Trabajo</h4>
                <?php
                require_once("../Controladores/obtener_horarios.php");
                ?>
                <p>Jornada Matutina: <?php echo $jornadaMatutina; ?></p>
                <p>Jornada Vespertina: <?php echo $jornadaVespertina; ?></p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body text-center">
                <form method="POST">
                    <button type="submit" name="registrarEntrada" class="btn btn-success me-2">Registrarse</button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Verificar Registro de Hoy</h5>
                <div id="verificarResultado" class="mt-3"></div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Reportes</h5>
                <div class="mb-3">
                    <button class="btn btn-secondary" onclick="mostrarReporteSemanal()">Ver Reporte Semanal</button>
                </div>
                <div>
                    <label for="mesReporte" class="form-label">Selecciona el Mes:</label>
                    <input type="month" id="mesReporte" class="form-control" />
                    <button class="btn btn-secondary mt-2" onclick="mostrarReporteMensual()">Ver Reporte Mensual</button>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Resultados del Reporte Semanal</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Día</th>
                            <th scope="col">Entrada Matutina</th>
                            <th scope="col">Salida Matutina</th>
                            <th scope="col">Entrada Vespertina</th>
                            <th scope="col">Salida Vespertina</th>
                            <th scope="col">Horas Trabajadas</th>
                        </tr>
                    </thead>
                    <tbody id="reporteSemanal">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Resultados del Reporte Mensual</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Día</th>
                            <th scope="col">Entrada Matutina</th>
                            <th scope="col">Salida Matutina</th>
                            <th scope="col">Entrada Vespertina</th>
                            <th scope="col">Salida Vespertina</th>
                            <th scope="col">Horas Trabajadas</th>
                        </tr>
                    </thead>
                    <tbody id="reporteMensual">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var registros = <?php echo json_encode($registros); ?>;

        window.onload = function() {
            verificarRegistroHoy();
        };

        <?php if (!empty($mensaje)) { ?>
            Toastify({
                text: "<?php echo $mensaje; ?>",
                duration: 5000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#5162A8",
            }).showToast();
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

            registrosSemana.forEach(function(r) {
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
                    registrosMes.forEach(function(r) {
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
