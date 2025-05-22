<?php
session_start(); // Iniciar sesión para acceder a los datos guardados

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

// Consulta SQL para obtener los datos del usuario
$sql = "SELECT * FROM usuario WHERE Id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$foto_perfil = $user['foto_perfil'] ? $user['foto_perfil'] : '../images/login/usuario.png'; // Usar la imagen de perfil si existe, sino la por defecto

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - ToyMeet</title>
    <link rel="stylesheet" href="../Styles/editar_perfil.css">
</head>
<body>
    <div class="contenedor-principal">
        <div class="contenedor">
            <div class="particion particion-1">
                <h2>Editar Perfil</h2>
                <form action="../php/procesar_editar_perfil.php" method="POST" enctype="multipart/form-data">
                    <table>
                        <tr>
                            <th><img src="../images/registro/usuario.png" alt=""></th>
                            <th><input type="text" name="nickname" value="<?= $user['nickname'] ?>" maxlength="50" required></th>
                        </tr>
                        <tr>
                            <th><img src="../images/registro/usuario.png" alt=""></th>
                            <th><input type="text" name="nombre" value="<?= $user['nombre'] ?>" maxlength="30" required></th>
                        </tr>
                        <tr>
                            <th><img src="../images/registro/email.png" alt=""></th>
                            <th><input type="email" name="email" value="<?= $user['correo'] ?>" maxlength="100" required></th>
                        </tr>
                        <tr>
                            <th><img src="../images/registro/apellidos.png" alt=""></th>
                            <th><input type="text" name="ap_paterno" value="<?= $user['ap_paterno'] ?>" maxlength="30"></th>
                        </tr>
                        <tr>
                            <th><img src="../images/registro/apellidos.png" alt=""></th>
                            <th><input type="text" name="ap_materno" value="<?= $user['ap_materno'] ?>" maxlength="30"></th>
                        </tr>
                        <tr>
                            <th><img src="../images/registro/fecha.png" alt=""></th>
                            <th><input type="date" name="fecha_nac" value="<?= $user['fecha_nac'] ?>" required></th>
                        </tr>
                        <tr>
                            <th><img src="../images/registro/celular.png" alt=""></th>
                            <th><input type="tel" name="celular" value="<?= $user['nro_celular'] ?>" maxlength="30" required></th>
                        </tr>
                       
                        <tr>
                            <th><label for="current_password">Contraseña actual</label></th>
                            <th><input type="password" name="current_password" id="current_password" placeholder="Contraseña actual (si desea cambiarla)"></th>
                        </tr>
                        <tr>
                            <th><label for="new_password">Nueva contraseña</label></th>
                            <th><input type="password" name="new_password" id="new_password" placeholder="Nueva contraseña (si desea cambiarla)"></th>
                        </tr>
                        <tr>
                            <th><label for="confirm_password">Confirmar nueva contraseña</label></th>
                            <th><input type="password" name="confirm_password" id="confirm_password" placeholder="Confirmar nueva contraseña (si desea cambiarla)"></th>
                        </tr>
                    </table>
                    <button type="submit" class="h">Guardar Cambios</button>
                </form>
            </div>

            <!-- Sección para la foto de perfil -->
            <div class="particion-2">
                <div class="perfil-imagen">
                    <img src="<?php echo (isset($foto_perfil) && $foto_perfil != '../images/login/usuario.png') ? $foto_perfil : '../images/login/usuario.png'; ?>" alt="Foto de perfil" class="foto-perfil">
                    <form action="../php/subir_imagen.php" method="POST" enctype="multipart/form-data">
                        <label for="foto_perfil">Cambiar foto de perfil</label>
                        <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*">
                        <button type="submit">Subir foto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
