<?php
// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root"; // o el que uses
$password = "";     // o tu contraseña
$database = "toymeetpagina";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del formulario (por GET ya que tu formulario usa GET)
$nickname = $_GET['Nickname'];
$nombre = $_GET['nombre'];
$contrasena = $_GET['password'];
$correo = $_GET['email'];
$ap_paterno = $_GET['ap_paterno'];
$ap_materno = $_GET['ap_materno'];
$fecha_nac = $_GET['fecha_nac'];
$nro_celular = $_GET['celular'];
$sqlCheckNickname = "SELECT * FROM usuario WHERE nickname = '$nickname'";
$resultNickname = $conn->query($sqlCheckNickname);

$sqlCheckEmail = "SELECT * FROM usuario WHERE correo = '$correo'";
$resultEmail = $conn->query($sqlCheckEmail);

// Verificar si el nickname ya está registrado
if ($resultNickname->num_rows > 0) {
    echo "<script>alert('El nickname ya está registrado. Por favor, elige otro.'); window.history.back();</script>";
    exit();  // Detener el script si ya existe el nickname
}

// Verificar si el correo ya está registrado
if ($resultEmail->num_rows > 0) {
    echo "<script>alert('El correo ya está registrado. Por favor, usa otro.'); window.history.back();</script>";
    exit();  // Detener el script si ya existe el correo
}
// Valores predefinidos
$rol = "usuario";
$foto_perfil = null; // No hay foto aún

// Preparar y ejecutar la inserción
$sql = "INSERT INTO usuario (nombre, contrasena, correo, ap_paterno, ap_materno, fecha_nac, nro_celular, rol, foto_perfil,nickname)
VALUES ('$nombre', '$contrasena', '$correo', '$ap_paterno', '$ap_materno', '$fecha_nac', '$nro_celular', '$rol', '$foto_perfil','$nickname')";

if ($conn->query($sql) === TRUE) {
    echo "Usuario registrado.";
    

   
    header("Location: ../CodePage/login.html");
    exit();
} else {
    echo "Error en el registro: " . $conn->error;
}




