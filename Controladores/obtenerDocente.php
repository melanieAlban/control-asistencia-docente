<?php
require_once '../servicios/conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $conn = new Conexion();
    $con = $conn->conectar();

    // Verifica si la conexión se estableció correctamente
    if ($con->connect_error) {
        die("Conexión fallida: " . $con->connect_error);
    }

    $query = "SELECT e.id, e.nombre, e.apellido, e.cedula,
                     MAX(CASE WHEN h.jornada = 'MAT' THEN h.entrada END) as entradaMat,
                     MAX(CASE WHEN h.jornada = 'MAT' THEN h.salida END) as salidaMat,
                     MAX(CASE WHEN h.jornada = 'VES' THEN h.entrada END) as entradaVes,
                     MAX(CASE WHEN h.jornada = 'VES' THEN h.salida END) as salidaVes
              FROM empleados e 
              LEFT JOIN horarios h ON e.id = h.id_empleado
              WHERE e.id = ?
              GROUP BY e.id, e.nombre, e.apellido, e.cedula";
              
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $docente = $result->fetch_assoc();
        echo json_encode($docente);
    } else {
        echo json_encode(['error' => 'No se encontraron datos para el docente especificado.']);
    }

    $stmt->close();
    $con->close();
}
?>
