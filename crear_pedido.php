<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es cliente
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 3) {
    header("Location: login.php");
    exit();
}

// Obtener la lista de productos para el formulario
$sql_productos = "SELECT id, nombre FROM productos";
$result_productos = $conn->query($sql_productos);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    $estado = 'pendiente';
    $descripcion = $_POST['descripcion'];
    $fecha_pedido = $_POST['fecha_pedido']; // Obtener la fecha del formulario
    $hora_pedido = $_POST['hora_pedido']; // Obtener la hora del formulario
    $fecha_hora_pedido = $fecha_pedido . ' ' . $hora_pedido;
    $productos = $_POST['productos']; // Array de productos
    $cantidades = $_POST['cantidades']; // Array de cantidades

    // Iniciar transacción
    $conn->begin_transaction();

    // Insertar el pedido en la tabla pedidos
    $sql_pedido = "INSERT INTO pedidos (usuario_id, fecha_pedido, estado) 
                   VALUES ('$usuario_id', '$fecha_hora_pedido', '$estado')";
    if ($conn->query($sql_pedido) === TRUE) {
        // Obtener el ID del último pedido insertado
        $pedido_id = $conn->insert_id;

        // Iterar sobre los productos y cantidades para insertar en la tabla detalles_pedidos
        for ($i = 0; $i < count($productos); $i++) {
            $producto_id = $productos[$i];
            $cantidad = $cantidades[$i];

            // Insertar detalle del pedido en la tabla detalles_pedidos
            $sql_detalle = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad,descripcion_pedido) 
                            VALUES ('$pedido_id', '$producto_id', '$cantidad','$descripcion')";
            if (!$conn->query($sql_detalle)) {
                // Rollback y mostrar error si falla la inserción del detalle del pedido
                $error = "Error al crear el detalle del pedido: " . $conn->error;
                $conn->rollback();
                break;
            }
        }

        // Commit si todas las inserciones fueron exitosas
        if (!isset($error)) {
            $conn->commit();
            header("Location: pedidos_cliente.php");
            exit();
        }
    } else {
        $error = "Error al crear el pedido: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Pedido</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/icon.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            background-image: url('img/pollo.png'); /* Ruta del Archivo */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 88vh; /* Ajusta la altura según tus necesidades */
        }
        .container {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 30px;
            text-align: center;
        }
        label {
            font-weight: bold;
        }
        input[type="date"],
        input[type="time"],
        input[type="text"],
        input[type="number"],
        textarea,
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
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
            text-align: center;
            transition: color 0.3s ease;
        }
        a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .cantidad,
        .cantidad-label {
            display: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Crear Pedido</h2>
    <form id="pedidoForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="descripcion">Descripción del Pedido:</label>
        <textarea name="descripcion" id="descripcion" required></textarea><br>
        <label for="fecha_pedido">Fecha del Pedido:</label>
        <input type="date" name="fecha_pedido" id="fecha_pedido" required><br>
        <label for="hora_pedido">Hora del Pedido:</label>
        <input type="time" name="hora_pedido" id="hora_pedido" required><br>
        <label for="productos">Productos:</label><br>
        <?php while ($row = $result_productos->fetch_assoc()): ?>
            <input type="checkbox" class="producto" name="productos[]" value="<?php echo $row['id']; ?>"> <?php echo $row['nombre']; ?><br>
            <label class="cantidad-label" for="cantidad_<?php echo $row['id']; ?>">Cantidad:</label>
            <input type="number" class="cantidad" id="cantidad_<?php echo $row['id']; ?>" name="cantidades[]" min="1" value="1"><br>
        <?php endwhile; ?>
        <br>
        <input type="submit" value="Crear Pedido">
    </form>
    <a href="panel.php">Volver a Panel</a>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
</div>

<script>
    document.querySelectorAll('.producto').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const cantidadLabel = this.nextElementSibling.nextElementSibling;
            const cantidadInput = cantidadLabel.nextElementSibling;
            if (this.checked) {
                cantidadLabel.style.display = 'block';
                cantidadInput.style.display = 'block';
            } else {
                cantidadLabel.style.display = 'none';
                cantidadInput.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>



