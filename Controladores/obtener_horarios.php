<?php
include '../servicios/conexion.php';

$jornadaMatutina = 'No registrada';
$jornadaVespertina = 'No registrada';

if (isset($_SESSION['id'])) {
    $idEmpleado = $_SESSION['id'];
    $conn = new Conexion();
    $con = $conn->conectar();
    $sqlSelect = "SELECT * FROM `horarios` WHERE id_empleado = '$idEmpleado';";
    $respuesta = $con->query($sqlSelect);
    $resultado = array();
    if ($respuesta->num_rows > 0) {
        while($fila = $respuesta->fetch_array()){
            if ($fila['jornada'] == 'MAT') {
                $jornadaMatutina = $fila['entrada'] . ' - ' . $fila['salida'];
            } else if ($fila['jornada'] == 'VES') {
                $jornadaVespertina = $fila['entrada'] . ' - ' . $fila['salida'];
            }
            array_push($resultado, $fila);
        }
    } else {
        $resultado = 'No hay Horarios Registrados para ese empleado';
    }
}

