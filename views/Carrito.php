<?php
// Carrito.php - PÁGINA DE VISTA DEL CARRITO

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

// Incluir la CLASE, no el controlador AJAX
require_once "../assets/app/controllers/CarritoClass.php";

// Obtener puntos del usuario para mostrar opción de pago con puntos
require_once __DIR__ . "/../assets/app/config/ConnectionController.php";
require_once __DIR__ . "/../assets/app/models/PerfilModel.php";

$dbConn = (new ConnectionController())->connect();
$perfilModel = new PerfilModel($dbConn);
$perfil = $perfilModel->obtenerPerfil($_SESSION['user_id']);
$puntosUsuario = $perfil['puntos_acumulados'] ?? 0;

// Manejar acciones GET (eliminar/vaciar)
if (isset($_GET['eliminar'])) {
    CarritoController::eliminar($_GET['eliminar']);
    // Recargar para ver cambios
    header('Location: Carrito.php');
    exit();
}

if (isset($_GET['vaciar'])) {
    CarritoController::vaciar();
    // Recargar para ver cambios
    header('Location: Carrito.php');
    exit();
}

if (isset($_GET['eliminar_todo'])) {
    CarritoController::eliminarTodo($_GET['eliminar_todo']);
    header('Location: Carrito.php');
    exit();
}

$carrito = CarritoController::obtenerCarrito();
$total = CarritoController::total();

$userName = $_SESSION['user_nombre'] ?? "Usuario";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Carrito</title>
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
            <a href="principal.php">Tienda</a>
            <a href="NivelesClientes.php">Niveles</a>
            <a class="active" href="#">Carrito</a>

            <div class="user-section">
                <a href="Perfil.php" class="user-link"><?php echo htmlspecialchars($userName); ?></a>
                <button id="logoutBtn" class="logout-btn" title="Cerrar sesión">
                    <img src="../assets/img/cerrar-sesion.png" alt="Cerrar sesión" />
                </button>
            </div>
        </div>
    </nav>

    <main>
        <section class="carrito-container">

            <h2 class="carrito-title">Tu carrito</h2>

            <p class="usuario-puntos">Tus puntos: <strong><?php echo intval($puntosUsuario); ?></strong></p>

            <div class="carrito-card">

                <?php if (empty($carrito)) { ?>
                    <p class="carrito-empty">Tu carrito está vacío</p>
                    <a href="principal.php" class="btn-volver-tienda">← Volver a la tienda</a>
                <?php } else { ?>

                    <?php foreach ($carrito as $id => $item) { ?>
                        <div class="carrito-item">
                            <div class="item-info">
                                <span class="item-nombre"><?php echo htmlspecialchars($item['nombre']); ?></span>
                                <span class="item-cantidad">Cantidad: <?php echo $item['cantidad']; ?></span>
                            </div>
                            <div class="item-precio">
                                <span>Mex$ <?php echo number_format($item['precio'], 2); ?> c/u</span>
                                <span class="item-subtotal">
                                    Subtotal: Mex$ <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?>
                                </span>
                            </div>
                            <div class="item-acciones">
                                <a class="btn-eliminar" href="Carrito.php?eliminar=<?php echo $id; ?>" title="Quitar un item">
                                    Eliminar
                                </a>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="carrito-total">
                        <span>Total a pagar:</span>
                        <strong>Mex$ <?php echo number_format($total, 2); ?></strong>
                    </div>

                    <div class="carrito-acciones">
                        <a class="btn-vaciar" href="Carrito.php?vaciar=1">
                            Vaciar carrito
                        </a>
                        <a class="btn-comprar" href="Checkout.php">
                            Proceder al pago
                        </a>
                        <button id="btn-pagar-puntos" class="btn-pagar-puntos">
                            Pagar con puntos
                        </button>
                    </div>

                <?php } ?>
            </div>

        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-logo">
                <img src="../assets/img/LogoEnevo.png" alt="Logo Enevo">
                <p>© 2025 – Todos los derechos reservados</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/main.js"></script>
    <script>
        // Logout SIN confirmación - cierra sesión directamente
        document.getElementById('logoutBtn').addEventListener('click', function() {
            window.location.href = '../assets/app/controllers/LogoutController.php';
        });
        
        // Actualizar contador del carrito
        function actualizarContadorCarrito() {
            fetch('../assets/app/controllers/CarritoController.php?action=get')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Crear o actualizar contador
                        let contador = document.getElementById('cart-count');
                        if (!contador) {
                            contador = document.createElement('span');
                            contador.id = 'cart-count';
                            contador.style.cssText = `
                                background: #f44336;
                                color: white;
                                border-radius: 50%;
                                padding: 2px 8px;
                                font-size: 12px;
                                margin-left: 5px;
                                display: inline-block;
                            `;
                            
                            // Insertar después del enlace del carrito
                            const carritoLink = document.querySelector('a[href="Carrito.php"], a.active');
                            if (carritoLink) {
                                carritoLink.appendChild(contador);
                            }
                        }
                        
                        contador.textContent = data.total_items || 0;
                        contador.style.display = data.total_items > 0 ? 'inline-block' : 'none';
                    }
                })
                .catch(console.error);
        }
        
        // Actualizar al cargar
        document.addEventListener('DOMContentLoaded', actualizarContadorCarrito);

        // Manejar pago con puntos
        const btnPagarPuntos = document.getElementById('btn-pagar-puntos');
        if (btnPagarPuntos) {
            btnPagarPuntos.addEventListener('click', function(e) {
                e.preventDefault();

                if (!confirm('¿Deseas pagar esta compra usando tus puntos acumulados?')) return;

                fetch('../assets/app/controllers/CompraController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=procesar_compra_puntos'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification({
                            message: data.message || 'Compra con puntos realizada',
                            type: 'success',
                            autoHide: 2500,
                            primaryText: 'Ver historial',
                            primaryHref: 'HistorialCompras.php'
                        });
                    } else {
                        if (data.message === 'Puntos insuficientes') {
                            showNotification({
                                message: 'Puntos insuficientes. Necesitas ' + (data.puntos_requeridos || 'N/A') + ' puntos. Tienes: ' + (data.puntos_actuales || 0),
                                type: 'error',
                                primaryText: 'Ver perfil',
                                primaryHref: 'Perfil.php'
                            });
                        } else {
                            showNotification({ message: data.message || 'Error al procesar la compra', type: 'error' });
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    showNotification({ message: 'Error en la petición. Intenta nuevamente.', type: 'error' });
                });
            });
        }
    </script>

</body>
</html>