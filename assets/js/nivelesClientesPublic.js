document.addEventListener("DOMContentLoaded", cargarNivelesPublicos);

async function cargarNivelesPublicos() {
  const contenedor = document.getElementById("nivelesClientesContainer");
  if (!contenedor) return;

  try {
    const res = await fetch(
      "../assets/app/controllers/NivelController.php?action=getAll"
    );

    const json = await res.json();

    contenedor.innerHTML = "";

    if (!json.success || !json.data.length) {
      contenedor.innerHTML = `
        <p style="text-align:center; padding:40px; color:#ccc;">
          No hay niveles disponibles por el momento.
        </p>`;
      return;
    }

    json.data.forEach((nivel) => {
      const card = document.createElement("div");
      card.className = "nivel-card";

      card.innerHTML = `
        <h3>${nivel.nombre}</h3>
        <p>${nivel.beneficios}</p>
        <p style="margin-top:8px;color:#d6d6d6;font-size:0.9rem;">
          <strong>Puntos mínimos:</strong> ${nivel.puntos_minimos} · 
          <strong>Compras necesarias:</strong> ${nivel.compras_necesarias}
        </p>
      `;

      contenedor.appendChild(card);
    });
  } catch (error) {
    console.error("Error cargando niveles:", error);
  }
}
