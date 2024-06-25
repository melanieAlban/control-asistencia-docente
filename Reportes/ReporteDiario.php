<?php

require('../fpdf186/fpdf.php');
include ('../servicios/conexion.php');
class PDF extends FPDF
{

   // Cabecera de página
   function Header()
   {
      //include '../../recursos/Recurso_conexion_bd.php';//llamamos a la conexion BD
      $conn = new Conexion();
      $conexion=$conn->conectar();
      $cedula = $_POST['cedula'];
      $consulta_info = $conexion->query("select e.cedula,e.nombre,e.apellido, e.cedula From empleados e where e.cedula = '$cedula';");
      $dato_info = $consulta_info->fetch_object();
      
      $this->Image('logoUta.jpg', 320, 20, 40); //logo de la empresa,moverDerecha,moverAbajo,tamañoIMG
      $this->SetFont('Arial', 'B', 19); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
      $this->Cell(95); // Movernos a la derecha
      $this->SetTextColor(0, 0, 0); //color
      //creamos una celda o fila
      $this->Cell(180, 15, utf8_decode('UNIVERSIDAD TÉCNICA DE AMBATO'), 0, 1, 'C', 0); // AnchoCelda,AltoCelda,titulo,borde(1-0),saltoLinea(1-0),posicion(L-C-R),ColorFondo(1-0)
      $this->Ln(3); // Salto de línea
      $this->SetTextColor(103); //color

      $this->Cell(5);  // mover a la derecha
      $this->SetFont('Arial', 'B', 13);
      $this->Cell(96, 10, utf8_decode("Nombre Y Apellido: ".$dato_info->nombre." ".$dato_info->apellido), 0, 0, '', 0);
      $this->Ln(6);

      $this->Cell(5);  // mover a la derecha
      $this->SetFont('Arial', 'B', 13);
      $this->Cell(59, 10, utf8_decode("Numero de Cédula : ".$dato_info->cedula), 0, 1, '', 0);
      $this->Ln(6);
      /* TITULO DE LA TABLA */
      //color
      $this->SetTextColor(110, 7, 7);
      $this->Cell(95); // mover a la derecha
      $this->SetFont('Arial', 'B', 13);
      $this->Cell(180, 10, utf8_decode("REPORTE DE ASISTENCIA DIARIO "), 0, 1, 'C', 0);
      $this->Ln(7);

      /* CAMPOS DE LA TABLA */
      //color
      $this->SetFillColor(110, 7, 7); //colorFondo
      $this->SetTextColor(255, 255, 255); //colorTexto
      $this->SetDrawColor(163, 163, 163); //colorBorde
      $this->SetFont('Arial', 'B', 9);
      $this->Cell(28, 10, utf8_decode('FECHA'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('ENTRADA MATUTINA'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('SALIDA MATUTINA'), 1, 0, 'C', 1);
      $this->Cell(45, 10, utf8_decode('ENTRADA VESPERTINA'), 1, 0, 'C', 1);
      $this->Cell(45, 10, utf8_decode('SALIDA VESPERTINA'), 1, 0, 'C', 1);
      $this->Cell(45, 10, utf8_decode('HORAS TRABAJADAS'), 1, 0, 'C', 1);
      $this->Cell(35, 10, utf8_decode('SUBTOTAL'), 1, 0, 'C', 1);
      $this->Cell(35, 10, utf8_decode('DESCUENTO'), 1, 0, 'C', 1);
      $this->Cell(45, 10, utf8_decode('SUELDO GENERADO'), 1, 1, 'C', 1);
   }

   // Pie de página
   function Footer()
   {
      $this->SetY(-15); // Posición: a 1,5 cm del final
      $this->SetFont('Arial', 'I', 8); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
      $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C'); //pie de pagina(numero de pagina)

      $this->SetY(-15); // Posición: a 1,5 cm del final
      $this->SetFont('Arial', 'I', 8); //tipo fuente, cursiva, tamañoTexto
      $hoy = date('d/m/Y');
      $this->Cell(540, 10, utf8_decode($hoy), 0, 0, 'C'); // pie de pagina(fecha de pagina)
   }
}


   $cedula = $_POST['cedula'];
   $diaReporte = $_POST['diaReporte'];
   list($year, $month, $day) = explode('-', $diaReporte);
   $fecha = "$year-$month-$day";
   
   $conn = new Conexion();
   $conexion = $conn->conectar();
   $consulta_reporte_asistencia = $conexion->query("
   SELECT a.fecha, h.entrada, h.salida, h.jornada, da.hora_ingreso, da.hora_salida, da.horas_trabajadas, a.descuento, a.total_generado 
        FROM empleados e 
        INNER JOIN horarios h ON h.id_empleado = e.id 
        INNER JOIN asistencias a ON a.id_empleado = e.id
        INNER JOIN detalle_asistencias da ON da.id_asistencia = a.id 
        WHERE e.cedula = '$cedula'
        AND a.fecha ='$fecha'
        AND da.jornada = h.jornada 
        ORDER BY a.fecha ASC;
   ");

   $resultado_descuento = $conexion->query("
   SELECT COALESCE (a.descuento,0) as total_descuento 
   FROM empleados e 
   INNER JOIN horarios h ON h.id_empleado = e.id 
   INNER JOIN asistencias a ON a.id_empleado = e.id
   INNER JOIN detalle_asistencias da ON da.id_asistencia = a.id 
   WHERE e.cedula = '$cedula'
   AND a.fecha = '$fecha'
   AND da.jornada = h.jornada ;");

   $resultado_total_generado = $conexion->query("
   SELECT COALESCE (a.total_generado,0) as total_generado
   FROM empleados e 
   INNER JOIN horarios h ON h.id_empleado = e.id 
   INNER JOIN asistencias a ON a.id_empleado = e.id
   INNER JOIN detalle_asistencias da ON da.id_asistencia = a.id 
   WHERE e.cedula = '$cedula'
   AND a.fecha = '$fecha' 
   AND da.jornada = h.jornada ;");

   $resultado_horas_totales = $conexion->query("  
   SELECT  SEC_TO_TIME(SUM(TIME_TO_SEC(da.horas_trabajadas))) AS total_horas_trabajadas
   FROM empleados e 
           INNER JOIN horarios h ON h.id_empleado = e.id 
           INNER JOIN asistencias a ON a.id_empleado = e.id
           INNER JOIN detalle_asistencias da ON da.id_asistencia = a.id 
           WHERE e.cedula = '$cedula'
           AND a.fecha ='$fecha'
           AND da.jornada = h.jornada;");

   $pdf = new PDF("L","mm",array(380,350));
   $pdf->AddPage(); /* aqui entran dos para parametros (horientazion,tamaño)V->portrait H->landscape tamaño (A3.A4.A5.letter.legal) */
   $pdf->AliasNbPages(); //muestra la pagina / y total de paginas
   $total_horas_trabajadas = $resultado_horas_totales->fetch_assoc()['total_horas_trabajadas'];

   $pdf->SetFont('Arial', '', 12);
   $pdf->SetDrawColor(163, 163, 163); //colorBorde


   $contador = 0;
   $currentDate = "";
   $entrada_mat = "";
   $salida_mat = "";
   $entrada_ves = "";
   $salida_ves = "";
   $subtotal_descuento = "";
   $subtotal = "";
   $horas_trabajadas = "";
   $sueldo_generado = "";
$sueldo_generado_total = 0;
while ($row = $consulta_reporte_asistencia->fetch_object()) {
    if ($currentDate != $row->fecha) {
        // Imprimir fila anterior si existe
        if ($currentDate != "") {
         $pdf->Cell(28, 10, utf8_decode($currentDate), 1, 0, 'C', 0);
         $pdf->Cell(40, 10, utf8_decode($entrada_mat), 1, 0, 'C', 0);
         $pdf->Cell(40, 10, utf8_decode($salida_mat), 1, 0, 'C', 0);
         $pdf->Cell(45, 10, utf8_decode($entrada_ves), 1, 0, 'C', 0);
         $pdf->Cell(45, 10, utf8_decode($salida_ves), 1, 0, 'C', 0);
         $pdf->Cell(45, 10, utf8_decode($horas_trabajadas), 1, 0, 'C', 0);
         $pdf->Cell(35, 10, utf8_decode("$".$subtotal), 1, 0, 'C', 0);
         $pdf->Cell(35, 10, utf8_decode("$".$subtotal_descuento), 1, 0, 'C', 0);
         $pdf->Cell(45, 10, utf8_decode("$".$sueldo_generado), 1, 1, 'C', 0);
        }
        // Resetear valores
        $currentDate = $row->fecha;
        $entrada_mat = "";
        $salida_mat = "";
        $entrada_ves = "";
        $salida_ves = "";
        $subtotal_descuento = "";
        $subtotal = "";
    }

    // Actualizar valores según jornada
    if ($row->jornada == 'MAT') {
        $entrada_mat = $row->hora_ingreso;
        $salida_mat = $row->hora_salida;
    } else if ($row->jornada == 'VES') {
        $entrada_ves = $row->hora_ingreso;
        $salida_ves = $row->hora_salida;
    }
    $sueldo_generado =  $row->total_generado-$row->descuento ;
    if ($sueldo_generado < 0) {
      $sueldo_generado = 0;
    }
    // Actualizar subtotal y descuentos
    $subtotal_descuento = $row->descuento;
    $subtotal = $row->total_generado;
    $sueldo_generado_total+= $sueldo_generado;
}

// Imprimir la última fila

$pdf->Cell(28, 10, utf8_decode($currentDate), 1, 0, 'C', 0);
$pdf->Cell(40, 10, utf8_decode($entrada_mat), 1, 0, 'C', 0);
$pdf->Cell(40, 10, utf8_decode($salida_mat), 1, 0, 'C', 0);
$pdf->Cell(45, 10, utf8_decode($entrada_ves), 1, 0, 'C', 0);
$pdf->Cell(45, 10, utf8_decode($salida_ves), 1, 0, 'C', 0);
$pdf->Cell(45, 10, utf8_decode($total_horas_trabajadas), 1, 0, 'C', 0);
$pdf->Cell(35, 10, utf8_decode("$".$subtotal), 1, 0, 'C', 0);
$pdf->Cell(35, 10, utf8_decode("$".$subtotal_descuento), 1, 0, 'C', 0);
$pdf->Cell(45, 10, utf8_decode("$".$sueldo_generado), 1, 1, 'C', 0);


$pdf->Cell(28, 10,"", 0, 0, 'C', 0);
$pdf->Cell(40, 10,"" ,0, 0, 'C', 0);
$pdf->Cell(40, 10,"" ,0, 0, 'C', 0);
$pdf->Cell(45, 10,"" ,0, 0, 'C', 0);
$pdf->Cell(45, 10,"",0, 0, 'C', 0);
$pdf->Cell(45, 10,"",0, 0, 'C', 0);
$pdf->Cell(35, 10,"",0, 0, 'C', 0);
$pdf->Cell(35, 10,"TOTAL",1, 0, 'C', 0);
$pdf->Cell(45, 10,utf8_decode("$".$sueldo_generado_total),1, 1, 'C', 0);
   $pdf->Output('Prueba2.pdf', 'I');//nombreDescarga, Visor(I->visualizar - D->descargar)