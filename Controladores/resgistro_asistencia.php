<?php
require_once '../servicios/conexion.php';

class RegistroAsistencia
{
    private $db;
    private $idEmpleado;
    private $fecha;
    private $hora;
    private $idAsistencia;
    private $horarioMatutino;
    private $horarioVespertino;


    public function __construct()
    {
        $this->db = (new Conexion())->conectar();
    }

    public function registrarAsistencia($idEmpleado)
    {
        date_default_timezone_set('America/Guayaquil');
        // $this->fecha = date('Y-m-d');
        $this->fecha = ('2024-06-02');
        // $this->hora = date('H:i:s');
        $this->hora = '18:00:00';
        //$this->hora = '11:50:00';
        //$this->hora = '14:20:00';
        //$this->hora = '17:10:00';
        $this->idEmpleado = $idEmpleado;

        // Obtiene horario matutino y vespertino
        $this->obtenerHorario();

        $sqlSelectAsistencia = "SELECT * FROM asistencias 
                        WHERE fecha = '$this->fecha' 
                        AND id_empleado = '$this->idEmpleado'";
        $resultAsistencia = $this->db->query($sqlSelectAsistencia);
        $asistencia = null;

        if ($resultAsistencia->num_rows == 0) {
            $sqlInsertAsistencia = "INSERT INTO asistencias (fecha, id_empleado, total_generado, descuento) 
                                    VALUES ('$this->fecha', '$this->idEmpleado', 0, 0)";
            $this->db->query($sqlInsertAsistencia);
            $this->idAsistencia = $this->db->insert_id;
            $sqlSelectAsistencia = "SELECT * FROM asistencias 
                        WHERE fecha = '$this->fecha' 
                        AND id_empleado = '$this->idEmpleado'";
            $resultAsistencia = $this->db->query($sqlSelectAsistencia);
            $asistencia = $resultAsistencia->fetch_object();
        } else {
            $asistencia = $resultAsistencia->fetch_object();
            if ($asistencia->estado == 'FINALIZADO') {
                return "Se han registrado todas las jornadas de hoy.";
            }
            $this->idAsistencia = $asistencia->id;
        }

        // // Trae detalles
        // $sqlSelectDetalleAsistencia = "SELECT * FROM detalle_asistencias 
        //                                 WHERE id_asistencia = '$this->idAsistencia'";
        // $resultDetalleAsistencia = $this->db->query($sqlSelectDetalleAsistencia);
        // if ($resultDetalleAsistencia) {
        // }
        // $detalleAsistencia = $resultDetalleAsistencia->fetch_object();

        $detalleMatutino = null;
        $detalleVespertino = null;
        $sqlSelectDetalleAsistencia = "SELECT * FROM detalle_asistencias 
                               WHERE id_asistencia = '$this->idAsistencia'";
        $resultDetalleAsistencia = $this->db->query($sqlSelectDetalleAsistencia);
        // Verificar si la consulta tuvo éxito
        if ($resultDetalleAsistencia) {
            while ($detalleAsistencia = $resultDetalleAsistencia->fetch_object()) {
                if ($detalleAsistencia->jornada == "MAT") {
                    $detalleMatutino = $detalleAsistencia;
                } else {
                    $detalleVespertino = $detalleAsistencia;
                }
            }
        }

        $maximoSalidaMat = date("H:i:s", strtotime("+10 minutes", strtotime($this->horarioMatutino->salida)));
        $minimoEntradaVes = date("H:i:s", strtotime("-15 minutes", strtotime($this->horarioVespertino->entrada)));
        $maximoSalidaVes = date("H:i:s", strtotime("+10 minutes", strtotime($this->horarioVespertino->salida)));

        // Si no encuentra es decir es el primer registro del dia
        if ($resultDetalleAsistencia->num_rows == 0) {
            // Restar 15 minutos para la entrada
            $minimoEntrada = date("H:i:s", strtotime("-15 minutes", strtotime($this->horarioMatutino->entrada)));

            // Si la intenta entrar antes de 15 min de su hora de entrada
            if ($this->hora < $minimoEntrada) {
                return "La hora de ingreso es desde $minimoEntrada";
            }

            // Si ingresa a tiempo
            if ($this->hora >= $minimoEntrada && $this->hora <= $this->horarioMatutino->entrada) {
                $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, hora_ingreso, jornada, horas_trabajadas, subtotal_generado) 
                                VALUES ('$this->idAsistencia', '$this->hora', 'MAT', 0, 0)";
                $this->db->query($sqlInsertDetalleAsistencia);
                return "Registro de ingreso exitoso.";
            }

            // Si ingresa tarde pero antes de la hora de salida
            if ($this->hora >= $minimoEntrada && $this->hora < $this->horarioMatutino->salida) {
                $descuento = $this->calcularDescuento($this->horarioMatutino->entrada, $this->hora);
                $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, hora_ingreso, jornada, horas_trabajadas, subtotal_generado) 
                                VALUES ('$this->idAsistencia', '$this->hora', 'MAT', 0, 0)";
                $this->db->query($sqlInsertDetalleAsistencia);
                $updateAsistencia = "UPDATE asistencias SET descuento = '$descuento' 
                                WHERE id = '{$this->idAsistencia}'";
                $this->db->query($updateAsistencia);
                return "Registro de ingreso exitoso. (Descuento por atraso: $$descuento)";
            }

