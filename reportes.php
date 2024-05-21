<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es administrador o trabajador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol_id'] != 1 && $_SESSION['rol_id'] != 2)) {
    header("Location: login.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Panel de Control</title>
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
        .panel-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .panel-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .panel-links {
            list-style-type: none;
            padding-left: 0;
        }
        .panel-links a {
            display: block;
            margin-bottom: 10px;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .panel-links a:hover {
            background-color: #0056b3;
        }
        .user-icon {
            position: absolute;
            top: 20px;
            right: 60px;
            cursor: pointer;
            width: 40px; /* Ancho deseado */
            height: auto; /* Altura automática para mantener la proporción */
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
        .logout-collapse {
            position: absolute;
            top: 70px;
            right: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="panel-container">
        <h2 class="panel-title">Gestion de Reportes</h2>
        <ul class="panel-links">
            <li><a href="reportes_pedidos.php">Reportes de Pedidos</a></li>
            <li><a href="reportes_productos.php">Reportes de Productos</a></li>
        </ul>
        <a href="panel.php" class="btn">Volver al Panel</a>
    </div>

</div>
<!-- Imagen de usuario -->
<img src="img/user.png" alt="Usuario" class="user-icon" data-bs-toggle="collapse" href="#logoutCollapse" aria-expanded="false" aria-controls="logoutCollapse">
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

