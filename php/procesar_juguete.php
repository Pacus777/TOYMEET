<?php
session_start();
$host="localhost";
$usu="root";
$password = "";
$database = "toymeetpagina";
$conn = new mysqli($host, $usu, $password, $database);
// Conexión a la base de datos
if($conn==false){
    die('No se pudo establecer la conexion a la base de datos');
}else{
   
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $nombre = $_POST['nombre'];
    $modelado = empty($_POST['modelado']) ? null : $_POST['modelado'];
    $caracteristicas = $_POST['caracteristicas'];
    $precio = $_POST['precio'];
    $detalles = $_POST['detalles'];
    $marca = $_POST['marca'];
    $estado = $_POST['estado'];
    $cantidad = $_POST['cantidad'];

    // Manejo del archivo de fotografía
    $fotografia = "";
    if (isset($_FILES["fotografia"]) && $_FILES["fotografia"]["error"] == 0) {
        $directorioDestino = "../images/juguetes/fotografias/";
        $nombreArchivo = basename($_FILES["fotografia"]["name"]);
        $nombreArchivo = preg_replace("/[^A-Za-z0-9_.-]/", "_", $nombreArchivo); // Evita caracteres raros
        $rutaDestino = $directorioDestino . uniqid() . "_" . $nombreArchivo;

        if (move_uploaded_file($_FILES["fotografia"]["tmp_name"], $rutaDestino)) {
            $fotografia = $rutaDestino;
        } else {
            echo "Error al subir la imagen.";
            exit;
        }
    }

    // Insertar datos en la base de datos
    $sql = "INSERT INTO juguete (nombre, modelado, fotografia, caracteristicas, precio, detalles, marca, estado, cantidad)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $nombre, $modelado, $fotografia, $caracteristicas, $precio, $detalles, $marca, $estado, $cantidad);

    if ($stmt->execute()) {
        echo "<script>alert('Juguete registrado exitosamente'); window.location.href='../CodePage/index.php';</script>";
    } else {
        echo "Error al registrar el juguete: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
