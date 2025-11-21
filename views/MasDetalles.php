<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detalles del Juego</title>

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
            <a href="principal.html">Tienda</a>
            <a href="NivelesClientes.html">Niveles</a>
            <a href="Carrito.html">Carrito</a>
            <div class="user-section">
                <span class="user-link">Usuario</span>
                <button id="logoutBtn" class="logout-btn">
                    <img src="../assets/img/cerrar-sesion.png" alt="Cerrar sesión">
                </button>
            </div>
        </div>
    </nav>

   
    <main class="novedades" style="max-width: 760px;">
        <div class="details-card">

          
            <h1 id="game-title" class="details-title"></h1>

           
            <img id="game-img" class="details-hero" src="" alt="Juego">

            
            <div class="buy-row">
                <div class="buy-text">
                    <strong id="buy-title"></strong><br>
                    <small>Versión digital · Plataforma: PC</small>
                </div>

                <div class="buy-actions">
                    <div class="price-box" id="game-price"></div>
                    <button class="btn-buy" id="addCartBtn">Agregar al carrito</button>
                </div>
            </div>

           
            <div class="section">
                <h4>ACERCA DE ESTE JUEGO</h4>
                <p class="text-muted" id="game-desc"></p>
            </div>

            
            <div class="section requirements">
                <h4>REQUISITOS DEL SISTEMA</h4>
                <ul id="req-list"></ul>
            </div>
        </div>
    </main>

   
    <footer class="site-footer" style="margin-top:20px;">
        <div class="footer-inner">
            <div class="footer-logo">
                <img src="../assets/img/LogoEnevo.png" alt="Enevo Logo">
                <p>© 2025 – Todos los derechos reservados</p>
            </div>
        </div>
    </footer>

  
    <script src="../assets/js/main.js"></script>

</body>
</html>

