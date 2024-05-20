<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre_apellidos = $_POST['nombre_apellidos'];
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $dni = $_POST['dni'];
    $rol_id = $_POST['rol_id'];

    // Comprobar si el campo de contraseña está vacío
    if (empty($password)) {
        // No actualizar la contraseña
        $sql = "UPDATE usuarios SET 
                    nombre_apellidos='$nombre_apellidos', 
                    usuario='$usuario',
                    email='$email', 
                    direccion='$direccion', 
                    telefono='$telefono', 
                    dni='$dni', 
                    rol_id='$rol_id' 
                WHERE id=$id";
    } else {
        // Hashear la nueva contraseña
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET 
                    nombre_apellidos='$nombre_apellidos', 
                    usuario='$usuario',
                    password='$password_hashed', 
                    email='$email', 
                    direccion='$direccion', 
                    telefono='$telefono', 
                    dni='$dni', 
                    rol_id='$rol_id' 
                WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: usuarios.php");
        exit();
    } else {
        $error = "Error al actualizar el usuario: " . $conn->error;
    }
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM usuarios WHERE id=$id";
    $result = $conn->query($sql);
    if ($result->num_rows != 1) {
        header("Location: usuarios.php");
        exit();
    }
    $usuario = $result->fetch_assoc();
}

// Obtener roles
$sql_roles = "SELECT * FROM roles";
$result_roles = $conn->query($sql_roles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Editar Usuario</title>
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
        input[type="email"],
        input[type="password"],
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
    </style>
</head>
<body>
<div class="container">
    <h2>Editar Usuario</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
        <label for="nombre_apellidos">Nombre y Apellidos:</label>
        <input type="text" name="nombre_apellidos" id="nombre_apellidos" value="<?php echo htmlspecialchars($usuario['nombre_apellidos']); ?>" required><br>
        <label for="usuario">Usuario:</label>
        <input type="text" name="usuario" id="usuario" value="<?php echo htmlspecialchars($usuario['usuario']); ?>" required><br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" placeholder="Dejar vacío para mantener la contraseña actual"><br>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required><br>
        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" id="direccion" value="<?php echo htmlspecialchars($usuario['direccion']); ?>" required><br>
        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" id="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required><br>
        <label for="dni">DNI:</label>
        <input type="text" name="dni" id="dni" value="<?php echo htmlspecialchars($usuario['dni']); ?>" required><br>
        <label for="rol_id">Rol:</label>
        <select name="rol_id" id="rol_id" required>
            <?php while ($row = $result_roles->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>" <?php if ($row['id'] == $usuario['rol_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($row['nombre']); ?>
                </option>
            <?php endwhile; ?>
        </select><br>
        <input type="submit" value="Actualizar Usuario">
    </form>
    <a href="usuarios.php">Volver a Usuarios</a>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
</div>
</body>
</html>

