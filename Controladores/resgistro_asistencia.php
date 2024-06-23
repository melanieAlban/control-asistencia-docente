<?php
require_once '../servicios/conexion.php';

class RegistroAsistencia
{
    private $db;
    private $idEmpleado;
    private $fecha;
    private $hora;
    private $horaSalida;
    private $horaEntrada;
    private $jornada;
    private $idAsistencia;
    private $costoAtraso;

    public function __construct()
    {
        $this->db = (new Conexion())->conectar();
    }

    public function registrarAsistencia($idEmpleado)
    {
        date_default_timezone_set('America/Guayaquil');
        $this->fecha = date('Y-m-d');
       $this->hora = date('H:i:s');
       //$this->hora = '06:45:00';
       //$this->hora = '11:50:00';
       //$this->hora = '14:20:00';
       //$this->hora = '17:10:00';
        $this->idEmpleado = $idEmpleado;

        // Obtener horario del empleado
        $horario = $this->obtenerHorario();
        if (!$horario) {
            return "No tiene horario asignado";
        }

        // Verificar jornada y registrar asistencia
        $this->jornada = $horario->jornada;
        $this->horaEntrada = $horario->entrada;
        $this->horaSalida = $horario->salida;

        $sqlSelectAsistencia = "SELECT * FROM asistencias WHERE fecha = '$this->fecha' AND id_empleado = '$this->idEmpleado'";
        $resultAsistencia = $this->db->query($sqlSelectAsistencia);

        if ($resultAsistencia->num_rows == 0) {
            $sqlInsertAsistencia = "INSERT INTO asistencias (fecha, id_empleado, total_generado, descuento) VALUES ('$this->fecha', '$this->idEmpleado', 0, 0)";
            $this->db->query($sqlInsertAsistencia);
            $this->idAsistencia = $this->db->insert_id;
        } else {
            $asistencia = $resultAsistencia->fetch_object();
            $this->idAsistencia = $asistencia->id;
        }

        //Detalle Asistencia
        $sqlSelectDetalleAsistencia = "SELECT * FROM detalle_asistencias WHERE id_asistencia = '$this->idAsistencia' AND jornada = '$this->jornada'";
        $resultDetalleAsistencia = $this->db->query($sqlSelectDetalleAsistencia);

        if ($resultDetalleAsistencia->num_rows == 0) {
            if ($this->hora > $this -> horaSalida) {
                return "No cumple con su hora de ingreso";
            }
            $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias (id_asistencia, hora_ingreso, jornada, horas_trabajadas, subtotal_generado) VALUES ('$this->idAsistencia', '$this->hora', '$this->jornada', 0, 0)";
            $this->db->query($sqlInsertDetalleAsistencia);
            $this->aplicarDescuentoAtraso();
            return "Registro de ingreso exitoso";
        } else {
            if ($this->hora < $this -> horaEntrada) {
                return "No cumple con su hora de salida";
            }
            $detalleAsistencia = $resultDetalleAsistencia->fetch_object();
            if ($detalleAsistencia->hora_salida == null) {
                $this->actualizarDetalleAsistencia($detalleAsistencia);
                return "Registro de salida exitoso";
            } else {
                return "Ya registro su salida";
            }
        }
    }

    private function obtenerHorario()
    {
        $sql = "SELECT * FROM horarios WHERE id_empleado = '{$this->idEmpleado}'";
        $result = $this->db->query($sql);

        if ($result->num_rows > 0) {
            while ($horario = $result->fetch_object()) {
                $horaEntrada = date("H:i:s", strtotime("-15 minutes", strtotime($horario->entrada)));
                $horaSalida = date("H:i:s", strtotime("+10 minutes", strtotime($horario->salida)));
                if ($this->hora >= $horaEntrada && $this->hora <= $horaSalida) {
                    return $horario;
                }
            }
        }
        return null;
    }


    private function actualizarDetalleAsistencia($detalle)
    {
        $horasTotalesObligatorias = (strtotime($this->horaSalida) - strtotime($this->horaEntrada)) / 3600;
        $horaIngreso = strtotime($detalle->hora_ingreso);
        if ($detalle->hora_ingreso < $this->horaEntrada) {
            $horaIngreso=strtotime($this->horaEntrada);
        }
        $horaSalida = strtotime($this->hora);
        if ($this->hora > $this->horaSalida) {
            $horaSalida=strtotime($this->horaSalida);
        }
        $tiempoTrabajado = $horaSalida - $horaIngreso;
        $horasTrabajadas = $tiempoTrabajado / 3600;
        $horasDate = gmdate('H:i:s', $tiempoTrabajado);
        if ($horasTrabajadas > $horasTotalesObligatorias) {
            $horasTrabajadas = $horasTotalesObligatorias;
            $horasDate = gmdate('H:i:s', $horasTotalesObligatorias * 3600);
        }

        $subtotalGenerado = $horasTrabajadas * 8;

        $sql = "UPDATE detalle_asistencias SET hora_salida = '{$this->hora}', horas_trabajadas = '{$horasDate}', subtotal_generado = '{$subtotalGenerado}' WHERE id = '{$detalle->id}'";
        $this->db->query($sql);

        // Actualizar total generado en la asistencia principal
        $sql = "UPDATE asistencias SET total_generado = total_generado + '{$subtotalGenerado}' WHERE id = '{$this->idAsistencia}'";
        $this->db->query($sql);

        return "Registro de salida exitoso";
    }

    private function aplicarDescuentoAtraso()
    {

        if ($this->hora > $this->horaEntrada) {
            $horaIngreso = strtotime($this->hora);
            $horaEntrada = strtotime($this->horaEntrada);
            $tiempoAtraso = ($horaIngreso - $horaEntrada) / 60;
            $this->costoAtraso = $tiempoAtraso * 0.25;
            $sql = "UPDATE asistencias SET descuento = descuento + '{$this->costoAtraso}' WHERE id = '{$this->idAsistencia}'";
            $this->db->query($sql);
        }
    }

    public function obtenerHorasRegistradas($idEmpleado, $fecha) {
        $sql = "SELECT hora_ingreso, hora_salida, jornada FROM detalle_asistencias
                INNER JOIN asistencias ON detalle_asistencias.id_asistencia = asistencias.id
                WHERE asistencias.id_empleado = $idEmpleado AND asistencias.fecha = '$fecha'";
        $result = $this->db->query($sql);
    
        if ($result->num_rows > 0) {
            $horasRegistradas = [];
            while ($row = $result->fetch_assoc()) {
                $horasRegistradas[] = $row;
            }
            return $horasRegistradas;
        } else {
            return null;
        }
    }
    
    
    
}
