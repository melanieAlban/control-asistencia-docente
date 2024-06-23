<?php
require_once '../servicios/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['idDocente'];
    $cedula = $_POST['cedulaDocente'];
    $nombre = $_POST['nombreDocente'];
    $apellido = $_POST['apellidoDocente'];
    $horaInicioMatutina = $_POST['horaInicioMatutina'];
    $horaFinMatutina = $_POST['horaFinMatutina'];
    $horaInicioVespertina = $_POST['horaInicioVespertina'];
    $horaFinVespertina = $_POST['horaFinVespertina'];

    if (isset($id)) {
        $conn = new Conexion();
        $con = $conn->conectar();
        
        // Verifica si la conexión se estableció correctamente
        if ($con->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Database connection error: ' . $con->connect_error]);
            exit();
        }
        
        $con->begin_transaction();

        try {
            // Actualizar datos del docente
            $stmt = $con->prepare("UPDATE empleados SET nombre=?, apellido=?, cedula=? WHERE id=?");
            $stmt->bind_param("sssi", $nombre, $apellido, $cedula, $id);
            $stmt->execute();

            // Actualizar horario del docente para jornada matutina
            $stmt = $con->prepare("UPDATE horarios SET entrada=?, salida=? WHERE id_empleado=? AND jornada='MAT'");
            $stmt->bind_param("ssi", $horaInicioMatutina, $horaFinMatutina, $id);
            $stmt->execute();

            // Actualizar horario del docente para jornada vespertina
            $stmt = $con->prepare("UPDATE horarios SET entrada=?, salida=? WHERE id_empleado=? AND jornada='VES'");
            $stmt->bind_param("ssi", $horaInicioVespertina, $horaFinVespertina, $id);
            $stmt->execute();

            $con->commit();

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $con->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }

        $stmt->close();
        $con->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'ID is missing']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
