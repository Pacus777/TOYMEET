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

// Verificar si se ha enviado un archivo
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
    $file_name = $_FILES['foto_perfil']['name'];
    $file_tmp = $_FILES['foto_perfil']['tmp_name'];
    $file_type = $_FILES['foto_perfil']['type'];

    // Validar tipo de archivo (solo imágenes)
    if (in_array($file_type, ['image/jpeg', 'image/png', 'image/gif'])) {
        // Establecer el nombre del archivo para guardarlo de manera única
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = "perfil_" . $id_usuario . "." . $file_ext;

        // Mover el archivo al directorio deseado
        $upload_dir = '../images/usuarios/';
        $upload_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Actualizar la base de datos con la nueva imagen
            $sql = "UPDATE usuario SET foto_perfil = ? WHERE Id_usuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $upload_path, $id_usuario);

            if ($stmt->execute()) {
                echo "Foto de perfil actualizada correctamente.";
                header("Location: ../CodePage/editar_perfil.php"); // Redirigir después de actualizar
            } else {
                echo "Error al actualizar la foto de perfil: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error al subir la imagen.";
        }
    } else {
        echo "Solo se permiten imágenes JPG, PNG o GIF.";
    }
} else {
    echo "No se seleccionó ninguna imagen.";
}

$conn->close();
?>