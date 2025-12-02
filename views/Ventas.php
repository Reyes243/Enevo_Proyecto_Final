<?php
session_start();

// Verificar si el usuario está logueado Y es admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    header('Location: login.html');
    exit();
}

// Incluir el modelo de compras
require_once "../assets/app/models/CompraModel.php";

$userName = $_SESSION['user_nombre'] ?? 'Admin';

// Obtener estadísticas de ventas
$compraModel = new CompraModel();
$ventas = $compraModel->obtenerEstadisticasVentas();
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
                        <?php if (empty($ventas)) { ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 40px; color: #666;">
                                    No hay ventas registradas aún.
                                </td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($ventas as $venta) { ?>
                                <tr>
                                    <td><?php echo $venta['id']; ?></td>
                                    <td><?php echo htmlspecialchars($venta['nombre']); ?></td>
                                    <td><?php echo number_format($venta['copias_vendidas']); ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
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