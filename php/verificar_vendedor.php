<?php
session_start();

// Verificar si el usuario está en sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../CodePage/login.html");
    exit();
}

// Conexión a la base de datos
$host = "localhost";
$usu = "root";
$password = "";
$database = "toymeetpagina";
$conn = new mysqli($host, $usu, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar si el usuario ya está en la tabla vendedor
$sql = "SELECT 1 FROM vendedor WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Ya es vendedor, redirige a su panel
    header("Location: ../CodePage/registro_juguete.html");
} else {
    // No es vendedor aún, redirige al registro
    header("Location: ../CodePage/registrovendedor.php");
}

$conn->close();
exit();
?>
