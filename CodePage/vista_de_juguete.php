<?php
session_start();

// Verifica que el usuario esté logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../CodePage/login.html");
    exit();
}

// Verifica que se haya enviado un ID
if (!isset($_GET['id'])) {
    echo "ID del juguete no especificado.";
    exit();
}

$id_juguete = $_GET['id'];
$id_usuario = $_SESSION['id_usuario'];

// Conexión a la base de datos
$host = "localhost";
$usu = "root";
$password = "";
$database = "toymeetpagina";
$conn = new mysqli($host, $usu, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener datos del usuario
$sql_usuario = "SELECT * FROM usuario WHERE Id_usuario = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $id_usuario);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();

if ($result_usuario->num_rows > 0) {
    $user = $result_usuario->fetch_assoc();
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
    exit();
}

// Consulta para obtener el juguete
$sql = "SELECT j.*, u.nickname as vendedor_nombre, u.correo as vendedor_correo, u.nro_celular as vendedor_telefono 
        FROM juguete j 
        LEFT JOIN usuario u ON j.id_vendedor = u.Id_usuario 
        WHERE j.Id_juguete = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_juguete);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Juguete no encontrado.";
    exit();
}

$juguete = $result->fetch_assoc();

// Consulta para obtener juguetes relacionados (de la misma marca o categoría)
$sql_relacionados = "SELECT * FROM juguete 
                    WHERE marca = ? 
                    AND Id_juguete != ? 
                    LIMIT 4";
$stmt_relacionados = $conn->prepare($sql_relacionados);
$stmt_relacionados->bind_param("si", $juguete['marca'], $id_juguete);
$stmt_relacionados->execute();
$result_relacionados = $stmt_relacionados->get_result();

// Verificar si el usuario actual es el vendedor del juguete
$es_vendedor = false;
if (isset($juguete['id_vendedor']) && $juguete['id_vendedor'] == $id_usuario) {
    $es_vendedor = true;
}

