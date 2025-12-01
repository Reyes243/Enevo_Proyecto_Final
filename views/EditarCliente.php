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
            <a href="ClientesAdmin.php" class="active">Clientes</a>
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
        <div class="editCliente-container">

            <h2 class="editCliente-title">ID Cliente: <span id="idClienteTexto">1</span></h2>

            <div class="editCliente-card">

                <!-- COLUMNA IZQUIERDA -->
                <div class="editCliente-left">

                    <img src="../assets/img/usuario.png" class="editCliente-avatar">

                    <div class="editCliente-info">
                        <p><strong>Nombre:</strong> <span id="nombreTexto"></span></p>
                        <p><strong>Correo:</strong> <span id="correoTexto"></span></p>
                        <p><strong>Nivel:</strong> <span id="nivelTexto"></span></p>
                        <p><strong>Siguiente nivel:</strong> <span id="sigNivelTexto"></span></p>
                        <p><strong>Compras faltantes para siguiente nivel:</strong> <span id="faltantesTexto"></span></p>

                    </div>

                </div>

                <!-- COLUMNA DERECHA (INPUTS) -->
                <div class="editCliente-right">

                    <input type="hidden" id="clienteId">

                    <div class="editCliente-inputBox">
                        <input type="text" id="editarNombre" class="editCliente-input">
                    </div>

                    <div class="editCliente-inputBox">
                        <input type="email" id="editarCorreo" class="editCliente-input">
                    </div>

                </div>

            </div>

            <div class="cliente-botones-2">
                <button class="btn-cancelar" onclick="location.href='ClientesAdmin.php'">Cancelar</button>
                <button class="btn-guardar" onclick="guardarCambios()">Guardar</button>
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

    <!-- Scripts -->
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/clientes.js"></script>
</body>

</html>