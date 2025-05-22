<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../CodePage/login.html");
    exit();
}

$host = "localhost";
$usu = "root";
$password = "";
$database = "toymeetpagina";
$conn = new mysqli($host, $usu, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$id_usuario = $_SESSION['id_usuario'];
$id_vendedor = isset($_GET['id_vendedor']) ? intval($_GET['id_vendedor']) : 0;

// Validar existencia del vendedor
$sql = "SELECT usuario.nickname, usuario.foto_perfil FROM vendedor INNER JOIN usuario ON vendedor.Id_usuario = usuario.Id_usuario WHERE vendedor.Id_vendedor = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_vendedor);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Vendedor no encontrado.";
    exit();
}

$vendedor = $result->fetch_assoc();
$nickname = $vendedor['nickname'];
$foto = $vendedor['foto_perfil'] ? "../images/" . $vendedor['foto_perfil'] : "../images/login/usuario.png";

// Procesar calificación
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $puntuacion = intval($_POST['puntuacion']);
    
    if ($puntuacion >= 1 && $puntuacion <= 5) {
        // Insertar calificación
        $insert = $conn->prepare("INSERT INTO calificacion_vendedor (Id_usuario, Id_vendedor, puntuacion) VALUES (?, ?, ?)");
        $insert->bind_param("iii", $id_usuario, $id_vendedor, $puntuacion);
        
        if ($insert->execute()) {
            echo "<script>alert('¡Gracias por tu calificación!'); window.location.href='vendedores.php';</script>";
        } else {
            echo "Error al registrar la calificación.";
        }
    } else {
        echo "Calificación inválida.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calificar Vendedor</title>
    <link rel="stylesheet" href="../Styles/calificar_vendedor.css">
</head>
<body>
    
       
            
            <form method="POST">
                <div class="contenedor-principal">

                <div class="contenedor">
                    <div class="particion particion-1">
                        <h2>Califica a <?php echo htmlspecialchars($nickname); ?></h2>
  <label for="puntuacion">Selecciona una calificación (1 a 5):</label><br>
                <select name="puntuacion" id="puntuacion" required>
                    <option value="">--Seleccionar--</option>
                    <option value="1">⭐ 1</option>
                    <option value="2">⭐⭐ 2</option>
                    <option value="3">⭐⭐⭐ 3</option>
                    <option value="4">⭐⭐⭐⭐ 4</option>
                    <option value="5">⭐⭐⭐⭐⭐ 5</option>
                </select><br>
                <button type="submit" class="h">Enviar calificación</button>
                    </div>
                    <div class="particion particion-2">
<img src="<?php echo $foto; ?>" alt="Foto de perfil" width="100px" height="100px">
                    </div>
                </div>
                </div>
              
            </form>
        
        
            
        
   
</body>
</html>
