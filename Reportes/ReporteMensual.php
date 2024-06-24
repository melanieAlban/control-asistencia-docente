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
      
      $this->Image('logoUta.jpg', 245, 20, 30); //logo de la empresa,moverDerecha,moverAbajo,tamañoIMG
      $this->SetFont('Arial', 'B', 19); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
      $this->Cell(95); // Movernos a la derecha
      $this->SetTextColor(0, 0, 0); //color
      //creamos una celda o fila
      $this->Cell(100, 15, utf8_decode('UNIVERSIDAD TÉCNICA DE AMBATO'), 0, 1, 'C', 0); // AnchoCelda,AltoCelda,titulo,borde(1-0),saltoLinea(1-0),posicion(L-C-R),ColorFondo(1-0)
      $this->Ln(3); // Salto de línea
      $this->SetTextColor(103); //color

      $this->Cell(5);  // mover a la derecha
      $this->SetFont('Arial', 'B', 11);
      $this->Cell(96, 10, utf8_decode("Nombre Y Apellido: ".$dato_info->nombre." ".$dato_info->apellido), 0, 0, '', 0);
      $this->Ln(6);

      $this->Cell(5);  // mover a la derecha
      $this->SetFont('Arial', 'B', 11);
      $this->Cell(59, 10, utf8_decode("Numero de Cédula : ".$dato_info->cedula), 0, 1, '', 0);
      $this->Ln(6);
      /* TITULO DE LA TABLA */
      //color
      $this->SetTextColor(110, 7, 7);
      $this->Cell(95); // mover a la derecha
      $this->SetFont('Arial', 'B', 15);
      $this->Cell(100, 10, utf8_decode("REPORTE DE ASISTENCIA "), 0, 1, 'C', 0);
      $this->Ln(7);

      /* CAMPOS DE LA TABLA */
      //color
      $this->SetFillColor(110, 7, 7); //colorFondo
      $this->SetTextColor(255, 255, 255); //colorTexto
      $this->SetDrawColor(163, 163, 163); //colorBorde
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(30, 10, utf8_decode('FECHA'), 1, 0, 'C', 1);
      $this->Cell(30, 10, utf8_decode('JORNADA'), 1, 0, 'C', 1);
      $this->Cell(45, 10, utf8_decode('HORA DE ENTRADA'), 1, 0, 'C', 1);
      $this->Cell(45, 10, utf8_decode('HORA DE SALIDA'), 1, 0, 'C', 1);
      $this->Cell(45, 10, utf8_decode('HORAS TRABAJADAS'), 1, 0, 'C', 1);
      $this->Cell(45, 10, utf8_decode('SUBTOTAL DESCUENTO'), 1, 0, 'C', 1);
      $this->Cell(35, 10, utf8_decode('SUBTOTAL'), 1, 1, 'C', 1);
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
   $mesReporte = $_POST['mesReporte'];
   list($year, $month) = explode('-', $mesReporte);
   $conn = new Conexion();
   $conexion = $conn->conectar();

   $fechaInicio = "$year-$month-01";
   $fechaFin = date("Y-m-t", strtotime($fechaInicio)); // Último día del mes

   $consulta_reporte_asistencia = $conexion->query("
      SELECT a.fecha, h.entrada, h.salida, h.jornada, da.hora_ingreso, da.hora_salida, da.horas_trabajadas,
            da.subtotal_generado, da.subtotal_descuento, a.total_generado
      FROM empleados e 
      INNER JOIN horarios h ON h.id_empleado = e.id 
      INNER JOIN asistencias a ON a.id_empleado = e.id
      INNER JOIN detalle_asistencias da ON da.id_asistencia = a.id 
      WHERE e.cedula = '$cedula'
      AND a.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
      AND da.jornada = h.jornada
      ORDER BY a.fecha ASC;
   ");

   $resultado = $conexion->query("SELECT COALESCE (SUM(da.subtotal_descuento),0) as total_descuento
   FROM empleados e 
   INNER JOIN horarios h ON h.id_empleado = e.id 
   INNER JOIN asistencias a ON a.id_empleado = e.id
   INNER JOIN detalle_asistencias da ON da.id_asistencia = a.id 
   WHERE e.cedula = '$cedula'
   AND a.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
   AND da.jornada = h.jornada ;");
   $pdf = new PDF();
   $pdf->AddPage("landscape"); /* aqui entran dos para parametros (horientazion,tamaño)V->portrait H->landscape tamaño (A3.A4.A5.letter.legal) */
   $pdf->AliasNbPages(); //muestra la pagina / y total de paginas
   $total_descuento= $resultado->fetch_assoc()['total_descuento'];
   

   $pdf->SetFont('Arial', '', 12);
   $pdf->SetDrawColor(163, 163, 163); //colorBorde

   $total_generado = 0; 

   while ($row = $consulta_reporte_asistencia->fetch_object()) {     
   $fecha= $row->fecha;
   $jornada = $row->jornada;
   $hora_entrada = $row->hora_ingreso;
   $hora_salida = $row->hora_salida;
   $horas_trabajada = $row->horas_trabajadas;
   $descuentos = $row->subtotal_descuento;
   $subtotal = $row->subtotal_generado;
   $total_generado = $row->total_generado;
   $pdf->Cell(30, 10, utf8_decode($fecha), 1, 0, 'C', 0);
   $pdf->Cell(30, 10, utf8_decode($jornada), 1, 0, 'C', 0);
   $pdf->Cell(45, 10, utf8_decode($hora_entrada), 1, 0, 'C', 0);
   $pdf->Cell(45, 10, utf8_decode($hora_salida), 1, 0, 'C', 0);
   $pdf->Cell(45, 10, utf8_decode($horas_trabajada), 1, 0, 'C', 0);
   $pdf->Cell(45, 10, utf8_decode("$".$descuentos), 1, 0, 'C', 0);
   $pdf->Cell(35, 10, utf8_decode("$".$subtotal), 1, 1, 'C', 0);
   }
   $pdf->Cell(30, 10,"" ,0, 0, 'C', 0);
   $pdf->Cell(30, 10,"" ,0, 0, 'C', 0);
   $pdf->Cell(45, 10,"" ,0, 0, 'C', 0);
   $pdf->Cell(45, 10,"" ,0, 0, 'C', 0);
   $pdf->Cell(45, 10,"TOTAL" ,1, 0, 'C', 0);
   $pdf->Cell(45, 10,"$".$total_descuento ,1, 0, 'C', 0);
   $pdf->Cell(35, 10, utf8_decode("$".$total_generado), 1, 1, 'C', 0);

   $pdf->Output('Prueba2.pdf', 'I');//nombreDescarga, Visor(I->visualizar - D->descargar)
