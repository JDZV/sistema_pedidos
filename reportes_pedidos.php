<?php
session_start();
include 'conexion.php';
require 'vendor/autoload.php'; // Asegúrate de que esta ruta sea la correcta

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing as PhpSpreadsheetDrawing;

// Verificar si el usuario es administrador o trabajador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol_id'] != 1 && $_SESSION['rol_id'] != 2)) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha_inicio = $_POST['fecha_inicio'];
    $hora_inicio = $_POST['hora_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $hora_fin = $_POST['hora_fin'];
    $estado = $_POST['estado'];

    // Combina fecha y hora de inicio
    $fecha_hora_inicio = $fecha_inicio . ' ' . $hora_inicio;
    // Combina fecha y hora de fin
    $fecha_hora_fin = $fecha_fin . ' ' . $hora_fin;


    // Consulta SQL para seleccionar los pedidos dentro del rango de fechas y el estado especificados
    $sql = "SELECT p.*, u.nombre_apellidos, 
            GROUP_CONCAT(CONCAT(productos.nombre, ' (', detalles_pedido.cantidad, ')') SEPARATOR ', ') AS detalles, 
            detalles_pedido.descripcion_pedido
            FROM pedidos p 
            JOIN usuarios u ON p.usuario_id = u.id
            LEFT JOIN detalles_pedido ON p.id = detalles_pedido.pedido_id
            LEFT JOIN productos ON detalles_pedido.producto_id = productos.id
            WHERE p.date_created BETWEEN '$fecha_hora_inicio' AND '$fecha_hora_fin' 
            AND p.estado = '$estado'
            GROUP BY p.id";
    $result = $conn->query($sql);

// Crear un nuevo objeto Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

// Agregar imagen al encabezado (parte superior izquierda)
    $drawing = new PhpSpreadsheetDrawing();
    $drawing->setName('Logo');
    $drawing->setDescription('Logo');
    $drawing->setPath('img/logoex.jpeg'); // Reemplazar con la ruta correcta a la imagen
    $drawing->setCoordinates('A1');
    $drawing->setOffsetX(3);
    $drawing->setOffsetY(5);
    $drawing->setHeight(100);
    $drawing->setWidth(100);
    $drawing->setWorksheet($sheet);
    // Establecer borde para las celdas de A1 a A5
    $styleArray = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'], // Color del borde
            ],
        ],
    ];

// Aplicar el estilo de borde a las celdas de A1 a A5
    $sheet->getStyle('A1:A5')->applyFromArray($styleArray);
    // Combinar celdas para el título del reporte
    $sheet->mergeCells('B1:F1'); // Combinar celdas desde B1 hasta E1 para el título
    $sheet->mergeCells('B2:F2'); // Combinar celdas desde B2 hasta E2 para el título
    $sheet->mergeCells('B3:F3'); // Combinar celdas desde B2 hasta E2 para el título
    $sheet->mergeCells('B4:F4'); // Combinar celdas desde B2 hasta E2 para el título
    $sheet->mergeCells('B5:F5'); // Combinar celdas desde B2 hasta E2 para el título

// Personalizar el título del reporte, el tipo de pedido y el estado (parte superior derecha)
    $sheet->setCellValue('B1', 'Reporte de Pedidos');
    $sheet->setCellValue('B2', 'Estado: ' . $estado);
// Centrar el título del reporte, el tipo de pedido y el estado
    $sheet->getStyle('B1:B2')->getAlignment()->setHorizontal('center');
// Establecer el título del reporte y el estado en negrita
    $sheet->getStyle('B1')->getFont()->setBold(true); // Título del reporte
    $sheet->getStyle('B2')->getFont()->setBold(true); // Estado
    // Establecer borde para las celdas de B1 a F5
    $styleArray = [
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'], // Color del borde
            ],
        ],
    ];

// Aplicar el estilo de borde a las celdas de A1 a A5
    $sheet->getStyle('B1:F5')->applyFromArray($styleArray);
// Encabezados de las columnas
    $sheet->setCellValue('A6', 'ID');
    $sheet->setCellValue('B6', 'Cliente');
    $sheet->setCellValue('C6', 'Fecha Pedido');
    $sheet->setCellValue('D6', 'Estado');
    $sheet->setCellValue('E6', 'Producto y Cantidad');
    $sheet->setCellValue('F6', 'Descripción');
    // Establecer el título del reporte y el estado en negrita
    $sheet->getStyle('A6')->getFont()->setBold(true); // ENCABEZADOS
    $sheet->getStyle('B6')->getFont()->setBold(true); // ENCABEZADOS
    $sheet->getStyle('C6')->getFont()->setBold(true); // ENCABEZADOS
    $sheet->getStyle('D6')->getFont()->setBold(true); // ENCABEZADOS
    $sheet->getStyle('E6')->getFont()->setBold(true); // ENCABEZADOS
    $sheet->getStyle('F6')->getFont()->setBold(true); // ENCABEZADOS

