<?php
session_start();

// RUTAS CORREGIDAS - todos los archivos están en assets/app/
require_once __DIR__ . '/../assets/app/config/ConnectionController.php';
require_once __DIR__ . '/../assets/app/controllers/PerfilController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

if ($_SESSION['user_rol'] === 'admin') {
    header('Location: admin.php');
    exit();
}

// Crear la conexión usando tu ConnectionController
$connection = new ConnectionController();
$conn = $connection->connect();

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

$controller = new PerfilController($conn);
$userName = $_SESSION['user_nombre'] ?? 'Usuario';

// Procesar actualización del perfil
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $resultado = $controller->actualizarPerfil($_SESSION['user_id'], $_POST);
    
    if ($resultado['success']) {
        $mensaje = '<div class="alert success">' . $resultado['message'] . '</div>';
        // Recargar datos actualizados
        $datosPerfil = $controller->mostrarPerfil($_SESSION['user_id']);
    } else {
        $mensaje = '<div class="alert error">' . $resultado['message'] . '</div>';
        $datosPerfil = $controller->mostrarPerfil($_SESSION['user_id']);
    }
} else {
    // Cargar datos del perfil
    $datosPerfil = $controller->mostrarPerfil($_SESSION['user_id']);
}

$perfil = $datosPerfil['perfil'];
$proximo_nivel = $datosPerfil['proximo_nivel'];
$compras_faltantes = $datosPerfil['compras_faltantes'];
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
            <h2 class="perfil-title">Mi Perfil</h2>

            <?php echo $mensaje; ?>

            <div class="perfil-card">
                <div class="perfil-left">
                    <img src="../assets/img/usuario.png" alt="Foto usuario" class="perfil-avatar">

                    <!-- Formulario de edición -->
                    <form method="POST" class="perfil-form">
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($perfil['nombre'] ?? $perfil['username'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Correo:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($perfil['email'] ?? ''); ?>" required>
                        </div>

                        <button type="submit" class="btn-actualizar">Actualizar Perfil</button>
                    </form>

                    <div class="nivel-info">
                        <p><strong>Nivel Actual:</strong> <?php echo htmlspecialchars($perfil['nivel_nombre'] ?? 'Bronce'); ?></p>
                        
                        <?php if ($proximo_nivel): ?>
                            <p><strong>Siguiente nivel:</strong> <?php echo htmlspecialchars($proximo_nivel['nombre']); ?></p>
                            <p><strong>Puntos faltantes:</strong> <?php echo max(0, ($proximo_nivel['puntos_minimos'] - ($perfil['puntos_acumulados'] ?? 0))); ?> puntos</p>
                            <p><strong>Compras faltantes:</strong> <?php echo $compras_faltantes; ?></p>
                        <?php else: ?>
                            <p><strong>¡Felicidades! Has alcanzado el nivel máximo.</strong></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="perfil-right">
                    <h3>Puntos Totales</h3>
                    <span class="puntos-num"><?php echo $perfil['puntos_acumulados'] ?? 0; ?></span>

                    <div class="perfil-buttons">
                        <a href="HistorialCompras.php" class="btn-perfil">Ver Historial de Compras</a>
                        <a href="../ExportarDatos.php" class="btn-perfil">Descargar Mis Datos</a>
                    </div>

                    <div class="info-adicional">
                        <p><strong>Total de Compras:</strong> <?php echo $perfil['total_compras'] ?? 0; ?></p>
                        <p><strong>Fecha de Registro:</strong> <?php echo date('d/m/Y', strtotime($perfil['fecha_registro'] ?? 'now')); ?></p>
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