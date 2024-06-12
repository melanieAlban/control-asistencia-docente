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
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Gestor de Turnos y Docentes</title>
        <!-- Incluir CSS de Bootstrap -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
        />
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        />
        
    </head>
    <body>
        
        <div class="container">
            <h1 class="mt-5">Gestor de Turnos y Docentes</h1>

            <!-- Formulario unificado -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">
                        Información del Docente y Horarios
                    </h5>
                    <form id="docenteYHorariosForm" action="../Controladores/docenteController.php" method="post" >
                        <!-- Información del docente -->
                        <div class="mb-3">
                            <label for="cedulaDocente" class="form-label"
                                >Cédula</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                id="cedulaDocente"
                                name="cedulaDocente"
                                placeholder="Número de cédula del docente"
                                maxlength="10" 
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <label for="nombreDocente" class="form-label"
                                >Nombre</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                id="nombreDocente"
                                name="nombreDocente"
                                maxlength="30" 
                                placeholder="Nombre del docente"
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <label for="apellidoDocente" class="form-label"
                                >Apellido</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                id="apellidoDocente"
                                name="apellidoDocente"
                                maxlength="30" 
                                placeholder="Apellido del docente"
                                
                                required
                            />
                        </div>
                        <!-- Horarios -->
                        <div class="row">
                            <!-- Jornada Matutina -->
                            <div class="col-md-6">
                                <h6>Jornada Matutina</h6>
                                <div class="mb-3">
                                    <label
                                        for="horaInicioMatutina"
                                        class="form-label"
                                        >Hora de Inicio</label
                                    >
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i class="far fa-clock"></i
                                        ></span>
                                        <select
                                            id="horaInicioMatutina"
                                            name="horaInicioMatutina"
                                            class="form-control"
                                            required
                                        ></select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label
                                        for="horaFinMatutina"
                                        class="form-label"
                                        >Hora de Fin</label
                                    >
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i class="far fa-clock"></i
                                        ></span>
                                        <select
                                            id="horaFinMatutina"
                                            name="horaFinMatutina"
                                            class="form-control"
                                            required
                                        >
                                            <!-- Opciones de tiempo aquí -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Jornada Vespertina -->
                            <div class="col-md-6">
                                <h6>Jornada Vespertina</h6>
                                <div class="mb-3">
                                    <label
                                        for="horaInicioVespertina"
                                        class="form-label"
                                        >Hora de Inicio</label
                                    >
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i class="far fa-clock"></i
                                        ></span>
                                        <select
                                            id="horaInicioVespertina"
                                            name="horaInicioVespertina"
                                            class="form-control"
                                            required
                                        ></select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label
                                        for="horaFinVespertina"
                                        class="form-label"
                                        >Hora de Fin</label
                                    >
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i class="far fa-clock"></i
                                        ></span>
                                        <select
                                            id="horaFinVespertina"
                                            name="horaFinVespertina"
                                            class="form-control"
                                            required
                                        ></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            Guardar Información
                        </button>
                    </form>
                    
                </div>
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
