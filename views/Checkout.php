<?php
// Checkout.php - PÁGINA DE CONFIRMACIÓN DE COMPRA

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

// Incluir controladores necesarios
require_once "../assets/app/controllers/CarritoClass.php";
require_once "../assets/app/controllers/CompraController.php";

// Obtener datos del carrito
$carrito = CarritoController::obtenerCarrito();
$total = CarritoController::total();
$puntos_a_generar = floor($total / 10);

// Si el carrito está vacío, redirigir
if (empty($carrito)) {
    header('Location: Carrito.php');
    exit();
}

$userName = $_SESSION['user_nombre'] ?? "Usuario";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Confirmar Compra</title>
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
            <a href="Carrito.php">Carrito</a>

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

            <h2 class="carrito-title">Confirmar Compra</h2>

            <div class="carrito-card">

                <p style="text-align: center; color: #666; margin-bottom: 20px;">
                    Revisa tu pedido antes de confirmar la compra
                </p>

                <!-- Resumen de productos -->
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
                    </div>
                <?php } ?>

                <!-- Total y puntos -->
                <div class="carrito-total">
                    <span>Total a pagar:</span>
                    <strong>Mex$ <?php echo number_format($total, 2); ?></strong>
                </div>

                <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center;">
                    <p style="margin: 0; color: #2e7d32; font-weight: 600;">
                         Ganarás <?php echo $puntos_a_generar; ?> punto<?php echo $puntos_a_generar != 1 ? 's' : ''; ?> con esta compra
                    </p>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">
                        (1 punto por cada $10 MXN)
                    </p>
                </div>

                <!-- Botones de acción -->
                <div class="carrito-acciones">
                    <a class="btn-vaciar" href="Carrito.php">
                        Volver al carrito
                    </a>
                    <button class="btn-comprar" id="btnConfirmarCompra">
                        Confirmar y Pagar
                    </button>
                </div>

                <!-- Mensaje de resultado (oculto por defecto) -->
                <div id="mensajeResultado" style="display: none; margin-top: 20px; padding: 15px; border-radius: 8px; text-align: center;"></div>

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

    <script>
        // Logout
        document.getElementById('logoutBtn').addEventListener('click', function() {
            window.location.href = '../assets/app/controllers/LogoutController.php';
        });

        // Confirmar compra
        document.getElementById('btnConfirmarCompra').addEventListener('click', function() {
            const btn = this;
            const mensaje = document.getElementById('mensajeResultado');
            
            // Deshabilitar botón mientras se procesa
            btn.disabled = true;
            btn.textContent = 'Procesando...';
            
            // Enviar petición para procesar la compra
            fetch('../assets/app/controllers/CompraController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=procesar_compra'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    mensaje.style.display = 'block';
                    mensaje.style.background = '#e8f5e9';
                    mensaje.style.color = '#2e7d32';
                    mensaje.innerHTML = `
                        <strong>¡Compra realizada con éxito!</strong><br>
                        Has ganado ${data.puntos_generados} punto${data.puntos_generados != 1 ? 's' : ''}.<br>
                        Redirigiendo a la tienda...
                    `;
                    
                    // Redirigir después de 2 segundos
                    setTimeout(() => {
                        window.location.href = 'principal.php';
                    }, 2000);
                } else {
                    // Mostrar mensaje de error
                    mensaje.style.display = 'block';
                    mensaje.style.background = '#ffebee';
                    mensaje.style.color = '#c62828';
                    mensaje.innerHTML = `<strong> Error:</strong> ${data.message}`;
                    
                    // Rehabilitar botón
                    btn.disabled = false;
                    btn.textContent = 'Confirmar y Pagar';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mensaje.style.display = 'block';
                mensaje.style.background = '#ffebee';
                mensaje.style.color = '#c62828';
                mensaje.innerHTML = '<strong> Error al procesar la compra.</strong> Intenta nuevamente.';
                
                // Rehabilitar botón
                btn.disabled = false;
                btn.textContent = 'Confirmar y Pagar';
            });
        });
    </script>

</body>
</html>