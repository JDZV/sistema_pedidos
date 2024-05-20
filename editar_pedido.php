<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es administrador o trabajador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol_id'] != 1 && $_SESSION['rol_id'] != 2)) {
    header("Location: login.php");
    exit();
}

// Obtener los datos del pedido a editar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $estado = $_POST['estado'];

    $sql = "UPDATE pedidos SET estado='$estado' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header("Location: pedidos.php");
        exit();
    } else {
        $error = "Error al actualizar el pedido: " . $conn->error;
    }
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM pedidos WHERE id=$id";
    $result = $conn->query($sql);
    if ($result->num_rows != 1) {
        header("Location: pedidos.php");
        exit();
    }
    $pedido = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Pedido</title>
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
        input[type="text"],
        input[type="number"],
        textarea {
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
    </style>
</head>
<body>
<div class="container">
    <h2>Editar Pedido</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <input type="hidden" name="id" value="<?php echo $pedido['id']; ?>">
        <label for="estado">Estado:</label>
        <select name="estado" id="estado" required>
            <option value="pendiente" <?php if ($pedido['estado'] == 'pendiente') echo 'selected'; ?>>Pendiente</option>
            <option value="aprobado" <?php if ($pedido['estado'] == 'aprobado') echo 'selected'; ?>>Aprobado</option>
            <option value="en camino" <?php if ($pedido['estado'] == 'en camino') echo 'selected'; ?>>En camino</option>
            <option value="entregado" <?php if ($pedido['estado'] == 'entregado') echo 'selected'; ?>>Entregado</option>
        </select><br><br>
        <input type="submit" value="Actualizar Pedido">
    </form>
    <a href="pedidos.php">Volver a Pedidos</a>
    <?php if(isset($error)) echo "<p>$error</p>"; ?>
</div>
</body>
</html>
