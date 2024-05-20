<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es cliente
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 3) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener lista de pedidos activos del cliente con sus detalles
$sql = "SELECT p.id, p.fecha_pedido, p.estado, dp.descripcion_pedido AS descripcion_pedido, GROUP_CONCAT(CONCAT(pr.nombre, ' (', dp.cantidad, ')') SEPARATOR ', ') AS detalles
        FROM pedidos p 
        LEFT JOIN detalles_pedido dp ON p.id = dp.pedido_id 
        LEFT JOIN productos pr ON dp.producto_id = pr.id
        WHERE p.usuario_id = '$usuario_id' AND p.estado != 'cancelado'
        GROUP BY p.id";
$result = $conn->query($sql);

// Verificar si se ha enviado una solicitud de cancelación de pedido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancelar_pedido'])) {
    $pedido_id = $_POST['pedido_id'];

    // Actualizar el estado del pedido a "cancelado"
    $sql_cancelar = "UPDATE pedidos SET estado = 'cancelado' WHERE id = '$pedido_id'";
    if ($conn->query($sql_cancelar) === TRUE) {
        // Refrescar la página para actualizar la lista de pedidos
        header("Location: pedidos_cliente.php");
        exit();
    } else {
        $error = "Error al cancelar el pedido: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Pedidos</title>
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
        .btn-cancel {
            background-color: #dc3545;
            color: #fff;
            border: none;
        }
        .btn-cancel:hover {
            background-color: #c82333;
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
        .action-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Mis Pedidos</h2>
    <div class="action-buttons">
        <a href="crear_pedido.php" class="btn">Crear Pedido</a>
        <a href="pedidos_cancelados.php" class="btn">Pedidos Cancelados</a>
    </div>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Fecha Pedido</th>
            <th>Estado</th>
            <th>Producto y Cantidad</th>
            <th>Descripción</th>
            <th>Acciones</th>
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
                <td>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <input type="hidden" name="pedido_id" value="<?php echo $row['id']; ?>">
                        <input type="submit" name="cancelar_pedido" value="Cancelar" class="btn btn-cancel" onclick="return confirm('¿Estás seguro de que deseas cancelar este pedido?');">
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <a href="panel.php" class="btn">Volver al Panel</a>
</div>
</body>
</html>
