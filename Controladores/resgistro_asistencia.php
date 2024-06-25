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
        $this->fecha = ('2024-05-26');
        // $this->hora = date('H:i:s');
        $this->hora = '17:00:00';
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
            $subtotal_descuento = 0;

            // Si la intenta entrar antes de 15 min de su hora de entrada
            if ($this->hora < $minimoEntrada) {
                return "La hora de ingreso es desde $minimoEntrada";
            }

            // Si ingresa a tiempo
            if ($this->hora >= $minimoEntrada && $this->hora <= $this->horarioMatutino->entrada) {
                $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, hora_ingreso, jornada, horas_trabajadas, subtotal_generado, subtotal_descuento) 
                                VALUES ('$this->idAsistencia', '$this->hora', 'MAT', 0, 0, 0)";
                $this->db->query($sqlInsertDetalleAsistencia);
                return "Registro de ingreso exitoso.";
            }

            // Si ingresa tarde pero antes de la hora de salida
            if ($this->hora >= $minimoEntrada && $this->hora < $this->horarioMatutino->salida) {
                $descuento = $this->calcularDescuento($this->horarioMatutino->entrada, $this->hora);
                $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, hora_ingreso, jornada, horas_trabajadas, subtotal_generado, subtotal_descuento) 
                                VALUES ('$this->idAsistencia', '$this->hora', 'MAT', 0, 0, $descuento)";
                $this->db->query($sqlInsertDetalleAsistencia);
                $updateAsistencia = "UPDATE asistencias SET descuento = '$descuento' 
                                WHERE id = '{$this->idAsistencia}'";
                $this->db->query($updateAsistencia);
                return "Registro de ingreso exitoso.";
            }

            // Si llega aqui significa que no registro ni entrada ni salida por ende pierde la jornada

            // Calcula descuento de toda la jornada
            $descuento = $this->calcularDescuento($this->horarioMatutino->entrada, $this->horarioMatutino->salida);

            // Inserta un detalle sin entrada ni salida
            $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, jornada, horas_trabajadas, subtotal_generado, subtotal_descuento) 
                                VALUES ('$this->idAsistencia', 'MAT', '00:00:00', 0, $descuento)";
            $this->db->query($sqlInsertDetalleAsistencia);

            // Cambia el descuento de la asistencia
            $updateAsistencia = "UPDATE asistencias SET descuento = '$descuento' 
                                WHERE id = '{$this->idAsistencia}'";
            $this->db->query($updateAsistencia);

            // Si entra despues de la segunda jornada
            if ($this->hora >= $this->horarioVespertino->salida) {
                $subtotal_descuentoVes = $this->calcularDescuento($this->horarioVespertino->entrada, $this->horarioVespertino->salida);
                $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, jornada, horas_trabajadas, subtotal_generado, subtotal_descuento) 
                                VALUES ('$this->idAsistencia', 'VES', '00:00:00', 0, $subtotal_descuentoVes)";
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
            $this->hora <= $maximoSalidaMat
        ) {
            // Registrar salida matutina si si esta dentro de la hora permitida
            if ($this->hora <= $maximoSalidaMat) {
                $subtotal_descuento = $detalleMatutino->subtotal_descuento;
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
                            horas_trabajadas = '$horasTrabajadas', subtotal_descuento = $descuento WHERE id = $detalleMatutino->id";
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
            } else {
                // Descontar jornada matutina por atraso
                $descuento = $this->calcularDescuento($this->horarioMatutino->entrada, $this->horarioMatutino->salida);
                $updateAsistencia = "UPDATE asistencias SET descuento = '$descuento' 
                                    WHERE id = $this->idAsistencia";
                $this->db->query($updateAsistencia);
                return "Perdió la jornada de la mañana...";
            }
        } else if ($resultDetalleAsistencia->num_rows == 1) { // Entrada vespertina
            // Registrar entrada vespertina
            // Restar 15 minutos para la entrada
            $descuento = $asistencia->descuento;
            $subtotal_descuento = 0;

            // Si la intenta entrar antes de 15 min de su hora de entrada
            if ($this->hora < $minimoEntradaVes) {
                return "La hora de ingreso es desde $minimoEntradaVes";
            }

            // Si ingresa a tiempo
            if ($this->hora >= $minimoEntradaVes && $this->hora <= $this->horarioVespertino->entrada) {
                $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, hora_ingreso, jornada, horas_trabajadas, subtotal_generado, subtotal_descuento) 
                                VALUES ('$this->idAsistencia', '$this->hora', 'VES', 0, 0, 0)";
                $this->db->query($sqlInsertDetalleAsistencia);
                return "Registro de ingreso exitoso.";
            }

            // Si ingresa tarde pero antes de la hora de salida
            if ($this->hora >= $minimoEntradaVes && $this->hora < $this->horarioVespertino->salida) {
                echo json_encode("Hola");
                $subtotal_descuento = $this->calcularDescuento($this->horarioVespertino->entrada, $this->hora);
                $descuento += $this->calcularDescuento($this->horarioVespertino->entrada, $this->hora);
                $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, hora_ingreso, jornada, horas_trabajadas, subtotal_generado, subtotal_descuento) 
                                VALUES ('$this->idAsistencia', '$this->hora', 'VES', 0, 0, $subtotal_descuento)";
                $this->db->query($sqlInsertDetalleAsistencia);
                $updateAsistencia = "UPDATE asistencias SET descuento = '$descuento' 
                                WHERE id = '{$this->idAsistencia}'";
                $this->db->query($updateAsistencia);
                return "Registro de ingreso exitoso.";
            }

            // Si llega aqui significa que no registro ni entrada ni salida por ende pierde la jornada

            // Calcula descuento de toda la jornada
            $descuento += $this->calcularDescuento($this->horarioVespertino->entrada, $this->horarioVespertino->salida);
            $subtotal_descuento = $this->calcularDescuento($this->horarioVespertino->entrada, $this->horarioVespertino->salida);
            // Inserta un detalle sin entrada ni salida
            $sqlInsertDetalleAsistencia = "INSERT INTO detalle_asistencias 
                                (id_asistencia, jornada, horas_trabajadas, subtotal_generado, subtotal_descuento) 
                                VALUES ('$this->idAsistencia', 'VES', '00:00:00', 0, $subtotal_descuento)";
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
            $subtotal_descuento = $detalleVespertino->subtotal_descuento;
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
                $subtotal_descuento = $this->calcularDescuento($this->hora, $this->horarioVespertino->salida);
                $descuento += $this->calcularDescuento($this->hora, $this->horarioVespertino->salida);
            }

            // Si entro tarde y sale antes
            if ($detalleVespertino->hora_ingreso > $this->horarioVespertino->entrada && $this->hora < $this->horarioVespertino->salida) {
                $generadoJornada =
                    $this->calcularGenerado($detalleVespertino->hora_ingreso, $this->hora);
                $horasTrabajadas = $this->calcularHorasTrabajdas($detalleVespertino->hora_ingreso, $this->hora);
                $descuento += $this->calcularDescuento($this->hora, $this->horarioVespertino->salida);
                $subtotal_descuento += $this->calcularDescuento($this->hora, $this->horarioVespertino->salida);
            }

            $updateDetalle = "UPDATE detalle_asistencias 
                            SET hora_salida = '$this->hora', subtotal_generado = $generadoJornada, 
                            horas_trabajadas = '$horasTrabajadas', subtotal_descuento = $subtotal_descuento 
                            WHERE id = $detalleVespertino->id";
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

    public function obtenerHorasRegistradas($idEmpleado, $fecha)
    {
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


    public function reporteMensual($cedula)
    {
        date_default_timezone_set('America/Guayaquil');
        $fechaInicio = date("Y-m-01");
        $fechaFin = date("Y-m-t");
        $sql = "SELECT a.fecha, h.entrada, h.salida, h.jornada, da.hora_ingreso, da.hora_salida, da.horas_trabajadas,
            da.subtotal_generado, a.descuento, a.total_generado
        FROM empleados e 
        INNER JOIN horarios h ON h.id_empleado = e.id 
        INNER JOIN asistencias a ON a.id_empleado = e.id
        INNER JOIN detalle_asistencias da ON da.id_asistencia = a.id 
        WHERE e.cedula = '$cedula'
        AND a.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
        AND da.jornada = h.jornada
        ORDER BY a.fecha ASC";
        echo($sql);
        $result = $this->db->query($sql);
        $reportes = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if (!isset($reportes[$row['fecha']])) {
                    $reportes[$row['fecha']] = [
                        'fecha' => $row['fecha'],
                        'entradaM' => '',
                        'salidaM' => '',
                        'entradaV' => '',
                        'salidaV' => '',
                        'horasTrabajadas' => '00:00:00'
                    ];
                }
                if ($row['jornada'] == 'MAT') {
                    $reportes[$row['fecha']]['entradaM'] = $row['hora_ingreso'];
                    $reportes[$row['fecha']]['salidaM'] = $row['hora_salida'];
                    $reportes[$row['fecha']]['horasTrabajadas'] = $this->sumarHoras($reportes[$row['fecha']]['horasTrabajadas'], $row['horas_trabajadas']);
                } elseif ($row['jornada'] == 'VES') {
                    $reportes[$row['fecha']]['entradaV'] = $row['hora_ingreso'];
                    $reportes[$row['fecha']]['salidaV'] = $row['hora_salida'];
                    $reportes[$row['fecha']]['horasTrabajadas'] = $this->sumarHoras($reportes[$row['fecha']]['horasTrabajadas'], $row['horas_trabajadas']);
                }
            }
        }
        return array_values($reportes);
    }


    public function reporteSemanal($cedula)
    {
        date_default_timezone_set('America/Guayaquil');
        $hoy = new DateTime();
        $hoy->setTime(0, 0, 0); // Asegurarse de que la hora esté en 00:00:00 para evitar problemas

        $diaSemana = (int) $hoy->format('N');

        // Calcular la fecha del lunes de la semana actual
        if ($diaSemana === 7) { // Si es domingo, restar 6 días
            $hoy->modify('-6 days');
        } else { // Si no es domingo, restar el número de días que han pasado desde el lunes
            $hoy->modify('-' . ($diaSemana - 1) . ' days');
        }
        $fechaInicioString = $hoy->format('Y-m-d');

        // Calcular la fecha del domingo de la semana actual
        $fechaFin = clone $hoy;
        $fechaFin->modify('+6 days');
        $fechaFinString = $fechaFin->format('Y-m-d');

        $sql = "SELECT a.fecha, h.entrada, h.salida, h.jornada, da.hora_ingreso, da.hora_salida, da.horas_trabajadas,
            da.subtotal_generado, a.descuento, a.total_generado 
        FROM empleados e 
        INNER JOIN horarios h ON h.id_empleado = e.id 
        INNER JOIN asistencias a ON a.id_empleado = e.id
        INNER JOIN detalle_asistencias da ON da.id_asistencia = a.id 
        WHERE e.cedula = '$cedula'
        AND a.fecha BETWEEN '$fechaInicioString' AND '$fechaFinString'
        AND da.jornada = h.jornada 
        ORDER BY a.fecha ASC";
        $result = $this->db->query($sql);

        $reportes = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if (!isset($reportes[$row['fecha']])) {
                    $reportes[$row['fecha']] = [
                        'fecha' => $row['fecha'],
                        'entradaM' => '',
                        'salidaM' => '',
                        'entradaV' => '',
                        'salidaV' => '',
                        'horasTrabajadas' => '00:00:00'
                    ];
                }
                if ($row['jornada'] == 'MAT') {
                    $reportes[$row['fecha']]['entradaM'] = $row['hora_ingreso'];
                    $reportes[$row['fecha']]['salidaM'] = $row['hora_salida'];
                    $reportes[$row['fecha']]['horasTrabajadas'] = $this->sumarHoras($reportes[$row['fecha']]['horasTrabajadas'], $row['horas_trabajadas']);
                    
                } elseif ($row['jornada'] == 'VES') {
                    $reportes[$row['fecha']]['entradaV'] = $row['hora_ingreso'];
                    $reportes[$row['fecha']]['salidaV'] = $row['hora_salida'];
                    $reportes[$row['fecha']]['horasTrabajadas'] = $this->sumarHoras($reportes[$row['fecha']]['horasTrabajadas'], $row['horas_trabajadas']);
                    
                }
            }
        }
        return array_values($reportes);
    }

    private function sumarHoras($hora1, $hora2) {
        $t1 = new DateTime($hora1);
        $t2 = new DateTime($hora2);
        
        $interval1 = new DateInterval('PT' . $t1->format('H') . 'H' . $t1->format('i') . 'M' . $t1->format('s') . 'S');
        $interval2 = new DateInterval('PT' . $t2->format('H') . 'H' . $t2->format('i') . 'M' . $t2->format('s') . 'S');
        
        $horasTotales = new DateTime('00:00:00');
        $horasTotales->add($interval1);
        $horasTotales->add($interval2);
    
        return $horasTotales->format('H:i:s');
    }
    
    

    
}
