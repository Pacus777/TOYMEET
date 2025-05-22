<?php
session_start();

// Verificar si el usuario está logueado
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

// Obtener datos del usuario
$sql = "SELECT * FROM usuario WHERE Id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $nickname = $user['nickname'];
    $nombre = $user['nombre'];
    $ap_paterno = $user['ap_paterno'];
    $ap_materno = $user['ap_materno'];
    $correo = $user['correo'];
    $fecha_nac = $user['fecha_nac'];
    $nro_celular = $user['nro_celular'];
    $rol = $user['rol'];
    $foto_perfil = $user['foto_perfil'] ? $user['foto_perfil'] : 'login/usuario.png';
} else {
    echo "No se encontraron datos del usuario.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo - ToyMeet</title>
    <link rel="stylesheet" href="../Styles/estilos.css">
    <style>
        .juguete-card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin: 10px;
            width: 250px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: inline-block;
            vertical-align: top;
        }

        .juguete-card img {
            max-width: 100%;
            height: 180px;
            object-fit: contain;
            border-radius: 5px;
        }

        .catalogo-container {
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="contenedor-total">
        <header class="header-full">
            <div class="logo">
                <img src="../images/TOYMEETLOGO.png" width="150" alt="Logo ToyMeet">
                <a href="#">TOY MEET</a>
            </div>

            <nav class="nav-full">
                <a href="#">Inicio</a>
                <a href="categorias.php">Categorias</a>
                <a href="#">Marcas</a>
                <a href="vendedores.php">Vendedores</a>
            </nav>

            <button id="userButton">
                <img src="../images/login/usuario.png" alt="" width="50px" height="50px">
            </button>
            <div id="userSidebar" class="user-sidebar">
                <button class="close-btn" onclick="toggleUserSidebar()">×</button><br><br>
                <div class="perfil">
                    <img src="../images/<?php echo $foto_perfil; ?>" alt="Foto de perfil" width="100" height="100">
                    <p><strong>Nickname:</strong> <?php echo $nickname; ?></p>
                    <p><strong>Nombre Completo:</strong> <?php echo $nombre . " " . $ap_paterno . " " . $ap_materno; ?></p>
                    <p><strong>Correo:</strong> <?php echo $correo; ?></p>
                    <p><strong>Fecha de Nacimiento:</strong> <?php echo $fecha_nac; ?></p>
                    <p><strong>Número de Celular:</strong> <?php echo $nro_celular; ?></p>
                    <p><strong>Rol:</strong> <?php echo $rol; ?></p>
                    <a href="../CodePage/editar_perfil.php">Editar Perfil</a>
                        <form action="../php/verificar_vendedor.php" method="post">
                            <button type="submit" style="margin-top: 10px;">Vender</button>
                        </form>
                </div>
            </div>
        </header>

        <div class="contenido-principal">
            <main class="main-full">
                <h2 style="color: Black; text-align: center;">Catálogo de Juguetes</h2>
                <div class="catalogo-container">
                    <?php
                    $sql = "SELECT * FROM juguete";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($juguete = $result->fetch_assoc()) {
        $id_juguete = $juguete['Id_juguete'];
        $nombre = htmlspecialchars($juguete['nombre']);
        $precio = htmlspecialchars($juguete['precio']);
        $foto = $juguete['fotografia'];
        $ruta_foto = (!empty($foto) && file_exists("../fotografias/$foto")) 
            ? "../fotografias/$foto" 
            : "../images/default-juguete.png";

        echo "
        <a href='vista_de_juguete.php?id=$id_juguete' style='text-decoration: none; color: inherit;'>
            <div class='juguete-card'>
                <img src='$ruta_foto' alt='Imagen del juguete'>
                <h3>$nombre</h3>
                <p><strong>Precio:</strong> Bs/. $precio</p>
            </div>
        </a>";
    }
} else {
    echo "<p style='color: Black;'>No hay juguetes registrados.</p>";
}


                    $conn->close();
                    ?>
                </div>
            </main>

            <aside class="aside-full">
                <div class="widget">
                    <div class="imagen">
                        <img src="../images/pagina/marvel legends.png" width="260px" height="250px">
                    </div>
                </div>

                <div class="widget">
                    <div class="parrafo">
                        <h3>Sobre nosotros</h3>
                        <p>Toy Meet es una página de venta de juguetes y figuras de acción que busca conectar a coleccionistas con vendedores confiables.</p>
                    </div>
                </div>
            </aside>
        </div>

        <footer class="footer-full">
            <section class="links">
                
                <a href="polycam.php">Polycam</a>
                <a href="novedades.php">Novedades</a>
                <a href="login.html">Login</a>
            </section>

            <div class="social">
                <a href="#">FB</a>
                <a href="#">IG</a>
            </div>
        </footer>
    </div>

    <script>
        const sidebar = document.getElementById('userSidebar');
        const userButton = document.getElementById('userButton');

        userButton.addEventListener('click', toggleUserSidebar);

        function toggleUserSidebar() {
            sidebar.classList.toggle('visible');
        }
    </script>
</body>
</html>
