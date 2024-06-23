<?php
session_start();
if (empty($_SESSION["id"])) {
    header("Location: Interfazlogin.php");
    exit();
}

// Verificar si el usuario tiene el rol de empleado o administrador
if ($_SESSION['rol'] == 'empleado') {
    // Si el usuario es un empleado, redirigir a la interfaz del empleado
    header("Location: interfazRegistroSalida.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Turnos y Docentes</title>
    <!-- Incluir CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .header {
            background-color: #800020;
            color: white;
            padding: 10px 15px;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
            position: fixed;
            
        }
        .sidebar .nav-item {
            margin-bottom: 10px;
        }
        .sidebar .nav-link {
            color: white;
            display: flex;
            align-items: center;
            padding: 10px;
            font-size: 18px;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
        }
        .btn-color {
            background-color: #800020;
            border-color: #800020;
        }
        .btn-color:hover {
            background-color: #5a0014;
            border-color: #5a0014;
        }
        .sidebar-img {
            width: 80%;
            padding: 10px;
            border-radius: 50%;
        }
        .sidebar h5 {
            color: white;
            text-align: center;
            margin-top: 10px;
        }
        .sidebar-footer {
            margin-top: auto;
            padding: 10px 0;
            background-color: #343a40;
            color: white;
        }
    </style>
</head>
<body>
<div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 d-none d-md-block sidebar">
                <div class="text-center">
                    <img src="../img/uta.png" alt="Logo" class="sidebar-img">
                    <h5>Universidad Técnica de Ambato</h5>
                </div>
                <div class="position-sticky pt-3 flex-grow-1">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">
                                <i class="fas fa-user-plus"></i> Agregar Docente
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="listaDocentes.php">
                                <i class="fas fa-list"></i> Lista de Docentes
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="sidebar-footer">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../Controladores/cerrar_sesion_controller.php">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Page Content -->
            <main class="col-md-10 ms-sm-auto col-lg-10 px-md-2">
                <div class="header">
                    <h1 class="text-center">Gestor de Turnos y Docentes</h1>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Información del Docente y Horarios</h5>
                        <form id="docenteYHorariosForm" action="../Controladores/docenteController.php" method="post">
                            <!-- Información del docente -->
                            <div class="mb-3">
                                <label for="cedulaDocente" class="form-label">Cédula</label>
                                <input type="text" class="form-control" id="cedulaDocente" name="cedulaDocente" placeholder="Número de cédula del docente" maxlength="10" required />
                            </div>
                            <div class="mb-3">
                                <label for="nombreDocente" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombreDocente" name="nombreDocente" maxlength="30" placeholder="Nombre del docente" required />
                            </div>
                            <div class="mb-3">
                                <label for="apellidoDocente" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellidoDocente" name="apellidoDocente" maxlength="30" placeholder="Apellido del docente" required />
                            </div>
                            <!-- Horarios -->
                            <div class="row">
                                <!-- Jornada Matutina -->
                                <div class="col-md-6">
                                    <h6>Jornada Matutina</h6>
                                    <div class="mb-3">
                                        <label for="horaInicioMatutina" class="form-label">Hora de Inicio</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                            <select id="horaInicioMatutina" name="horaInicioMatutina" class="form-control" required></select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="horaFinMatutina" class="form-label">Hora de Fin</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                            <select id="horaFinMatutina" name="horaFinMatutina" class="form-control" required></select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Jornada Vespertina -->
                                <div class="col-md-6">
                                    <h6>Jornada Vespertina</h6>
                                    <div class="mb-3">
                                        <label for="horaInicioVespertina" class="form-label">Hora de Inicio</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                            <select id="horaInicioVespertina" name="horaInicioVespertina" class="form-control" required></select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="horaFinVespertina" class="form-label">Hora de Fin</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                            <select id="horaFinVespertina" name="horaFinVespertina" class="form-control" required></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-color">Guardar Información</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Incluir JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/interfazDocente.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
    </script>
</body>
</html>
