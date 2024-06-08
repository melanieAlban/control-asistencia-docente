<?php
session_start();
if (empty($_SESSION["id"])) {
    header("Location: Interfazlogin.php");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Asistencia</title>
    <!-- Incluir CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5">
            <h1>Bienvenido/a <?php echo $_SESSION["nombre"] . ' ' . $_SESSION["apellido"]; ?></h1>
            <a href="../Controladores/cerrar_sesion_controller.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
        
        <!-- Reloj en vivo -->
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

        <!-- Información del horario del usuario -->
        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card-title">Tus Horarios de Trabajo</h4>
                <?php
                include("../Controladores/obtener_horarios.php");
                ?>
                <p>Jornada Matutina: <?php echo $jornadaMatutina; ?></p>
                <p>Jornada Vespertina: <?php echo $jornadaVespertina; ?></p>
            </div>
        </div>
    </div>

        <!-- Botones para registrar entrada y salida -->
        <div class="card mt-3">
            <div class="card-body text-center">
                <button class="btn btn-success me-2" onclick="registrarEntrada()">Registrar Entrada</button>
                <button class="btn btn-danger" onclick="registrarSalida()">Registrar Salida</button>
            </div>
        </div>

        <!-- Sección para verificar registros -->
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Verificar Registro de Hoy</h5>
                <div id="verificarResultado" class="mt-3"></div>
            </div>
        </div>

        <!-- Opciones para reportes -->
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

        <!-- Resultados de reportes -->
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
                        <!-- Los registros se añadirán aquí -->
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
                        <!-- Los registros se añadirán aquí -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Incluir JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Datos simulados para la semana
        var registros = [
            { dia: 'Lunes', entradaM: '07:00', salidaM: '13:00', entradaV: '14:00', salidaV: '20:00', horasTrabajadas: 12, fecha: '2023-05-29' },
            { dia: 'Martes', entradaM: '07:00', salidaM: '13:00', entradaV: '14:00', salidaV: '20:00', horasTrabajadas: 12, fecha: '2023-05-30' },
            { dia: 'Miércoles', entradaM: '07:00', salidaM: '13:00', entradaV: '14:00', salidaV: '20:00', horasTrabajadas: 12, fecha: '2023-05-31' },
            { dia: 'Jueves', entradaM: '07:00', salidaM: '13:00', entradaV: '14:00', salidaV: '20:00', horasTrabajadas: 12, fecha: '2023-06-01' },
            { dia: 'Viernes', entradaM: '07:00', salidaM: '13:00', entradaV: '14:00', salidaV: '20:00', horasTrabajadas: 12, fecha: '2023-06-02' }
        ];

        // Emulación del registro del día de hoy con entrada y salida en la jornada matutina, pero solo entrada en la jornada vespertina
        var now = new Date();
        var today = now.toISOString().split('T')[0];
        registros.push({
            dia: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"][now.getDay()],
            entradaM: '07:00',
            salidaM: '13:00',
            entradaV: '14:00',
            salidaV: '', // No se ha registrado la salida vespertina
            horasTrabajadas: 6, // Solo se cuentan las horas matutinas
            fecha: today
        });

        window.onload = function() {
            verificarRegistroHoy();
        };

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

        function registrarEntrada() {
            // Lógica para registrar la entrada
        }

        function registrarSalida() {
            // Lógica para registrar la salida
        }

        function mostrarReporteSemanal() {
            var resultado = document.getElementById('reporteSemanal');
            resultado.innerHTML = '';
            var registrosSemana = registros.slice(-7); // Últimos 7 registros para el reporte semanal

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
