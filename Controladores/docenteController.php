<?php
session_start(); // Esto debe estar al principio, antes de cualquier salida.
include '../servicios/conexion.php';

if (isset($_POST['cedulaDocente'], $_POST['nombreDocente'], $_POST['apellidoDocente'],
    $_POST['horaInicioMatutina'], $_POST['horaFinMatutina'], $_POST['horaInicioVespertina'],
    $_POST['horaFinVespertina'])) {

    $conn = new Conexion();
    $con = $conn->conectar();

    $sqlDocente = $con->prepare("INSERT INTO empleados (nombre, apellido, cedula, password, estado, rol) VALUES (?, ?, ?, ?, 'ACT', 'empleado')");
    $sqlDocente->bind_param("ssss", $_POST['nombreDocente'], $_POST['apellidoDocente'], $_POST['cedulaDocente'], $_POST['cedulaDocente']);

    if ($sqlDocente->execute()) {
        $idEmpleado = $con->insert_id;
        $sqlHorario = $con->prepare("INSERT INTO horarios (entrada, salida, jornada, id_empleado) VALUES (?, ?, 'MAT', ?), (?, ?, 'VES', ?)");
        $sqlHorario->bind_param("ssissi", $_POST['horaInicioMatutina'], $_POST['horaFinMatutina'], $idEmpleado, $_POST['horaInicioVespertina'], $_POST['horaFinVespertina'], $idEmpleado);
        if ($sqlHorario->execute()) {
            $_SESSION['success'] = 'El docente y sus horarios se han guardado correctamente.';
        } else {
            $_SESSION['error'] = 'Error al guardar los horarios: ' . $con->error;
        }
    } else {
        $_SESSION['error'] = 'Error al guardar la información del docente: ' . $con->error;
    }

    $sqlDocente->close();
    $sqlHorario->close();
    $con->close();

    header("Location: ../Interfaces/interfazRegistroDocente.php");
    exit;
}
?>
