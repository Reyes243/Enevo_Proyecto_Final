
 <?php
    session_start();

    // Verificar si el usuario está logueado Y es admin
    if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
        header('Location: login.html');
        exit();
    }

    $nombreNivel = $_GET['nombre'] ?? 'Nivel';
    $idNivel = $_GET['id'] ?? 0;

    $userName = $_SESSION['user_nombre'] ?? 'Admin';
?>


<!DOCTYPE html>
<html lang="es">

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Clientes - <?php echo htmlspecialchars($nombreNivel); ?></title>
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
            <a href="#">Clientes</a>
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
    <main class="clientes-page-container">

        <h2 class="clientes-page-title">Clientes en Nivel: <span id="nombreNivelDisplay"><?php echo htmlspecialchars($nombreNivel); ?></span></h2>

        <div class="clientes-page-content">
            <div class="tabla-clientes-wrapper">
                <table class="tabla-clientes-page">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                        </tr>
                    </thead>
                    <tbody id="tablaClientesBody">
                        <!-- Se llenará dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>

            <div class="clientes-page-buttons">
                <button type="button" class="btn-volver-page" onclick="window.location.href='NivelesAdmin.php'">Volver</button>
            </div>
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
    <script src="../assets/js/nivelesClientes.js"></script>
</body>

</html>