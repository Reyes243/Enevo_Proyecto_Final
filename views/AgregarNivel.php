<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    header('Location: login.html');
    exit();
}

$userName = $_SESSION['user_nombre'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="es">

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Agregar Nivel</title>
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
    <main>
        <div class="form-page-container">
            <h2 class="form-page-title">Bienvenido al apartado de Agregar Niveles</h2>

            <div class="form-page-content">
                <form id="formAgregarNivel" class="form-nivel-page">

                    <div class="form-nivel-group">
                        <label>Nombre:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-nivel-group">
                        <label>Puntos:</label>
                        <input type="number" id="puntos" name="puntos" required>
                    </div>

                    <div class="form-nivel-group">
                        <label>Compras Necesarias para subir de nivel:</label>
                        <input type="number" id="compras" name="compras" required>
                    </div>

                    <div class="form-nivel-group">
                        <label>Descripción:</label>
                        <textarea id="descripcion" name="descripcion" rows="5" required></textarea>
                    </div>

                    <div class="form-page-buttons">
                        <button type="button" class="btn-cancelar-page" onclick="window.location.href='NivelesAdmin.php'">Cancelar</button>
                        <button type="submit" class="btn-guardar-page">Guardar</button>
                    </div>
                </form>
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
    <script src="../assets/js/niveles.js"></script>
</body>

</html>