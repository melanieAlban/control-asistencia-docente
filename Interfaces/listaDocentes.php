<?php
session_start();
if (empty($_SESSION["id"])) {
    header("Location: Interfazlogin.php");
    exit();
}

// Verificar si el usuario tiene el rol de empleado o administrador
if ($_SESSION['rol'] == 'empleado') {
    header("Location: interfazRegistroSalida.php");
    exit();
}

require_once '../servicios/conexion.php';
require_once '../Controladores/obtenerDocentes.php';

$docentes = obtenerListaDocentes();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Docentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
        .table-icons {
            text-align: center;
        }
        .icon-edit {
            color: #ffc107;
        }
        .icon-report {
            color: #800020;
        }
        .icon-delete {
            color: #6c757d;
        }
        .swal2-confirm {
            background-color: #800020 !important;
            border-color: #800020 !important;
        }
        .swal2-cancel {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
        }
        .icon-edit:hover,
        .icon-report:hover,
        .icon-delete:hover {
            color: inherit;
            text-decoration: none;
        }
        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }
        #searchCedula {
            width: 50%;
            max-width: 300px;
        }
        .modal-body {
            max-height: 600px;
            overflow-y: auto;
        }
        .btn-close {
            background: none;
            border: none;
            font-size: 1.5rem;
        }
        .modal-content {
            border-radius: 1rem;
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
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="interfazRegistroDocente.php">
                                <i class="fas fa-user-plus"></i> Agregar Docente
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="listaDocentes.php">
                                <i class="fas fa-list"></i> Lista de Docentes
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="logout">
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
            <main class="col-md-10 ms-sm-auto col-lg-10 px-md-0">
                <div class="header">
                    <h1 class="text-center">Lista de Docentes</h1>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="search-container">
                            <input type="text" class="form-control" placeholder="Buscar por cédula" id="searchCedula">
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Cédula</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="docentesTableBody">
                                <?php
                                if (!empty($docentes)) {
                                    $contador = 1;
                                    foreach ($docentes as $docente) {
                                        echo "<tr>
                                            <td>{$contador}</td>
                                            <td>{$docente['nombre']}</td>
                                            <td>{$docente['apellido']}</td>
                                            <td>{$docente['cedula']}</td>
                                            <td class='table-icons'>
                                                <a href='#' class='icon-edit' data-bs-toggle='modal' data-bs-target='#editDocenteModal' data-id='{$docente['id']}'><i class='fas fa-edit'></i></a>
                                                <a href='#' class='icon-delete' data-id='{$docente['id']}'><i class='fas fa-trash'></i></a>
                                                <a href='#' class='icon-report'><i class='fas fa-file-alt'></i></a>
                                            </td>
                                        </tr>";
                                        $contador++;
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No hay datos disponibles</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <!-- Modal para editar docente -->
    <div class="modal fade" id="editDocenteModal" tabindex="-1" aria-labelledby="editDocenteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDocenteModalLabel">Editar Docente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editDocenteForm">
                        <div class="mb-3">
                            <label for="editCedulaDocente" class="form-label">Cédula</label>
                            <input type="hidden" id="editIdDocente" name="idDocente" />
                            <input type="text" class="form-control" id="editCedulaDocente" name="cedulaDocente" readonly />
                        </div>

                        <div class="mb-3">
                            <label for="editNombreDocente" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editNombreDocente" name="nombreDocente" required />
                        </div>
                        <div class="mb-3">
                            <label for="editApellidoDocente" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="editApellidoDocente" name="apellidoDocente" required />
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Jornada Matutina</h6>
                                <div class="mb-3">
                                    <label for="editHoraInicioMatutina" class="form-label">Hora de Inicio</label>
                                    <select id="editHoraInicioMatutina" name="horaInicioMatutina" class="form-control" required></select>
                                </div>
                                <div class="mb-3">
                                    <label for="editHoraFinMatutina" class="form-label">Hora de Fin</label>
                                    <select id="editHoraFinMatutina" name="horaFinMatutina" class="form-control" required></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Jornada Vespertina</h6>
                                <div class="mb-3">
                                    <label for="editHoraInicioVespertina" class="form-label">Hora de Inicio</label>
                                    <select id="editHoraInicioVespertina" name="horaInicioVespertina" class="form-control" required></select>
                                </div>
                                <div class="mb-3">
                                    <label for="editHoraFinVespertina" class="form-label">Hora de Fin</label>
                                    <select id="editHoraFinVespertina" name="horaFinVespertina" class="form-control" required></select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-color">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function updateTimeOptions(element, startTime, endTime) {
                let options = "";
                let current = new Date(`2021-01-01T${startTime}:00`);
                let end = new Date(`2021-01-01T${endTime}:00`);

                while (current <= end) {
                    let hour = current.getHours();
                    let value = (hour < 10 ? "0" + hour : hour) + ":00"; // Solo se considera la hora completa
                    options += `<option value="${value}">${value}</option>`;
                    current.setHours(current.getHours() + 1); // Incrementa en 1 hora
                }

                element.innerHTML = options;
                element.value = startTime; // Establece un valor inicial
            }

            function calculateHours(startTime, endTime) {
                const start = startTime.split(':');
                const end = endTime.split(':');
                const startDate = new Date(0, 0, 0, start[0], start[1], 0);
                const endDate = new Date(0, 0, 0, end[0], end[1], 0);
                let diff = endDate.getTime() - startDate.getTime();
                let hours = diff / 1000 / 60 / 60;
                if (hours < 0) hours += 24;
                return hours;
            }

            function addEditListeners() {
                document.querySelectorAll('.icon-edit').forEach(function(element) {
                    element.addEventListener('click', function() {
                        const docenteId = this.getAttribute('data-id');
                        fetch(`../Controladores/obtenerDocente.php?id=${docenteId}`)
                            .then(response => response.json())
                            .then(data => {
                                openEditModal(data);
                            })
                            .catch(error => console.error('Error:', error));
                    });
                });
            }

            function addDeleteListeners() {
                document.querySelectorAll('.icon-delete').forEach(function(element) {
                    element.addEventListener('click', function() {
                        const docenteId = this.getAttribute('data-id');
                        Swal.fire({
                            title: '¿Está seguro?',
                            text: "No podrá revertir esto",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#800020',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Sí, eliminarlo'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch('../Controladores/eliminarDocente.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({ id: docenteId, estado: 'INA' })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire(
                                            'Eliminado',
                                            'El docente ha sido eliminado.',
                                            'success'
                                        ).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire(
                                            'Error',
                                            'Hubo un problema al eliminar el docente.',
                                            'error'
                                        );
                                    }
                                })
                                .catch(error => console.error('Error:', error));
                            }
                        });
                    });
                });
            }

            document.getElementById('searchCedula').addEventListener('input', function() {
                const cedula = this.value;
                fetch(`../Controladores/obtenerDocentes.php?cedula=${cedula}`)
                    .then(response => response.json())
                    .then(data => {
                        const tableBody = document.getElementById('docentesTableBody');
                        tableBody.innerHTML = '';
                        let contador = 1;
                        if (data.length > 0) {
                            data.forEach(docente => {
                                const row = `<tr>
                                    <td>${contador}</td>
                                    <td>${docente.nombre}</td>
                                    <td>${docente.apellido}</td>
                                    <td>${docente.cedula}</td>
                                    <td class='table-icons'>
                                        <a href='#' class='icon-edit' data-bs-toggle='modal' data-bs-target='#editDocenteModal' data-id='${docente.id}'><i class='fas fa-edit'></i></a>
                                        <a href='#' class='icon-delete' data-id='${docente.id}'><i class='fas fa-trash'></i></a>
                                        <a href='#' class='icon-report'><i class='fas fa-file-alt'></i></a>
                                    </td>
                                </tr>`;
                                tableBody.insertAdjacentHTML('beforeend', row);
                                contador++;
                            });
                            addEditListeners();
                            addDeleteListeners();
                        } else {
                            tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No hay datos disponibles</td></tr>';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            document.getElementById('editNombreDocente').addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                if (this.value.length > 30) {
                    this.value = this.value.slice(0, 30);
                }
            });

            document.getElementById('editApellidoDocente').addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                if (this.value.length > 30) {
                    this.value = this.value.slice(0, 30);
                }
            });

            function openEditModal(docente) {
                document.getElementById('editIdDocente').value = docente.id; // Añadir id del docente
                document.getElementById('editCedulaDocente').value = docente.cedula;
                document.getElementById('editNombreDocente').value = docente.nombre;
                document.getElementById('editApellidoDocente').value = docente.apellido;

                const inicioMatutina = document.getElementById("editHoraInicioMatutina");
                const finMatutina = document.getElementById("editHoraFinMatutina");
                updateTimeOptions(inicioMatutina, "07:00", "13:00");
                updateTimeOptions(finMatutina, "07:00", "13:00");

                const inicioVespertina = document.getElementById("editHoraInicioVespertina");
                const finVespertina = document.getElementById("editHoraFinVespertina");
                updateTimeOptions(inicioVespertina, "14:00", "22:00");
                updateTimeOptions(finVespertina, "14:00", "22:00");

                if (docente.entradaMat) {
                    inicioMatutina.value = docente.entradaMat.slice(0, 5); // Ajustar formato HH:MM
                }
                if (docente.salidaMat) {
                    finMatutina.value = docente.salidaMat.slice(0, 5); // Ajustar formato HH:MM
                }
                if (docente.entradaVes) {
                    inicioVespertina.value = docente.entradaVes.slice(0, 5); // Ajustar formato HH:MM
                }
                if (docente.salidaVes) {
                    finVespertina.value = docente.salidaVes.slice(0, 5); // Ajustar formato HH:MM
                }

                $('#editDocenteModal').modal('show');
            }

            addEditListeners();
            addDeleteListeners();

            document.getElementById('editDocenteForm').addEventListener('submit', function(event) {
                event.preventDefault();

                const horaInicioMatutina = document.getElementById("editHoraInicioMatutina").value;
                const horaFinMatutina = document.getElementById("editHoraFinMatutina").value;
                const horaInicioVespertina = document.getElementById("editHoraInicioVespertina").value;
                const horaFinVespertina = document.getElementById("editHoraFinVespertina").value;

                const horasMatutina = calculateHours(horaInicioMatutina, horaFinMatutina);
                const horasVespertina = calculateHours(horaInicioVespertina, horaFinVespertina);
                const totalHoras = horasMatutina + horasVespertina;

                if (totalHoras !== 8) {
                    Swal.fire({
                        title: 'Error',
                        text: 'La jornada debe ser 8 horas. Actualmente suma: ' + totalHoras + ' horas.',
                        icon: 'error'
                    });
                    return;
                }

                const formData = new FormData(this);

                fetch('../Controladores/actualizarDocente.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Éxito',
                            text: 'El docente se ha actualizado correctamente.',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al actualizar el docente: ' + data.message,
                            icon: 'error'
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>
</html>
