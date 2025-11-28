
<?php
session_start();

// Verificar si el usuario está logueado Y es admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    header('Location: login.html');
    exit();
}

$userName = $_SESSION['user_nombre'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NivelesAdmin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style/main.css">
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="logo">
            <img src="../assets/img/LogoEnevo2.png" alt="Logo Enevo">
            <span class="brand">Enevo</span>
        </div>

        <div class="nav-links">
            <a href="principalAdmin.php">Tienda</a>
            <a href="NivelesAdmin.php" class="active">Niveles</a>
            <a href="ClientesAdmin.php">Clientes</a>
            <a href="Ventas.php">Ventas</a>

            <div class="user-section">
                <span class="user-link"><?php echo htmlspecialchars($userName); ?></span>
                <button id="logoutBtn" class="logout-btn">
                    <img src="../assets/img/cerrar-sesion.png" alt="Cerrar sesión" />
                </button>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <main class="niveles-container">

        <h2 class="niveles-title">Bienvenido al apartado de niveles</h2>

        <p class="niveles-descripcion">
            Aquí tus compras no solo te dan juegos, también te hacen subir de nivel y ganar recompensas exclusivas.
            <br><br>
            Empiezas en el Nivel Plata, donde por cada compra obtienes 10 puntos. Estos puntos pueden usarse para
            conseguir descuentos e incluso juegos totalmente gratis, dependiendo de cuántos acumules.
            <br><br>
            A medida que compras más, subes de nivel automáticamente.
        </p>

        <div class="niveles-header">
            <h3 class="niveles-subtitle">Nuestros niveles son</h3>
            <button class="btn-agregar" onclick="window.location.href='AgregarNivel.php'">Agregar</button>
        </div>

        <!-- CONTENEDOR DE NIVELES -->
        <div id="nivelesContainer">
            <!-- Los niveles se cargarán dinámicamente aquí -->
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-logo">
                <img src="../assets/img/LogoEnevo.png" alt="Enevo Logo">
                <p>© 2025 – Todos los derechos reservados</p>
            </div>
        </div>
    </footer>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/niveles.js"></script>
</body>

</html>