<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es administrador o trabajador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol_id'] != 1 && $_SESSION['rol_id'] != 2)) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$sql = "DELETE FROM pedidos WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    header("Location: pedidos.php");
    exit();
} else {
    echo "Error al eliminar el pedido: " . $conn->error;
}
?>
