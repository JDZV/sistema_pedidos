<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$sql = "DELETE FROM usuarios WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    header("Location: usuarios.php");
    exit();
} else {
    echo "Error al eliminar el usuario: " . $conn->error;
}
?>
<?php
