<?php
session_start();
if (empty($_SESSION["id"])) {
    header("Location: Interfazlogin.php");
    exit();
}
require_once('../Controladores/resgistro_asistencia.php');

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
    <title>Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<style>
    .navbar-vino {
    background-color: #800020; 
}

.navbar-vino .navbar-nav .nav-link {
    color: white;
}

.navbar-vino .navbar-brand {
    color: white;
}

.navbar-vino .navbar-nav .nav-link:hover,
.navbar-vino .navbar-brand:hover {
    color: #f8f9fa; 
}
.btn-vino {
    background-color: #800020; 
    color: white;
    border: none;
    
}

.btn-vino:hover {
    background-color: #800020; 
}

</style>
<body>
    <nav class="navbar navbar-expand-lg navbar-vino">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Control de Asistencia</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-3">
                        <a class="nav-link" href="interfazRegistroSalida.php">Registro</a>
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

    <div class="container mt-5">
        <h1>Reportes</h1>
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Reportes</h5>
                <div class="mb-3">
                    <form id="reporteSemanalForm" method="POST" action="../Reportes/ReporteSemanal.php" onsubmit="return validarFechaSemana()" target="_blank">
                        <input type="hidden" name="cedula" value="<?php echo $_SESSION['cedula']; ?>" />
                        <input type="week" id="semanaReporte" name="semanaReporte" class="form-control" required />
                        <button type="submit" class="btn btn-vino mt-2">Imprimir Reporte Semanal</button>
                    </form>
                </div>
                <div>
                    <label for="mesReporte" class="form-label">Selecciona el Mes:</label>
                    <form id="reporteMensualForm" method="POST" action="../Reportes/ReporteMensual.php" target="_blank" onsubmit="return validarFecha()">
                        <input type="month" id="mesReporte" name="mesReporte" class="form-control" required />
                        <input type="hidden" name="cedula" value="<?php echo $_SESSION['cedula']; ?>" />
                        <button type="submit" class="btn btn-vino mt-2">Imprimir Reporte Mensual</button>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Reporte Semanal Actual</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Fecha</th>
                                <th scope="col">Entrada Matutina</th>
                                <th scope="col">Salida Matutina</th>
                                <th scope="col">Entrada Vespertina</th>
                                <th scope="col">Salida Vespertina</th>
                                <th scope="col">Horas Trabajadas</th>
                            </tr>
                        </thead>
                        <tbody id="reporteSemanal">
                            <?php if (empty($reporteSemanal)) { ?>
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron registros de esta semana</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($reporteSemanal as $registro) { ?>
                                    <tr>
                                        <td><?php echo $registro['fecha']; ?></td>
                                        <td><?php echo $registro['entradaM']; ?></td>
                                        <td><?php echo $registro['salidaM']; ?></td>
                                        <td><?php echo $registro['entradaV']; ?></td>
                                        <td><?php echo $registro['salidaV']; ?></td>
                                        <td><?php echo $registro['horasTrabajadas']; ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Reporte Mensual Actual</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Fecha</th>
                                <th scope="col">Entrada Matutina</th>
                                <th scope="col">Salida Matutina</th>
                                <th scope="col">Entrada Vespertina</th>
                                <th scope="col">Salida Vespertina</th>
                                <th scope="col">Horas Trabajadas</th>
                            </tr>
                        </thead>
                        <tbody id="reporteMensual">
                            <?php if (empty($reporteMensual)) { ?>
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron registros de este mes</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($reporteMensual as $registro) { ?>
                                    <tr>
                                        <td><?php echo $registro['fecha']; ?></td>
                                        <td><?php echo $registro['entradaM']; ?></td>
                                        <td><?php echo $registro['salidaM']; ?></td>
                                        <td><?php echo $registro['entradaV']; ?></td>
                                        <td><?php echo $registro['salidaV']; ?></td>
                                        <td><?php echo $registro['horasTrabajadas']; ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
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
                return false; 
            }
            return true;
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
                return false;
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
                return false;
            }
            return true;
        }
    </script>
</body>

</html>
