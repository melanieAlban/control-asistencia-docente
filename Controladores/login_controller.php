<?php
session_start();
require_once  '../servicios/conexion.php';
if (isset($_POST['btniniciarSesion'])) {
    $cedula = $_POST['cedula'];
    $password = $_POST['password'];
    
    $conn = new Conexion();
    $con = $conn->conectar();
    $sqlSelect = "SELECT * FROM empleados WHERE cedula = '$cedula' AND password = '$password'";
    $respuesta = $con->query($sqlSelect);

    if ($respuesta->num_rows > 0) {
        $datos = $respuesta->fetch_object();
        $_SESSION['id'] = $datos->id;
        $_SESSION['nombre'] = $datos->nombre;
        $_SESSION['apellido'] = $datos->apellido;
        $_SESSION['rol'] = $datos->rol;
        if ($datos->rol == 'empleado') {
            header("Location: ../Interfaces/interfazRegistroSalida.php");
        } else if ($datos->rol == 'administrador') {
            header("Location: ../Interfaces/interfazRegistroDocente.php");
        }
    } else {
        $_SESSION['error'] = "Credenciales inválidas";
        header("Location: ../Interfaces/Interfazlogin.php");
    }
}
