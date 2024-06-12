<?php
class Conexion
{
    public function conectar()
    {
        $servername = "34.174.179.172";
        $username = "root";
        $password = "root";
        $dbname = "asistencia-docente";
        $conn = mysqli_connect($servername, $username, $password, $dbname);

        if (!$conn) {
            //el die mata a todo los procesos y ya no hace nada mas
            echo ("error en la conexion" . mysqli_connect_error());
        } else {
            return $conn;
        }
    }
}
