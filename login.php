<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: panel.php");
    exit();
}
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Obtener la contraseña hasheada de la base de datos
    $sql = "SELECT u.*, r.nombre AS rol_nombre 
            FROM usuarios u 
            JOIN roles r ON u.rol_id = r.id 
            WHERE u.usuario = '$usuario'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Verificar si la contraseña ingresada coincide con la contraseña hasheada
        if (password_verify($contrasena, $hashed_password)) {
            // Inicio de sesión exitoso
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['nombre_apellidos'] = $row['nombre_apellidos'];
            $_SESSION['rol_id'] = $row['rol_id'];
            $_SESSION['rol_nombre'] = $row['rol_nombre'];
            header("Location: panel.php");
            exit();
        } else {
            // Error en las credenciales
            $error = "Usuario o contraseña incorrectos";
        }
    } else {
        // Error en las credenciales
        $error = "Usuario o contraseña incorrectos";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Iniciar sesión</title>
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
        }
        .login-form {
            max-width: 360px;
            margin: 0 auto;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.8); /* Agrega un fondo semi-transparente */
            border: 1px solid #dee2e6;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="login-form">
                <h2 class="text-center mb-4">Iniciar sesión</h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario:</label>
                        <input type="text" name="usuario" id="usuario" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña:</label>
                        <input type="password" name="contrasena" id="contrasena" class="form-control" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                    </div>
                </form>
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger mt-3" role="alert"><?php echo $error; ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

