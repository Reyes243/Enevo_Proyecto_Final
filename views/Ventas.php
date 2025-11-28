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
    <title>ClientesAdmin</title>

    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Tu CSS global -->
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
            <a href="NivelesAdmin.php">Niveles</a>
            <a href="ClientesAdmin.php">Clientes</a>
            <a href="Ventas.php" class="active">Ventas</a>

            <div class="user-section">
                <span class="user-link"><?php echo htmlspecialchars($userName); ?></span>
                <button id="logoutBtn" class="logout-btn">
                    <img src="../assets/img/cerrar-sesion.png" alt="Cerrar sesión" />
                </button>
            </div>
        </div>
    </nav>
    <!-- CONTENIDO -->
    <main>
        <section class="contenido">
            <h2>Bienvenido al apartado de Ventas</h2>

            <div class="tabla-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Copias vendidas Totales</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>DARK SOULS: REMASTERED</td>
                            <td>5</td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td>Risk of Rain 2</td>
                            <td>10</td>
                        </tr>

                        <tr>
                            <td>3</td>
                            <td>Clair Obscur: Expedition 33</td>
                            <td>15</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
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

    <!-- Scripts -->
    <script src="../assets/js/main.js"></script>
</body>

</html>