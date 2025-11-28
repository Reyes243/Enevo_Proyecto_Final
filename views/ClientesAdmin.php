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
    <main class="clientes-admin-container">

        <!-- Encabezado -->
        <div class="clientes-header">
            <h2 class="clientes-title">Bienvenido al apartado de Clientes</h2>

            <button class="btn-agregar-top">
                Agregar
            </button>
        </div>

        <!-- Contenedor oscuro -->
        <div class="tabla-wrapper">

            <table class="tabla-clientes-admin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Nivel</th>
                        <th>Correo</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- EJEMPLO (luego esto será dinámico) -->
                    <tr>
                        <td>1</td>
                        <td>Carlos</td>
                        <td>Plata</td>
                        <td><a href="mailto:carlos@gmail.com">carlos@gmail.com</a></td>
                        <td>
                            <button class="btn-editar">
                                Editar
                            </button>
                        </td>
                        <td>
                            <button class="btn-eliminar">
                                Eliminar
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td>Reyes</td>
                        <td>Oro</td>
                        <td><a href="mailto:reyes@gmail.com">reyes@gmail.com</a></td>
                        <td>
                            <button class="btn-editar">Editar</button>
                        </td>
                        <td>
                            <button class="btn-eliminar">Eliminar</button>
                        </td>
                    </tr>

                </tbody>
            </table>

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
</body>

</html>