// Funcionalidad para agregar a favoritos (simulada)
$en_favoritos = false;
// Aquí se implementaría la lógica para verificar si está en favoritos

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Juguete - ToyMeet</title>
    <link rel="stylesheet" href="../Styles/vistadejuguete.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="contenedor-total">
        <!-- Header -->
        <header class="header-full">
            <div class="logo">
                <img src="../images/TOYMEETLOGO.png" width="150" alt="Logo ToyMeet">
                <a href="#">TOY MEET</a>
            </div>

            <nav class="nav-full">
                <a href="index.php">Inicio</a>
                <a href="categorias.php">Categorias</a>
                <a href="#">Marcas</a>
                <a href="vendedores.php">Vendedores</a>
            </nav>

            <button id="userButton">
                <img src="../images/<?php echo $foto_perfil; ?>" alt="" width="50px" height="50px">
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
                    <a href="../CodePage/editar_perfil.php" class="btn btn-secondary">Editar Perfil</a>
                    <form action="../php/verificar_vendedor.php" method="post">
                        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Vender</button>
                    </form>
                    <a href="../php/cerrar_sesion.php" class="btn btn-danger" style="margin-top: 10px;">Cerrar Sesión</a>
                </div>
            </div>
        </header>

        <!-- Contenido Principal -->
        <div class="contenido-principal">
            <main class="main-full">
                <div class="detalle-container">
                    <div class="juguete-header">
                        <h1><?php echo htmlspecialchars($juguete['nombre']); ?></h1>
                        <?php if(isset($juguete['categoria']) && !empty($juguete['categoria'])): ?>
                            <span class="etiqueta"><?php echo htmlspecialchars($juguete['categoria']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="juguete-content">
                        <div class="juguete-imagen">
                            <?php
                                $foto = $juguete['fotografia'];
                                $ruta_foto = (!empty($foto) && file_exists("../fotografias/$foto")) 
                                    ? "../fotografias/$foto" 
                                    : "../images/default-juguete.png";
                            ?>
                            <img src="<?php echo $ruta_foto; ?>" alt="Imagen del juguete" id="juguete-img">
                            
                            <!-- Aquí iría un carrusel de imágenes si hubiera más de una foto -->
                        </div>
                        
                        <div class="juguete-info">
                            <div class="juguete-precio">
                                Bs/. <?php echo htmlspecialchars($juguete['precio']); ?>
                                <button class="favorito-btn <?php echo $en_favoritos ? 'active' : ''; ?>" id="favoritoBtn" title="Agregar a favoritos">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                            
                            <div>
                                <p><strong>Marca:</strong> <?php echo htmlspecialchars($juguete['marca']); ?></p>
                                <p><strong>Estado:</strong> <?php echo htmlspecialchars($juguete['estado']); ?></p>
                                <p><strong>Disponible:</strong> <?php echo htmlspecialchars($juguete['cantidad']); ?> unidades</p>
                            </div>

                            <!-- Selector de cantidad -->
                            <div class="cantidad-selector">
                                <span><strong>Cantidad:</strong></span>
                                <button class="cantidad-btn" id="decrementBtn">-</button>
                                <input type="number" class="cantidad-input" id="cantidadInput" value="1" min="1" max="<?php echo htmlspecialchars($juguete['cantidad']); ?>">
                                <button class="cantidad-btn" id="incrementBtn">+</button>
                            </div>
                            
                            <!-- Botones de acción -->
                            <div>
                                <button class="btn btn-primary" id="comprarBtn">
                                    <i class="fas fa-shopping-cart"></i> Contactar vendedor
                                </button>
                                <!--<button class="btn btn-secondary" id="carritoBtn">
                                    <i class="fas fa-cart-plus"></i> Añadir al carrito
                                </button>-->
                                
                                <?php if($es_vendedor): ?>
                                    <a href="editar_juguete.php?id=<?php echo $juguete['Id_juguete']; ?>" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Información del vendedor -->
                            <div class="vendedor-info">
                                <h3>Información del vendedor</h3>
                                <p><i class="fas fa-user"></i> <strong>Vendedor:</strong> <?php echo isset($juguete['vendedor_nombre']) ? htmlspecialchars($juguete['vendedor_nombre']) : "No disponible"; ?></p>
                                <p><i class="fas fa-envelope"></i> <strong>Contacto:</strong> <?php echo isset($juguete['vendedor_correo']) ? htmlspecialchars($juguete['vendedor_correo']) : "No disponible"; ?></p>
                                <p><i class="fas fa-phone"></i> <strong>Teléfono:</strong> <?php echo isset($juguete['vendedor_telefono']) ? htmlspecialchars($juguete['vendedor_telefono']) : "No disponible"; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pestañas para más información -->
                    <div class="tabs">
                        <div class="tab active" data-tab="descripcion">Descripción</div>
                        <div class="tab" data-tab="caracteristicas">Características</div>
                        <div class="tab" data-tab="valoraciones">Valoraciones</div>
                    </div>
                    
                    <div class="tab-content active" id="descripcion">
                        <p><?php echo !empty($juguete['descripcion']) ? nl2br(htmlspecialchars($juguete['descripcion'])) : "No hay descripción disponible para este juguete."; ?></p>
                    </div>
                    
                    <div class="tab-content" id="caracteristicas">
                        <div class="juguete-caracteristicas">
                            <?php echo !empty($juguete['caracteristicas']) ? nl2br(htmlspecialchars($juguete['caracteristicas'])) : "No hay características detalladas disponibles."; ?>
                        </div>
                    </div>
                    
                    <div class="tab-content" id="valoraciones">
                        <!-- Aquí irían las valoraciones de los usuarios, simulamos algunas -->
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span>(4.0/5)</span>
                        </div>
                        <p><em>Esta sección mostrará las valoraciones de los usuarios cuando se implemente esa funcionalidad.</em></p>
                    </div>
                    
                    <!-- Sección de juguetes relacionados -->
                    <?php if($result_relacionados->num_rows > 0): ?>
                    <div class="juguetes-relacionados">
                        <h3>Productos relacionados</h3>
                        <div class="relacionados-grid">
                            <?php while($relacionado = $result_relacionados->fetch_assoc()): 
                                $foto_rel = $relacionado['fotografia'];
                                $ruta_foto_rel = (!empty($foto_rel) && file_exists("../fotografias/$foto_rel")) 
                                    ? "../fotografias/$foto_rel" 
                                    : "../images/default-juguete.png";
                            ?>
                            <a href="vista_de_juguete.php?id=<?php echo $relacionado['Id_juguete']; ?>" class="relacionado-card">
                                <img src="<?php echo $ruta_foto_rel; ?>" alt="<?php echo htmlspecialchars($relacionado['nombre']); ?>">
                                <div class="relacionado-info">
                                    <h4><?php echo htmlspecialchars($relacionado['nombre']); ?></h4>
                                    <p class="relacionado-precio">Bs/. <?php echo htmlspecialchars($relacionado['precio']); ?></p>
                                </div>
                            </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </main>

            <!-- Sidebar -->
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

        <!-- Footer -->
        <footer class="footer-full">
            <section class="links">
                <a href="polycam.php">Polycam</a>
                <a href="novedades.php">Novedades</a>
                <a href="../CodePage/contacto.php">Contacto</a>
            </section>

            <div class="social">
                <a href="#"><i class="fab fa-facebook-f"></i> Facebook</a>
                <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
            </div>
        </footer>
    </div>

    <script>
        // Script para el panel lateral de usuario
        const sidebar = document.getElementById('userSidebar');
        const userButton = document.getElementById('userButton');

        userButton.addEventListener('click', toggleUserSidebar);

        function toggleUserSidebar() {
            sidebar.classList.toggle('visible');
        }

        // Script para mostrar/ocultar pestañas
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Quitar la clase active de todas las pestañas
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                // Añadir la clase active a la pestaña clicada
                this.classList.add('active');
                
                // Ocultar todos los contenidos de las pestañas
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Mostrar el contenido correspondiente a la pestaña clicada
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Script para el botón de favoritos
        const favoritoBtn = document.getElementById('favoritoBtn');
        favoritoBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            // Aquí se implementaría la lógica para guardar el favorito en la base de datos
            
            // Mostrar mensaje
            if (this.classList.contains('active')) {
                alert('¡Agregado a favoritos!');
            } else {
                alert('Eliminado de favoritos');
            }
        });

        // Script para el selector de cantidad
        const decrementBtn = document.getElementById('decrementBtn');
        const incrementBtn = document.getElementById('incrementBtn');
        const cantidadInput = document.getElementById('cantidadInput');
        const maxCantidad = <?php echo htmlspecialchars($juguete['cantidad']); ?>;

        decrementBtn.addEventListener('click', function() {
            let value = parseInt(cantidadInput.value, 10);
            if (value > 1) {
                cantidadInput.value = value - 1;
            }
        });

        incrementBtn.addEventListener('click', function() {
            let value = parseInt(cantidadInput.value, 10);
            if (value < maxCantidad) {
                cantidadInput.value = value + 1;
            }
        });

        cantidadInput.addEventListener('change', function() {
            let value = parseInt(this.value, 10);
            if (isNaN(value) || value < 1) {
                this.value = 1;
            } else if (value > maxCantidad) {
                this.value = maxCantidad;
            }
        });

        // Script para los botones de acción
        const comprarBtn = document.getElementById('comprarBtn');
        const carritoBtn = document.getElementById('carritoBtn');

        comprarBtn.addEventListener('click', function() {
            // Aquí se implementaría la lógica para comprar directamente
            alert('Se contactará al vendedor.');
            // window.location.href = 'proceso_compra.php?id=<?php echo $id_juguete; ?>&cantidad=' + cantidadInput.value;
        });

        carritoBtn.addEventListener('click', function() {
            // Aquí se implementaría la lógica para añadir al carrito
            alert('¡Producto añadido al carrito!');
        });

        // Script para zoom de imagen
        const jugueteImg = document.getElementById('juguete-img');
        jugueteImg.addEventListener('click', function() {
            // Aquí se podría implementar un visor de imagen ampliada o lightbox
            this.classList.toggle('expanded');
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>