// Definir el ancho de las columnas
    $sheet->getColumnDimension('A')->setWidth(15); // Ancho de la columna A
    $sheet->getColumnDimension('B')->setWidth(30); // Ancho de la columna B
    $sheet->getColumnDimension('C')->setWidth(30); // Ancho de la columna C
    $sheet->getColumnDimension('D')->setWidth(30); // Ancho de la columna D
    $sheet->getColumnDimension('E')->setWidth(30); // Ancho de la columna E
    $sheet->getColumnDimension('F')->setWidth(30); // Ancho de la columna F

// Obtener los datos de los pedidos y llenar la tabla
    $row = 7; // Empezar desde la quinta fila
    while ($pedido = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $pedido['id']);
        $sheet->setCellValue('B' . $row, $pedido['nombre_apellidos']);
        $sheet->setCellValue('C' . $row, $pedido['fecha_pedido']);
        $sheet->setCellValue('D' . $row, $pedido['estado']);
        $sheet->setCellValue('E' . $row, $pedido['detalles']);
        $sheet->setCellValue('F' . $row, $pedido['descripcion_pedido']);
        $row++;
    }

// Definir el rango de celdas para aplicar bordes
    $range = 'A6:F' . ($row - 1);

// Establecer bordes para la tabla
    $styleArray = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
    ];
    $sheet->getStyle($range)->applyFromArray($styleArray);


    // Llenar el resto de las filas con los datos de los pedidos
    $row = 8; // Empezar desde la quinta fila
    while ($pedido = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $pedido['id']);
        $sheet->setCellValue('B' . $row, $pedido['nombre_apellidos']);
        $sheet->setCellValue('C' . $row, $pedido['fecha_pedido']);
        $sheet->setCellValue('D' . $row, $pedido['estado']);
        $sheet->setCellValue('E' . $row, $pedido['detalles']);
        $sheet->setCellValue('F' . $row, $pedido['descripcion_pedido']);
        $row++;
    }

// Formatear la fecha de inicio y fin para incluirlas en el nombre del archivo
    $fecha_inicio_formateada = date('Y-m-d', strtotime($fecha_inicio));
    $fecha_fin_formateada = date('Y-m-d', strtotime($fecha_fin));

// Formatear la hora de inicio y fin para incluirlas en el nombre del archivo
    $hora_inicio_formateada = date('H-i-s', strtotime($hora_inicio));
    $hora_fin_formateada = date('H-i-s', strtotime($hora_fin));

// Eliminar espacios y caracteres no permitidos en el nombre del estado
    $estado_cleaned = preg_replace("/[^A-Za-z0-9 ]/", '', $estado);

// Nombre del archivo Excel
    $filename = 'reporte_pedidos_' .  str_replace(' ', '_', $estado_cleaned) . '_' . $fecha_inicio_formateada . '_' . $hora_inicio_formateada . '_al_' . $fecha_fin_formateada . '_' . $hora_fin_formateada . '.xlsx';


    // Guardar el archivo Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save($filename);

    // Configurar encabezado HTTP para descargar el archivo XLSX
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    readfile($filename); // Leer y enviar el archivo al cliente
    // Eliminar el archivo del servidor después de la descarga
    unlink($filename);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Reporte de Pedidos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/icon.png">
    <style>
        body {
            background-image: url('img/pollo.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 88vh;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        label {
            font-weight: bold;
        }
        input[type="date"],
        input[type="time"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Generar Reporte de Pedidos</h2>
    <form method="post">
        <label for="fecha_inicio">Fecha Inicio:</label>
        <input type="date" name="fecha_inicio" id="fecha_inicio" required><br>
        <label for="hora_inicio">Hora Inicio:</label>
        <input type="time" name="hora_inicio" id="hora_inicio" required><br>
        <label for="fecha_fin">Fecha Fin:</label>
        <input type="date" name="fecha_fin" id="fecha_fin" required><br>
        <label for="hora_fin">Hora Fin:</label>
        <input type="time" name="hora_fin" id="hora_fin" required><br>
        <label for="estado">Estado:</label>
        <select name="estado" id="estado" required>
            <option value="pendiente">Pendiente</option>
            <option value="aprobado">Aprobado</option>
            <option value="en camino">En camino</option>
            <option value="entregado">Entregado</option>
        </select><br>
        <input type="submit" value="Generar Reporte">
    </form>
    <a href="reportes.php">Volver a Gestion de Reportes</a>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
</div>
</body>
</html>

