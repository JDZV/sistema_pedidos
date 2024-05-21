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
    $filtro_stock = $_POST['filtro_stock'];

    // Construir la consulta SQL basada en el filtro de stock
    $sql = "SELECT * FROM productos";
    if ($filtro_stock == 'con_stock') {
        $sql .= " WHERE stock > 0";
    } elseif ($filtro_stock == 'sin_stock') {
        $sql .= " WHERE stock = 0";
    }
    $result = $conn->query($sql);

    // Crear un nuevo objeto Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Configurar la zona horaria
    date_default_timezone_set('America/Lima');

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
    $sheet->mergeCells('B1:E1'); // Combinar celdas desde B1 hasta E1 para el título
    $sheet->mergeCells('B2:E2'); // Combinar celdas desde B2 hasta E2 para el título
    $sheet->mergeCells('B3:E3'); // Combinar celdas desde B2 hasta E2 para el título
    $sheet->mergeCells('B4:E4'); // Combinar celdas desde B2 hasta E2 para el título
    $sheet->mergeCells('B5:E5'); // Combinar celdas desde B2 hasta E2 para el título

    // Personalizar el título del reporte
    $sheet->setCellValue('B1', 'Reporte de Productos');
    $sheet->setCellValue('B2', date('Y-m-d H:i:s')); // Fecha y hora del reporte
    $sheet->setCellValue('B3', 'Filtro: ' . ucfirst(str_replace('_', ' ', $filtro_stock)));
    $sheet->getStyle('B1')->getFont()->setBold(true); // Título del reporte
    $sheet->getStyle('B2')->getFont()->setBold(true); // Fecha y hora del reporte
    $sheet->getStyle('B3')->getFont()->setBold(true); // Fecha y hora del reporte
    $sheet->getStyle('B1:B3')->getAlignment()->setHorizontal('center'); // Centrar el título del reporte y la fecha
    // Establecer borde para las celdas de B1 a E5
    $styleArray = [
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'], // Color del borde
            ],
        ],
    ];
    $sheet->getStyle('B1:E5')->applyFromArray($styleArray);

    // Encabezados de las columnas
    $sheet->setCellValue('A6', 'ID');
    $sheet->setCellValue('B6', 'Nombre');
    $sheet->setCellValue('C6', 'Descripción');
    $sheet->setCellValue('D6', 'Precio');
    $sheet->setCellValue('E6', 'Stock');
    $sheet->getStyle('A6:E6')->getFont()->setBold(true); // Establecer encabezados en negrita

    // Definir el ancho de las columnas
    $sheet->getColumnDimension('A')->setWidth(15); // Ancho de la columna A
    $sheet->getColumnDimension('B')->setWidth(30); // Ancho de la columna B
    $sheet->getColumnDimension('C')->setWidth(50); // Ancho de la columna C
    $sheet->getColumnDimension('D')->setWidth(15); // Ancho de la columna D
    $sheet->getColumnDimension('E')->setWidth(15); // Ancho de la columna E

    // Obtener los datos de los productos y llenar la tabla
    $row = 7; // Empezar desde la séptima fila
    while ($producto = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $producto['id']);
        $sheet->setCellValue('B' . $row, $producto['nombre']);
        $sheet->setCellValue('C' . $row, $producto['descripcion']);
        $sheet->setCellValue('D' . $row, $producto['precio']);
        $sheet->setCellValue('E' . $row, $producto['stock']);
        $row++;
    }

    // Definir el rango de celdas para aplicar bordes
    $range = 'A6:E' . ($row - 1);

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

    // Nombre del archivo Excel
    $filename = 'reporte_productos_'.str_replace('_', ' ', $filtro_stock).'_' . date('Y-m-d_H-i-s') . '.xlsx';

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
    <title>Generar Reporte de Productos</title>
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
    <h2>Generar Reporte de Productos</h2>
    <form method="post">
        <label for="filtro_stock">Filtrar por stock:</label>
        <select name="filtro_stock" id="filtro_stock" required>
            <option value="todos">Todos</option>
            <option value="con_stock">Con stock</option>
            <option value="sin_stock">Sin stock</option>
        </select><br>
        <input type="submit" value="Generar Reporte">
    </form>
    <a href="reportes.php">Volver al Gestion de Reportes</a>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
</div>
</body>
</html>