            // Si llega aqui significa que no registro ni entrada ni salida por ende pierde la jornada

            // Calcula descuento de toda la jornada
            $descuento = $this->calcularDescuento($this->horarioMatutino->entrada, $this->horarioMatutino->salida);

            // Inserta un detalle sin entrada ni salida
            $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, jornada, horas_trabajadas, subtotal_generado) 
                                VALUES ('$this->idAsistencia', 'MAT', 0, 0)";
            $this->db->query($sqlInsertDetalleAsistencia);

            // Cambia el descuento de la asistencia
            $updateAsistencia = "UPDATE asistencias SET descuento = '$descuento' 
                                WHERE id = '{$this->idAsistencia}'";
            $this->db->query($updateAsistencia);

            if ($this->hora >= $this->horarioVespertino->salida) {
                $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, jornada, horas_trabajadas, subtotal_generado) 
                                VALUES ('$this->idAsistencia', 'VES', 0, 0)";
                $this->db->query($sqlInsertDetalleAsistencia);
                $descuentoVes = $descuento + $this->calcularDescuento($this->horarioVespertino->entrada, $this->horarioVespertino->salida);
                $updateAsistencia = "UPDATE asistencias SET descuento = '$descuentoVes', estado = 'FINALIZADO'
                                WHERE id = '{$this->idAsistencia}'";
                $this->db->query($updateAsistencia);
                return "Su jornada ha terminado, su descuento será de $$descuentoVes";
            }

            return "Su jornada ha terminado, su descuento será de $$descuento";
        } else if (
            $resultDetalleAsistencia->num_rows == 1 &&
            !$detalleMatutino->hora_salida &&
            $this->hora <= $maximoSalidaMat &&
            $detalleMatutino->hora_salida
        ) {
            // Registrar salida matutina si si esta dentro de la hora permitida
            if ($this->hora <= $maximoSalidaMat) {
                $generadoJornada = 0;
                $horasTrabajadas = '00:00:00';
                $descuento = $asistencia->descuento;

                // Si completa toda la jornada
                if ($detalleMatutino->hora_ingreso <= $this->horarioMatutino->entrada && $this->hora >= $this->horarioMatutino->salida) {
                    $generadoJornada =
                        $this->calcularGenerado($this->horarioMatutino->entrada, $this->horarioMatutino->salida);
                    $horasTrabajadas = $this->calcularHorasTrabajdas($this->horarioMatutino->entrada, $this->horarioMatutino->salida);
                };

                // Si entro tarde pero sale a la hora correcta
                if ($detalleMatutino->hora_ingreso > $this->horarioMatutino->entrada && $this->hora >= $this->horarioMatutino->salida) {
                    $generadoJornada =
                        $this->calcularGenerado($detalleMatutino->hora_ingreso, $this->horarioMatutino->salida);
                    $horasTrabajadas = $this->calcularHorasTrabajdas($detalleMatutino->hora_ingreso, $this->horarioMatutino->salida);
                };

                // Si entro a la hora correcta pero sale antes
                if ($detalleMatutino->hora_ingreso <= $this->horarioMatutino->entrada && $this->hora < $this->horarioMatutino->salida) {
                    $generadoJornada =
                        $this->calcularGenerado($this->horarioMatutino->entrada, $this->hora);
                    $horasTrabajadas = $this->calcularHorasTrabajdas($this->horarioMatutino->entrada, $this->hora);
                    $descuento += $this->calcularDescuento($this->hora, $this->horarioMatutino->salida);
                }

                // Si entro tarde y sale antes
                if ($detalleMatutino->hora_ingreso > $this->horarioMatutino->entrada && $this->hora < $this->horarioMatutino->salida) {
                    $generadoJornada =
                        $this->calcularGenerado($detalleMatutino->hora_ingreso, $this->hora);
                    $horasTrabajadas = $this->calcularHorasTrabajdas($detalleMatutino->hora_ingreso, $this->hora);
                    $descuento += $this->calcularDescuento($this->hora, $this->horarioMatutino->salida);
                }

                $updateDetalle = "UPDATE detalle_asistencias 
                            SET hora_salida = '$this->hora', subtotal_generado = $generadoJornada, 
                            horas_trabajadas = '$horasTrabajadas' WHERE id = $detalleMatutino->id";
                $this->db->query($updateDetalle);

                $updateAsistencia = "UPDATE asistencias SET total_generado = $generadoJornada 
                                    WHERE id = $this->idAsistencia";
                $this->db->query($updateAsistencia);

                if ($asistencia->descuento != $descuento) {
                    $updateAsistencia = "UPDATE asistencias SET descuento = '$descuento' 
                                        WHERE id = $this->idAsistencia";
                    $this->db->query($updateAsistencia);
                }
                return "Registro de salida exitoso";
            }
        } else if ($resultDetalleAsistencia->num_rows == 1) { // Entrada vespertina
            // Descontar jornada matutina por atraso
            $descuento = $this->calcularDescuento($this->horarioMatutino->entrada, $this->horarioMatutino->salida);
            $updateAsistencia = "UPDATE asistencias SET descuento = '$descuento' 
                                WHERE id = $this->idAsistencia";
            $this->db->query($updateAsistencia);

            // Registrar entrada vespertina
            // Restar 15 minutos para la entrada
            $descuento = $asistencia->descuento;

            // Si la intenta entrar antes de 15 min de su hora de entrada
            if ($this->hora < $minimoEntradaVes) {
                return "La hora de ingreso es desde $minimoEntradaVes";
            }

            // Si ingresa a tiempo
            if ($this->hora >= $minimoEntradaVes && $this->hora <= $this->horarioVespertino->entrada) {
                $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, hora_ingreso, jornada, horas_trabajadas, subtotal_generado) 
                                VALUES ('$this->idAsistencia', '$this->hora', 'VES', 0, 0)";
                $this->db->query($sqlInsertDetalleAsistencia);
                return "Registro de ingreso exitoso.";
            }

            // Si ingresa tarde pero antes de la hora de salida
            if ($this->hora >= $minimoEntradaVes && $this->hora < $this->horarioVespertino->salida) {
                $descuento += $this->calcularDescuento($this->horarioVespertino->entrada, $this->hora);
                $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, hora_ingreso, jornada, horas_trabajadas, subtotal_generado) 
                                VALUES ('$this->idAsistencia', '$this->hora', 'VES', 0, 0)";
                $this->db->query($sqlInsertDetalleAsistencia);
                $updateAsistencia = "UPDATE asistencias SET descuento = '$descuento' 
                                WHERE id = '{$this->idAsistencia}'";
                $this->db->query($updateAsistencia);
                return "Registro de ingreso exitoso. (Descuento por atraso: $$descuento)";
            }

            // Si llega aqui significa que no registro ni entrada ni salida por ende pierde la jornada

            // Calcula descuento de toda la jornada
            $descuento += $this->calcularDescuento($this->horarioVespertino->entrada, $this->horarioVespertino->salida);

            // Inserta un detalle sin entrada ni salida
            $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, jornada, horas_trabajadas, subtotal_generado) 
                                VALUES ('$this->idAsistencia', 'VES', 0, 0)";
            $this->db->query($sqlInsertDetalleAsistencia);

            // Cambia el descuento de la asistencia
            $updateAsistencia = "UPDATE asistencias SET descuento = '$descuento' 
                                WHERE id = '{$this->idAsistencia}'";
            $this->db->query($updateAsistencia);

            return "Su jornada ha terminado, su descuento será de $$descuento";
        } else if ($this->hora <= $maximoSalidaVes) { // 
            $generadoJornada = 0;
            $horasTrabajadas = '00:00:00';
            $descuento = $asistencia->descuento;
            $total_generado = $asistencia->total_generado;

            // Si completa toda la jornada
            if ($detalleVespertino->hora_ingreso <= $this->horarioVespertino->entrada && $this->hora >= $this->horarioVespertino->salida) {
                $generadoJornada =
                    $this->calcularGenerado($this->horarioVespertino->entrada, $this->horarioVespertino->salida);
                $horasTrabajadas = $this->calcularHorasTrabajdas($this->horarioVespertino->entrada, $this->horarioVespertino->salida);
            };

            // Si entro tarde pero sale a la hora correcta
            if ($detalleVespertino->hora_ingreso > $this->horarioVespertino->entrada && $this->hora >= $this->horarioVespertino->salida) {
                $generadoJornada =
                    $this->calcularGenerado($detalleVespertino->hora_ingreso, $this->horarioVespertino->salida);
                $horasTrabajadas = $this->calcularHorasTrabajdas($detalleVespertino->hora_ingreso, $this->horarioVespertino->salida);
            };

            // Si entro a la hora correcta pero sale antes
            if ($detalleVespertino->hora_ingreso <= $this->horarioVespertino->entrada && $this->hora < $this->horarioVespertino->salida) {
                $generadoJornada =
                    $this->calcularGenerado($this->horarioVespertino->entrada, $this->hora);
                $horasTrabajadas = $this->calcularHorasTrabajdas($this->horarioVespertino->entrada, $this->hora);
                $descuento += $this->calcularDescuento($this->hora, $this->horarioVespertino->salida);
            }

            // Si entro tarde y sale antes
            if ($detalleVespertino->hora_ingreso > $this->horarioVespertino->entrada && $this->hora < $this->horarioVespertino->salida) {
                $generadoJornada =
                    $this->calcularGenerado($detalleVespertino->hora_ingreso, $this->hora);
                $horasTrabajadas = $this->calcularHorasTrabajdas($detalleVespertino->hora_ingreso, $this->hora);
                $descuento += $this->calcularDescuento($this->hora, $this->horarioVespertino->salida);
            }

            $updateDetalle = "UPDATE detalle_asistencias 
                            SET hora_salida = '$this->hora', subtotal_generado = $generadoJornada, 
                            horas_trabajadas = '$horasTrabajadas' WHERE id = $detalleVespertino->id";
            $this->db->query($updateDetalle);

            $updateAsistencia = "UPDATE asistencias SET total_generado = $total_generado + $generadoJornada, estado = 'FINALIZADO'
                                    WHERE id = $this->idAsistencia";
            $this->db->query($updateAsistencia);

            if ($asistencia->descuento != $descuento) {
                $updateAsistencia = "UPDATE asistencias SET descuento = '$descuento', estado = 'FINALIZADO' 
                                        WHERE id = $this->idAsistencia";
                $this->db->query($updateAsistencia);
            }
            return "Registro de salida exitoso";
        } else {
            $descuento = $asistencia->descuento;
            $descuento += $this->calcularDescuento($this->horarioVespertino->entrada, $this->horarioVespertino->salida);
            $updateAsistencia = "UPDATE asistencias SET descuento = '$descuento', estado = 'FINALIZADO'
                                WHERE id = $this->idAsistencia";
            $this->db->query($updateAsistencia);
            return "Su jornada ha terminado, su descuento será de $$descuento";
        }
    }

    private function obtenerHorario()
    {
        $sql = "SELECT * FROM horarios WHERE id_empleado = '{$this->idEmpleado}'";
        $result = $this->db->query($sql);

        if ($result->num_rows > 0) {
            while ($horario = $result->fetch_object()) {
                if ($horario->jornada == 'MAT') {
                    $this->horarioMatutino = $horario;
                } else {
                    $this->horarioVespertino = $horario;
                }
            }
        }
        return null;
    }

    private function calcularDescuento($inicio, $fin)
    {
        $horaInicio = new DateTime($inicio);
        $horaFin = new DateTime($fin);
        $tiempo = $horaInicio->diff($horaFin);
        $totalSegundos = ($tiempo->h * 3600) + ($tiempo->i * 60) + $tiempo->s;
        $descuento = (0.25 / 60) * $totalSegundos;
        return $descuento;
    }

    private function calcularGenerado($inicio, $fin)
    {
        $horaInicio = new DateTime($inicio);
        $horaFin = new DateTime($fin);
        $tiempo = $horaInicio->diff($horaFin);
        $totalSegundos = ($tiempo->h * 3600) + ($tiempo->i * 60) + $tiempo->s;
        $generado = ($totalSegundos / 3600) * 8;
        return $generado;
    }

    private function calcularHorasTrabajdas($inicio, $fin)
    {
        $horaInicio = new DateTime($inicio);
        $horaFin = new DateTime($fin);
        $diferencia = $horaInicio->diff($horaFin);

        $horas = $diferencia->h;
        $minutos = $diferencia->i;
        $segundos = $diferencia->s;

        $horas = str_pad($horas, 2, "0", STR_PAD_LEFT);
        $minutos = str_pad($minutos, 2, "0", STR_PAD_LEFT);
        $segundos = str_pad($segundos, 2, "0", STR_PAD_LEFT);

        return "$horas:$minutos:$segundos";
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
