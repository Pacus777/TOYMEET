<?php
session_start();

// Validar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    echo "Debes iniciar sesión para registrarte como vendedor.";
    exit;
}

// Conexión a la base de datos
$host = "localhost";
$usu = "root";
$password = "";
$database = "toymeetpagina";

$conn = new mysqli($host, $usu, $password, $database);
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Obtener datos del formulario
$estado = $_POST['estado'];
$ciudad = $_POST['ciudad'];
$tiempo = $_POST['tiempo'];
$id_usuario = $_SESSION['id_usuario']; // ID del usuario actual

// Insertar en la tabla vendedor
$sql = "INSERT INTO vendedor (estado, tiempo, ciudad, Id_usuario) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $estado, $tiempo, $ciudad, $id_usuario);

if ($stmt->execute()) {
    // Obtener el Id_vendedor recién insertado
    $id_vendedor = $stmt->insert_id;

    // Insertar en la tabla calificacion_vendedor una calificación inicial de 1
    $puntuacion_inicial = 1;
    $criterio = "Registro automático";
    $sqlCalificacion = "INSERT INTO calificacion_vendedor (puntuacion, criterio, Id_usuario, Id_vendedor) VALUES (?, ?, ?, ?)";
    $stmtCal = $conn->prepare($sqlCalificacion);
    $stmtCal->bind_param("isii", $puntuacion_inicial, $criterio, $id_usuario, $id_vendedor);
    $stmtCal->execute();
    $stmtCal->close();

    // Redireccionar a la página principal
    header("Location: ../CodePage/index.php");
    exit;
} else {
    echo "Error al registrar vendedor: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
