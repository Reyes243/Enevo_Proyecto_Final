// ============================================
// JUEGOS.JS - Manejo de juegos dinámicos
// ============================================

// Esperar a que el DOM esté completamente cargado
window.addEventListener("DOMContentLoaded", function () {
  console.log("🎮 Cargando juegos desde la base de datos...");
  cargarJuegos();
});

async function cargarJuegos() {
  try {
    // Determinar la ruta correcta según la ubicación del archivo
    const isIndexPage =
      window.location.pathname.endsWith("index.html") ||
      window.location.pathname.endsWith("/");

    // Ruta corregida: app está dentro de assets
    const rutaController = isIndexPage
      ? "assets/app/controllers/JuegoController.php?action=getAll"
      : "../assets/app/controllers/JuegoController.php?action=getAll";

    console.log(" Ruta del fetch:", rutaController);

    const response = await fetch(rutaController);
    const data = await response.json();

    console.log("Juegos cargados:", data);

    if (data.success && data.data.length > 0) {
      mostrarJuegos(data.data);
    } else {
      mostrarMensaje("No hay juegos disponibles");
    }
  } catch (error) {
    console.error("Error al cargar juegos:", error);
    mostrarMensaje("Error al cargar los juegos");
  }
}

function mostrarJuegos(juegos) {
  const novedadesSection = document.querySelector(".novedades");

  if (!novedadesSection) {
    console.error("No se encontró la sección .novedades");
    return;
  }

  // Limpiar las cards existentes (mantener solo el título)
  const cardsExistentes = novedadesSection.querySelectorAll(".card");
  cardsExistentes.forEach((card) => card.remove());

  // Determinar la ruta de imágenes según la página
  const isIndexPage =
    window.location.pathname.endsWith("index.html") ||
    window.location.pathname.endsWith("/");
  const rutaImg = isIndexPage ? "assets/img/" : "../assets/img/";

  // Crear una card por cada juego
  juegos.forEach((juego) => {
    const puntos = Math.floor(juego.precio / 50); // 1 punto por cada $50

    const card = document.createElement("div");
    card.className = "card";

    card.innerHTML = `
            <img 
                src="${rutaImg}juego_${juego.id}.jpg" 
                alt="${escapeHtml(juego.nombre)}"
                onerror="this.src='${rutaImg}default_game.jpg'" 
            />
            <div class="info">
                <h3>${escapeHtml(juego.nombre)}</h3>
                <p class="precio">
                    <span>Desde ${parseFloat(juego.precio).toFixed(
                      2
                    )} mx</span><br>
                    ${puntos} Puntos
                </p>
                <button type="button" class="btn-mas-detalles" data-juego-id="${
                  juego.id
                }">
                    Más detalles
                </button>
            </div>
        `;

    novedadesSection.appendChild(card);
  });

  // Agregar eventos a los botones de "Más detalles"
  agregarEventosDetalles();
}

function agregarEventosDetalles() {
  const botones = document.querySelectorAll(".btn-mas-detalles");

  botones.forEach((btn) => {
    btn.addEventListener("click", function () {
      const juegoId = this.getAttribute("data-juego-id");
      // Siempre permitir acceder a la página de detalles; la página de detalles
      // manejará si el usuario está logueado o no (mostrando notificaciones al intentar añadir)
      redirigirADetalles(juegoId);
    });
  });
}

function redirigirADetalles(juegoId) {
  const pathname = window.location.pathname;

  // Detectar en qué página estamos
  if (pathname.includes("principal.php")) {
    // Vista de cliente
    window.location.href = `MasDetalles.php?id=${juegoId}`;
  } else if (pathname.includes("principalAdmin.php")) {
    // Vista de admin
    window.location.href = `MasDetallesAdmin.php?id=${juegoId}`;
  } else {
    // index.html (sin login)
    window.location.href = `views/MasDetallesNoLogin.html?id=${juegoId}`;
  }
}

function mostrarMensaje(mensaje) {
  const novedadesSection = document.querySelector(".novedades");
  if (novedadesSection) {
    const p = document.createElement("p");
    p.style.textAlign = "center";
    p.style.color = "#999";
    p.style.padding = "20px";
    p.style.fontSize = "16px";
    p.textContent = mensaje;
    novedadesSection.appendChild(p);
  }
}

// Función para escapar HTML y evitar XSS
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}
