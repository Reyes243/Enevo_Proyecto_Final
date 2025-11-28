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
    <title>Historial de compras</title>
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
                <a href="Perfil.php" class="user-link active"><?php echo htmlspecialchars($userName); ?></a>
                <button id="logoutBtn" class="logout-btn">
                    <img src="../assets/img/cerrar-sesion.png" alt="Cerrar sesión" />
                </button>
            </div>
        </div>
    </nav>

    <main>
        <section class="historial-container">

            <h2 class="historial-title">Historial de compras</h2>

            <div class="historial-card">

                <table class="historial-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Total</th>
                            <th>Puntos Generados</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>DARK SOULS: REMASTERED</td>
                            <td>Compra</td>
                            <td>Mex$ 549.00</td>
                            <td>10</td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td>Risk of Rain 2</td>
                            <td>Canje</td>
                            <td>Puntos 45</td>
                            <td>0</td>
                        </tr>
                    </tbody>
                </table>

                <div class="historial-btn-box">
                    <a href="#" class="historial-btn">Descargar</a>
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