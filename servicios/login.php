<?php
include 'conexion.php';
$conn = new Conexion();
$con = $conn->conectar();
$cedula = $_POST['cedula'];
$password = $_POST['password'];

$sqlSelect = "SELECT * FROM empleados WHERE cedula = '$cedula' AND password = '$password'";
$respuesta = $con->query($sqlSelect);
$resultado = array();

if ($respuesta->num_rows > 0) {
    while($fila = $respuesta->fetch_array()) {
        array_push($resultado, $fila);
    }
} else {
    $resultado = array('error' => 'No se encontró al usuario');
}

//echo json_encode($resultado);


if(isset($resultado[0]) && $resultado[0]['cedula'] == $cedula && $resultado[0]['password'] == $password) {
    if($resultado[0]['tipo_rol'] == 'empleado') {
        include '../Interfaces/interfazRegistroSalida.html';
    } else if($resultado[0]['tipo_rol'] == 'administrador') {
        include '../Interfaces/interfazRegistroDocente.html';
    } else {
        include '../Interfaces/login.html';
    }
} else {
    include '../Interfaces/login.html';
}

