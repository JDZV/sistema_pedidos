<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es administrador o trabajador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol_id'] != 1 && $_SESSION['rol_id'] != 2)) {
    header("Location: login.php");
    exit();
}

// Obtener lista de pedidos con información detallada
$sql = "SELECT p.*, u.nombre_apellidos, GROUP_CONCAT(CONCAT(productos.nombre, ' (', detalles_pedido.cantidad, ')') SEPARATOR ', ') AS detalles, detalles_pedido.descripcion_pedido
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id
        LEFT JOIN detalles_pedido ON p.id = detalles_pedido.pedido_id
        LEFT JOIN productos ON detalles_pedido.producto_id = productos.id
        GROUP BY p.id";
$result = $conn->query($sql);

// Creamos un array para almacenar los pedidos con su información detallada
$pedidos = array();

while ($row = $result->fetch_assoc()) {
    $pedidos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos</title>
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
        .btn-edit {
            background-color: #28a745;
            color: #fff;
            border: none;
        }
        .btn-edit:hover {
            background-color: #218838;
        }
        .btn-delete {
            background-color: #dc3545;
            color: #fff;
            border: none;
        }
        .btn-delete:hover {
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
        .actions {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Gestión de Pedidos</h2>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Fecha Pedido</th>
            <th>Estado</th>
            <th>Producto y Cantidad</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pedidos as $pedido): ?>
            <tr>
                <td><?php echo $pedido['id']; ?></td>
                <td><?php echo htmlspecialchars($pedido['nombre_apellidos']); ?></td>
                <td><?php echo $pedido['fecha_pedido']; ?></td>
                <td><?php echo $pedido['estado']; ?></td>
                <td><?php echo htmlspecialchars($pedido['detalles']); ?></td>
                <td><?php echo htmlspecialchars($pedido['descripcion_pedido']); ?></td>
                <td>
                    <a href="editar_pedido.php?id=<?php echo $pedido['id']; ?>" class="btn btn-edit">Editar Estado</a>
                    <?php if ($_SESSION['rol_id'] == 1): ?> <!-- Mostrar el enlace "Eliminar" solo para administradores -->
                        <a href="eliminar_pedido.php?id=<?php echo $pedido['id']; ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este pedido?')">Eliminar</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="panel.php" class="btn">Volver al Panel</a>
</div>
</body>
</html>

