// ============================================
// DETALLES JUEGO - Cargar info desde BD
// ============================================

window.addEventListener("DOMContentLoaded", function () {
  // Solo ejecutar en páginas de detalles
  if (window.location.pathname.includes("MasDetalles")) {
    console.log("📄 Página de detalles detectada");
    cargarDetalleJuego();
  }
});

async function cargarDetalleJuego() {
  try {
    // Obtener el ID del juego desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    const juegoId = urlParams.get("id");

    if (!juegoId) {
      console.error("No se especificó ID de juego");
      mostrarError("No se especificó un juego");
      return;
    }

    console.log("🎮 Cargando detalles del juego ID:", juegoId);

    // Determinar la ruta correcta
    const isNoLogin = window.location.pathname.includes("NoLogin");
    const rutaController = isNoLogin
      ? "../assets/app/controllers/JuegoController.php?action=getAll"
      : "../assets/app/controllers/JuegoController.php?action=getAll";

    const response = await fetch(rutaController);
    const data = await response.json();

    console.log("📦 Respuesta del servidor:", data);

    if (data.success && data.data.length > 0) {
      // Buscar el juego específico
      const juego = data.data.find((j) => j.id == juegoId);

      if (juego) {
        mostrarDetalles(juego);
      } else {
        mostrarError("Juego no encontrado");
      }
    } else {
      mostrarError("No se pudieron cargar los detalles");
    }
  } catch (error) {
    console.error("Error al cargar detalles:", error);
    mostrarError("Error al cargar la información del juego");
  }
}

function mostrarDetalles(juego) {
  console.log("✅ Mostrando detalles:", juego);

  const puntos = Math.floor(juego.precio / 50);

  // Detectar si es NoLogin o Login
  const isNoLogin = window.location.pathname.includes("NoLogin");
  const prefix = isNoLogin ? "nl-" : "";

  // Actualizar elementos
  const titulo = document.getElementById(`game-${prefix}title`);
  const img = document.getElementById(`game-${prefix}img`);
  const precio = document.getElementById(`game-${prefix}price`);
  const buyTitle = document.getElementById(`buy-${prefix}title`);
  const descripcion = document.getElementById(`game-${prefix}desc`);
  const requisitos = document.getElementById(`req-${prefix}list`);

  if (titulo) titulo.textContent = juego.nombre;
  if (img) img.src = `../assets/img/juego_${juego.id}.jpg`;
  if (img)
    img.onerror = function () {
      this.src = "../assets/img/default_game.jpg";
    };
  if (precio)
    precio.textContent = `Mex$ ${parseFloat(juego.precio).toFixed(
      2
    )} o ${puntos} Puntos`;
  if (buyTitle) buyTitle.textContent = `Comprar ${juego.nombre}`;
  if (descripcion)
    descripcion.textContent = juego.descripcion || "Sin descripción disponible";

  // Requisitos - Por ahora mostramos genéricos, después puedes agregarlos a la BD
  if (requisitos) {
    requisitos.innerHTML = "";
    const reqsGenericos = [
      `Plataforma: ${juego.plataforma || "Multi-plataforma"}`,
      `Género: ${juego.genero || "Acción"}`,
      "SO: Windows 10 64-bit",
      "RAM: 8 GB",
      "GPU: GTX 1060 o superior",
    ];

    reqsGenericos.forEach((req) => {
      const li = document.createElement("li");
      li.textContent = req;
      requisitos.appendChild(li);
    });
  }
}

function mostrarError(mensaje) {
  const main = document.querySelector("main");
  if (main) {
    main.innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <h2 style="color: #f44;">❌ ${mensaje}</h2>
                <p>
                    <a href="../index.html" style="color: #00a8e8;">← Volver a la tienda</a>
                </p>
            </div>
        `;
  }
}
