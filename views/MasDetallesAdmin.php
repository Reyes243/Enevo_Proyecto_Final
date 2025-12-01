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
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detalles del Juego - Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style/main.css">
</head>

<body>

    <nav class="navbar">
        <div class="logo">
            <img src="../assets/img/LogoEnevo2.png" alt="Logo Enevo">
            <span class="brand">Enevo</span>
        </div>

        <div class="nav-links">
            <a href="principalAdmin.php">Tienda</a>
            <a href="NivelesAdmin.php">Niveles</a>
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

    <main class="novedades" style="max-width: 760px; margin: 20px auto;">
        <div class="details-card">

            <h1 id="game-title" class="details-title">Cargando...</h1>

            <img id="game-img" class="details-hero" src="../assets/img/LogoEnevo.png" alt="Juego">

            <div class="buy-row">
                <div class="buy-text">
                    <strong id="buy-title">Cargando...</strong><br>
                    <small>Versión digital · Plataforma: PC</small>
                </div>

                <div class="buy-actions">
                    <div class="price-box" id="game-price">$0.00</div>
                    <button class="btn-buy" disabled style="opacity: 0.6; cursor: not-allowed;">Vista Admin</button>
                </div>
            </div>

            <div class="section">
                <h4>ACERCA DE ESTE JUEGO</h4>
                <p class="text-muted" id="game-desc">Cargando descripción...</p>
            </div>

            <div class="section requirements">
                <h4>REQUISITOS DEL SISTEMA</h4>
                <ul id="req-list"></ul>
            </div>
        </div>
    </main>

    <footer class="site-footer" style="margin-top:20px;">
        <div class="footer-inner">
            <div class="footer-logo">
                <img src="../assets/img/LogoEnevo.png" alt="Enevo Logo">
                <p>© 2025 – Todos los derechos reservados</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/detallesjuegos.js"></script>

</body>

</html>