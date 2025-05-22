<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    // Si no está logueado, redirigir al login
    header("Location: login.html");
    exit();
}

$host = "localhost";
$usu = "root";
$password = "";
$database = "toymeetpagina";
$conn = new mysqli($host, $usu, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id_usuario = $_SESSION['id_usuario']; // Obtener el id del usuario desde la sesión

// Obtener los datos del formulario
$nickname = $_POST['nickname'];
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$ap_paterno = $_POST['ap_paterno'];
$ap_materno = $_POST['ap_materno'];
$fecha_nac = $_POST['fecha_nac'];
$celular = $_POST['celular'];

// Obtener las contraseñas si se han ingresado
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];
$sql = "SELECT * FROM usuario WHERE (nickname = ? OR correo = ?) AND Id_usuario != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $nickname, $email, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si ya existe el nickname o correo, mostrar error
    echo "<script>alert('El nickname o correo ya está registrado. Por favor, elige otro.'); window.history.back();</script>";
    exit();
}

// Verificar la contraseña actual si el usuario desea cambiarla
$sql = "SELECT contrasena FROM usuario WHERE Id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Si el usuario desea cambiar la contraseña
if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
    // Verificar si la contraseña actual es correcta
    if ($user['contrasena'] === $current_password) {
        // Verificar si las nuevas contraseñas coinciden
        if ($new_password === $confirm_password) {
            // Actualizar la contraseña
            $sql = "UPDATE usuario SET contrasena = ? WHERE Id_usuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_password, $id_usuario);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Las contraseñas no coinciden.";
            exit();
        }
    } else {
        echo "La contraseña actual es incorrecta.";
        exit();
    }
}

// Actualizar los otros datos del usuario
$sql = "UPDATE usuario SET nickname = ?, nombre = ?, correo = ?, ap_paterno = ?, ap_materno = ?, fecha_nac = ?, nro_celular = ? WHERE Id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssi", $nickname, $nombre, $email, $ap_paterno, $ap_materno, $fecha_nac, $celular, $id_usuario);
$stmt->execute();
$stmt->close();

// Subir foto de perfil (si se ha enviado una nueva imagen)
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $foto_tmp = $_FILES['foto_perfil']['tmp_name'];
    $foto_nombre = $_FILES['foto_perfil']['name'];
    $foto_destino = "../images/usuarios/" . $id_usuario . "_" . $foto_nombre;
    
    if (move_uploaded_file($foto_tmp, $foto_destino)) {
        $sql = "UPDATE usuario SET foto_perfil = ? WHERE Id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $foto_destino, $id_usuario);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error al subir la imagen.";
        exit();
    }
}

// Redirigir a la página de perfil o a donde desees
echo '<meta http-equiv="refresh" content="1;url=../CodePage/login.html">';
exit();

$conn->close();
?>
