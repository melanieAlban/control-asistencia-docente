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
                require_once ("../Controladores/obtener_horarios.php");
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
            <form id="reporteSemanalForm" method="POST" action="../Reportes/ReporteSemanal.php"  onsubmit="return validarFechaSemana()" target="_blank">
                <input type="hidden" name="cedula" value="<?php echo $_SESSION['cedula']; ?>" />
                <input type="week" id="semanaReporte" name="semanaReporte" class="form-control" required />
                <button type="submit" class="btn btn-primary mt-2">Imprimir Reporte Semanal</button>
            </form>
            <button class="btn btn-primary mt-2" onclick="mostrarReporteSemanal()">Ver Reporte Semanal</button>
        </div>
        <div>
            <label for="mesReporte" class="form-label">Selecciona el Mes:</label>
            <form id="reporteMensualForm" method="POST" action="../Reportes/ReporteMensual.php" target="_blank" onsubmit="return validarFecha()"> 
            <input type="month" id="mesReporte" name="mesReporte" class="form-control" required />
                <input type="hidden" name="cedula" value="<?php echo $_SESSION['cedula']; ?>" />
                <button type="submit" class="btn btn-primary mt-2">Imprimir Reporte Mensual</button>
            </form>
            <button class="btn btn-primary mt-2" onclick="mostrarReporteMensual()">Ver Reporte Mensual</button>
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
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
                return false; // Evita que el formulario se envíe
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
        function agregarFechasSemana() {
            const ahora = new Date();
            const diaSemana = ahora.getDay(); // 0 (domingo) a 6 (sábado)
            const diferenciaLunes = (diaSemana === 0 ? -6 : 1) - diaSemana; // Diferencia en días hasta el lunes más cercano
            const diferenciaDomingo = 7 - diaSemana - (diaSemana === 0 ? 7 : 0); // Diferencia en días hasta el domingo más cercano

            const primerDiaSemana = new Date(ahora);
            primerDiaSemana.setDate(ahora.getDate() + diferenciaLunes);

            const ultimoDiaSemana = new Date(ahora);
            ultimoDiaSemana.setDate(ahora.getDate() + diferenciaDomingo);

            const fechaInicioSemana = primerDiaSemana.toISOString().split('T')[0];
            const fechaFinSemana = ultimoDiaSemana.toISOString().split('T')[0];

            document.getElementById('fecha_inicio_semana').value = fechaInicioSemana;
            document.getElementById('fecha_fin_semana').value = fechaFinSemana;

            return true; // Permite que el formulario se envíe
        }

        document.addEventListener('DOMContentLoaded', function() {
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
        var registros = [{
            dia: 'Lunes',
            entradaM: '07:00',
            salidaM: '13:00',
            entradaV: '14:00',
            salidaV: '20:00',
            horasTrabajadas: 12,
            fecha: '2023-05-29'
        },
        {
            dia: 'Martes',
            entradaM: '07:00',
            salidaM: '13:00',
            entradaV: '14:00',
            salidaV: '20:00',
            horasTrabajadas: 12,
            fecha: '2023-05-30'
        },
        {
            dia: 'Miércoles',
            entradaM: '07:00',
            salidaM: '13:00',
            entradaV: '14:00',
            salidaV: '20:00',
            horasTrabajadas: 12,
            fecha: '2023-05-31'
        },
        {
            dia: 'Jueves',
            entradaM: '07:00',
            salidaM: '13:00',
            entradaV: '14:00',
            salidaV: '20:00',
            horasTrabajadas: 12,
            fecha: '2023-06-01'
        },
        {
            dia: 'Viernes',
            entradaM: '07:00',
            salidaM: '13:00',
            entradaV: '14:00',
            salidaV: '20:00',
            horasTrabajadas: 12,
            fecha: '2023-06-02'
        }
        ];

        var now = new Date();
        var today = now.toISOString().split('T')[0];
        registros.push({
            dia: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"][now.getDay()],
            entradaM: '07:00',
            salidaM: '13:00',
            entradaV: '14:00',
            salidaV: '',
            horasTrabajadas: 6,
            fecha: today
        });

        window.onload = function () {
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
            var now = new Date();
            var today = now.toISOString().split('T')[0];
            var registro = registros.find(r => r.fecha === today);
            var resultado = document.getElementById('verificarResultado');

            if (registro) {
                resultado.innerHTML = `
                    <p><strong>Entrada Matutina:</strong> ${registro.entradaM}</p>
                    <p><strong>Salida Matutina:</strong> ${registro.salidaM}</p>
                    <p><strong>Entrada Vespertina:</strong> ${registro.entradaV}</p>
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