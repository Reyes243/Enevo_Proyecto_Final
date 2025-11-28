<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

if ($_SESSION['user_rol'] === 'admin') {
    header('Location: admin.php');
    exit();
}

$userName = $_SESSION['user_nombre'] ?? 'Usuario';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil</title>
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
            <a href="principal.php">Tienda</a>
            <a href="NivelesClientes.php">Niveles</a>
            <a href="Carrito.php">Carrito</a>

            <div class="user-section">
                <a class="user-link active"><?php echo htmlspecialchars($userName); ?></a>
                <button id="logoutBtn" class="logout-btn">
                    <img src="../assets/img/cerrar-sesion.png" alt="Cerrar sesión" />
                </button>
            </div>
        </div>
    </nav>

    <main>
        <!-- CONTENIDO DEL PERFIL -->
        <section class="perfil-container">
            <h2 class="perfil-title">Perfil</h2>

            <div class="perfil-card">
                <div class="perfil-left">
                    <img src="../assets/img/usuario.png" alt="Foto usuario" class="perfil-avatar">

                    <p><strong>Nombre:</strong> Carlos Hernandez</p>
                    <p><strong>Correo:</strong> CarlosH@gmail.com</p>
                    <br>
                    <p><strong>Nivel:</strong> Plata</p>
                    <p><strong>Siguiente nivel:</strong> Oro</p>
                    <p><strong>Compras faltantes para siguiente nivel:</strong> 1</p>
                </div>

                <div class="perfil-right">
                    <h3>Puntos Totales</h3>
                    <span class="puntos-num">30</span>

                    <div class="perfil-buttons">
                        <a href="HistorialCompras.php" class="btn-perfil">Ver Historial de compras</a>
                        <a href="#" class="btn-perfil">Descargar</a>
                    </div>
                </div>
            </div>
        </section>
    </main>





    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-logo">
                <img src="../assets/img/LogoEnevo.png" alt="Enevo Logo">
                <p>© 2025 – Todos los derechos reservados</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="../assets/js/main.js"></script>
</body>

</html>