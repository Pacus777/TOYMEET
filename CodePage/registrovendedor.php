<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Vendedor - ToyMeet</title>
    <link rel="stylesheet" href="../Styles/registro_vendedor.css">
</head>
<body>
    <form action="../php/procesar_vendedor.php" method="post">
        <div class="contenedor-principal">
            <div class="contenedor">
                <div class="particion particion-1">
                    <h2>Registro de Vendedor</h2>
                    <table>
                        <tr>
                            <th><img src="../images/registro/usuario.png" alt="icono estado"></th>
                            <th><input type="text" name="estado" maxlength="100" placeholder="Estado" required></th>
                        </tr>
                        <tr>
                            <th><img src="../images/registro/apellidos.png" alt="icono ciudad"></th>
                            <th><input type="text" name="ciudad" maxlength="100" placeholder="Ciudad" required></th>
                        </tr>
                        <tr>
                            <th><img src="../images/registro/fecha.png" alt="icono fecha"></th>
                            <th><input type="date" name="tiempo" required></th>
                        </tr>
                    </table>
                    <button class="h" type="submit">Registrarse como Vendedor</button>
                </div>
                <div class="particion particion-2">
                    <img src="../images/TOYMEETLOGO.png" width="300px" height="350px" alt="Imagen Vendedor">
                </div>
            </div>
        </div>
    </form>
</body>
</html>
