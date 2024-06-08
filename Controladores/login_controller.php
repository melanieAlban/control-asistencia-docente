<?php
session_start();
include '../servicios/conexion.php';
if (isset($_POST['btniniciarSesion'])) {
    $cedula = $_POST['cedula'];
    $password = $_POST['password'];
    
    $conn = new Conexion();
    $con = $conn->conectar();
    $sqlSelect = "SELECT * FROM empleados WHERE cedula = '$cedula' AND password = '$password'";
    $respuesta = $con->query($sqlSelect);

    if ($respuesta->num_rows > 0) {
        $datos = $respuesta->fetch_object();
        $_SESSION['id'] = $datos->id_empleado;
        $_SESSION['nombre'] = $datos->nombre;
        $_SESSION['apellido'] = $datos->apellido;
        if ($datos->tipo_rol == 'empleado') {
            header("Location: ../Interfaces/interfazRegistroSalida.php");
        } else if ($datos->tipo_rol == 'administrador') {
            header("Location: ../Interfaces/interfazRegistroDocente.html");
        }
    } else {
        $_SESSION['error'] = "Credenciales inválidas";
        header("Location: ../Interfaces/Interfazlogin.php");
    }
}
