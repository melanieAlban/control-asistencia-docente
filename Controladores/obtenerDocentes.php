<?php
require_once '../servicios/conexion.php';

function obtenerListaDocentes($cedula = null) {
    $conn = new Conexion();
    $con = $conn->conectar();
    
    // Verifica si la conexión se estableció correctamente
    if ($con->connect_error) {
        die("Conexión fallida: " . $con->connect_error);
    }
    
    $query = "SELECT id, nombre, apellido, cedula FROM empleados WHERE estado= 'ACT'";
    
    if ($cedula) {
        $query .= " AND cedula LIKE ?";
    }
    
    $stmt = $con->prepare($query);
    
    if ($cedula) {
        $likeCedula = "%".$cedula."%";
        $stmt->bind_param("s", $likeCedula);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $docentes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $docentes[] = $row;
        }
    }
    
    $stmt->close();
    $con->close();
    
    return $docentes;
}

if (isset($_GET['cedula'])) {
    $cedulaBusqueda = $_GET['cedula'];
    $docentes = obtenerListaDocentes($cedulaBusqueda);
    echo json_encode($docentes);
}
?>
