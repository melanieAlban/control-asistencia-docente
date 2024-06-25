<?php

require ('../fpdf186/fpdf.php');
include ('../servicios/conexion.php');
class PDF extends FPDF
{

   // Cabecera de página
   function Header()
   {
      $conn = new Conexion();
      $conexion = $conn->conectar();
      $diaReporte = $_POST['mesReporte1'];
      list($year, $month) = explode('-', $diaReporte);
      $fechaInicio = "$year-$month-01";
      $fechaFin = date("Y-m-t", strtotime($fechaInicio));

      $this->Image('logoUta.jpg', 210, 20, 30); //logo de la empresa,moverDerecha,moverAbajo,tamañoIMG
      $this->SetFont('Arial', 'B', 19); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
      $this->Cell(95); // Movernos a la derecha
      $this->SetTextColor(0, 0, 0); //color
      //creamos una celda o fila
      $this->Cell(70, 15, utf8_decode('UNIVERSIDAD TÉCNICA DE AMBATO'), 0, 1, 'C', 0); // AnchoCelda,AltoCelda,titulo,borde(1-0),saltoLinea(1-0),posicion(L-C-R),ColorFondo(1-0)
      $this->Ln(3); // Salto de línea
      $this->SetTextColor(103); //color

      $this->Cell(5);  // mover a la derecha
      $this->SetFont('Arial', 'B', 13);
      $this->Cell(59, 10, utf8_decode("Fecha de generación: " . $fechaInicio . " -- " . $fechaFin), 0, 1, '', 0);
      $this->Ln(6);
      /* TITULO DE LA TABLA */
      //color
      $this->SetTextColor(110, 7, 7);
      $this->Cell(90); // mover a la derecha
      $this->SetFont('Arial', 'B', 15);
      $this->Cell(80, 10, utf8_decode("REPORTE DE ASISTENCIA MENSUAL GENERAL"), 0, 1, 'C', 0);
      $this->Ln(7);

      /* CAMPOS DE LA TABLA */
      //color
      $this->SetFillColor(110, 7, 7); //colorFondo
      $this->SetTextColor(255, 255, 255); //colorTexto
      $this->SetDrawColor(163, 163, 163); //colorBorde
      $this->SetFont('Arial', 'B', 9);
      $this->Cell(23); 
      $this->Cell(30, 10, utf8_decode('CEDULA'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('NOMBRE'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('APELLIDO'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('TOTAL GENERADO'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('TOTAL DESCUENTOS'), 1, 1, 'C', 1);
   ;
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


$diaReporte = $_POST['mesReporte1'];
list($year, $month) = explode('-', $diaReporte);
$conn = new Conexion();
$conexion = $conn->conectar();

$fechaInicio = "$year-$month-01";
$fechaFin = date("Y-m-t", strtotime($fechaInicio)); // Último día del mes
$consulta_reporte_asistencia_global = $conexion->query("
SELECT 
    e.cedula, e.nombre, e.apellido,
    SUM(a.total_generado) AS suma_total_generado,
    SUM(a.descuento) AS suma_total_descuento
FROM asistencias a 
    INNER JOIN empleados e ON e.id = a.id_empleado 
WHERE 
    a.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
GROUP BY 
    e.cedula, e.nombre, e.apellido;
   ");

$resultado_descuento = $conexion->query("
SELECT 
(SELECT SUM(a2.descuento) 
 FROM asistencias a2 
 WHERE a2.fecha BETWEEN '$fechaInicio' AND '$fechaFin') AS suma_general_total_descuento
FROM 
asistencias a 
INNER JOIN empleados e ON e.id = a.id_empleado 
WHERE 
a.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
GROUP BY 
e.cedula, e.nombre, e.apellido 
LIMIT 1;");


$resultado_total_generado = $conexion->query("
SELECT 
(SELECT SUM(a2.total_generado) 
 FROM asistencias a2 
 WHERE a2.fecha BETWEEN '$fechaInicio' AND '$fechaFin') AS suma_general_total_generado
FROM 
asistencias a 
INNER JOIN empleados e ON e.id = a.id_empleado 
WHERE 
a.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
GROUP BY 
e.cedula, e.nombre, e.apellido
LIMIT 1;");

$total_descuento = $resultado_descuento->fetch_assoc()["suma_general_total_descuento"];
$total_generado_empleados =$resultado_total_generado->fetch_assoc()["suma_general_total_generado"];
$pdf = new PDF("L", "mm", array(260, 260));
$pdf->AddPage(); /* aqui entran dos para parametros (horientazion,tamaño)V->portrait H->landscape tamaño (A3.A4.A5.letter.legal) */
$pdf->AliasNbPages(); //muestra la pagina / y total de paginas


$pdf->SetFont('Arial', '', 10);
$pdf->SetDrawColor(163, 163, 163); //colorBorde
 
while ($row = $consulta_reporte_asistencia_global->fetch_object()) {
   $pdf->Cell(23);
   $pdf->Cell(30, 10, utf8_decode($row->cedula), 1, 0, 'C', 0);
   $pdf->Cell(40, 10, utf8_decode($row->nombre), 1, 0, 'C', 0);
   $pdf->Cell(40, 10, utf8_decode($row->apellido), 1, 0, 'C',0);
   $pdf->Cell(40, 10, utf8_decode($row->suma_total_generado), 1, 0, 'C', 0);
   $pdf->Cell(40, 10, utf8_decode($row->suma_total_descuento), 1, 1, 'C', 0);
}
$pdf->Cell(23);
$pdf->Cell(30, 10, utf8_decode(""),0, 0, 'C', 0);
$pdf->Cell(40, 10, utf8_decode(""),0, 0, 'C', 0);
$pdf->Cell(40, 10, utf8_decode("TOTAL"),1, 0, 'C', 0);
$pdf->Cell(40, 10, utf8_decode("$".$total_generado_empleados),1, 0, 'C', 0);
$pdf->Cell(40, 10, utf8_decode("$".$total_descuento),1, 1, 'C', 0);
//$pdf->Cell(45, 10,utf8_decode("$".$sueldo_generado_total),1, 1, 'C', 0);
$pdf->Output('Prueba2.pdf', 'I');//nombreDescarga, Visor(I->visualizar - D->descargar)
