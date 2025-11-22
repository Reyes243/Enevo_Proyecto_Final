
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
    <title>NivelesClientes</title>
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
            <a href="#" class="active">Niveles</a>
            <a href="#">Clientes</a>
            <a href="Ventas.php">Ventas</a>

            <div class="user-section">
                <span class="user-link"> <?php echo htmlspecialchars($userName); ?></span>
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
            conseguir descuentos o más juegos totalmente gratis.
            <br><br>
            A medida que compres más, subes de nivel automáticamente.
        </p>

        <h3 class="niveles-subtitle">Nuestros niveles son</h3>

        <!-- NIVEL PLATA -->
        <div class="nivel-card">
            <h3>Plata</h3>
            <p>Nivel inicial. Ganas 10 puntos por compra.</p>
        </div>

        <!-- NIVEL ORO -->
        <div class="nivel-card">
            <h3>Oro</h3>
            <p>Se desbloquea tras 5 compras. Aquí recibes 15 puntos por compra y pronto disfrutarás de un 10% de
                descuento.</p>
        </div>

        <!-- NIVEL PLATINO -->
        <div class="nivel-card">
            <h3>Platino</h3>
            <p>Lo alcanzas con 10 compras. Obtienes 20 puntos por compra y próximamente tendrás un 15% de descuento</p>
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

</body>

</html>