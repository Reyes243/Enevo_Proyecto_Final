<?php
session_start();

// Verificar si el usuario está logueado
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
  <title>PrincipalUser</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../assets/style/main.css" />
</head>

<body>
  <nav class="navbar">
    <div class="logo">
      <img src="../assets/img/LogoEnevo2.png" alt="Logo Enevo" />
      <span class="brand">Enevo</span>
    </div>

    <div class="nav-links">
      <a href="#" class="active">Tienda</a>
      <a href="NivelesClientes.php">Niveles</a>
      <a href="Carrito.php">Carrito</a>


      <div class="user-section">
        <a href="Perfil.php" class="user-link"><?php echo htmlspecialchars($userName); ?></a>
        <button id="logoutBtn" class="logout-btn">
          <img src="../assets/img/cerrar-sesion.png" alt="Cerrar sesión" />
        </button>
      </div>
    </div>
  </nav>
  <main>
    <section class="hero">
      <h1>Dark Souls</h1>
      <img src="../assets/img/DarkSoulsGIF.gif" alt="Dark Souls Banner" />
    </section>

    <section class="novedades">
      <div class="novedades-titulo">
        <h2>Novedades Destacadas</h2>
      </div>

      <div class="card">
        <img
          src="../assets/img/DarksoulsRemastered.jpg"
          alt="Dark Souls Remastered" />
        <div class="info">
          <h3>Dark Souls: Remastered</h3>
          <p class="precio"><span>Desde 549 mx</span><br />10 Puntos</p>
          <button class="btn-mas-detalles" data-juego="darksouls">
            Más detalles
          </button>
        </div>
      </div>

      <div class="card">
        <img src="../assets/img/Logo33.png" alt="Clair Obscur: Expedition 33" />
        <div class="info">
          <h3>Clair Obscur: Expedition 33</h3>
          <p class="precio"><span>Desde 710 mx</span><br />10 Puntos</p>
          <button class="btn-mas-detalles" data-juego="expedition33">
            Más detalles
          </button>
        </div>
      </div>

      <div class="card">
        <img src="../assets/img/LogoRisk.png" alt="Risk of Rain 2" />
        <div class="info">
          <h3>Risk of Rain 2</h3>
          <p class="precio"><span>Desde 233 mx</span><br />10 Puntos</p>
          <button class="btn-mas-detalles" data-juego="risk2">
            Más detalles
          </button>
        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="footer-inner">
      <div class="footer-logo">
        <img src="../assets/img/LogoEnevo.png" alt="Enevo Logo" />
        <p>© 2025 – Todos los derechos reservados</p>
      </div>
    </div>
  </footer>

  <script src="../assets/js/main.js"></script>
</body>

</html>