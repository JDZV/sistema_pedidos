<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es cliente
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 3) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener lista de pedidos entregados del cliente con sus detalles
$sql = "SELECT p.id, p.fecha_pedido, p.estado, dp.descripcion_pedido AS descripcion_pedido, GROUP_CONCAT(CONCAT(pr.nombre, ' (', dp.cantidad, ')') SEPARATOR ', ') AS detalles
        FROM pedidos p 
        LEFT JOIN detalles_pedido dp ON p.id = dp.pedido_id 
        LEFT JOIN productos pr ON dp.producto_id = pr.id
        WHERE p.usuario_id = '$usuario_id' AND p.estado = 'entregado'
        GROUP BY p.id";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedidos Entregados</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/icon.png">
    <style>
        body {
            background-image: url('img/pollo.png'); /* Ruta del Archivo */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 88vh; /* Ajusta la altura según tus necesidades */
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Pedidos Entregados</h2>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Fecha Pedido</th>
            <th>Estado</th>
            <th>Producto y Cantidad</th>
            <th>Descripción</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['fecha_pedido']; ?></td>
                <td><?php echo $row['estado']; ?></td>
                <td><?php echo htmlspecialchars($row['detalles']); ?></td>
                <td><?php echo htmlspecialchars($row['descripcion_pedido']); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <a href="pedidos_cliente.php" class="btn">Volver a Mis Pedidos</a>
</div>
</body>
</html>
