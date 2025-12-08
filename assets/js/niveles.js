/* ============================
   NIVELES - CONEXIÓN REAL BD
   ============================ */

const API = "../assets/app/controllers/NivelController.php";

/* ============================================================
   VALIDAR INPUTS NUMÉRICOS
============================================================ */
function validarNumeroInput(input) {
  input.addEventListener("input", function () {
    let val = parseInt(this.value);

    if (isNaN(val)) {
      this.value = "";
      return;
    }

    if (val < 1) {
      this.value = 1;
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  const puntos = document.getElementById("puntos");
  const compras = document.getElementById("compras");

  if (puntos) validarNumeroInput(puntos);
  if (compras) validarNumeroInput(compras);
});

/* ============================================================
    LISTAR NIVELES (ADMIN)
============================================================ */
async function cargarNivelesAdmin() {
  const container = document.getElementById("nivelesContainer");
  if (!container) return;

  const res = await fetch(`${API}?action=getAll`);
  const json = await res.json();

  if (!json.success) {
    container.innerHTML = "<p>Error cargando niveles.</p>";
    return;
  }

  const niveles = json.data;
  container.innerHTML = "";

  if (!niveles.length) {
    container.innerHTML = `
      <div style="text-align:center; padding:40px; color:#cfcfcf;">
          <p>No hay niveles registrados.</p>
      </div>`;
    return;
  }

  niveles.forEach((nivel) => {
    const card = document.createElement("div");
    card.className = "nivel-card-admin";
    card.dataset.nivelId = nivel.id;

    card.innerHTML = `
      <div class="nivel-card-header">
          <h3>${nivel.nombre}</h3>
      </div>

      <div class="nivel-card-info">
          <p>${nivel.beneficios}</p>
          <p style="margin-top:8px;color:#d6d6d6;font-size:0.9rem;">
              <strong>Puntos mínimos:</strong> ${nivel.puntos_minimos} · 
              <strong>Compras necesarias:</strong> ${nivel.compras_necesarias}
          </p>
      </div>

      <div class="nivel-card-buttons">
          <button class="btn-nivel btn-editar" onclick="editarNivel(${nivel.id})">Editar</button>
          <button class="btn-nivel btn-eliminar" onclick="eliminarNivel(${nivel.id}, '${nivel.nombre}')">Eliminar</button>
          <button class="btn-clientes" onclick="verClientes(${nivel.id}, '${nivel.nombre}')"> Ver clientes </button>
      </div>
    `;

    container.appendChild(card);
  });
}

/* ============================================================
    AGREGAR NIVEL
============================================================ */
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("formAgregarNivel");
  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const p = parseInt(document.getElementById("puntos").value);
      const c = parseInt(document.getElementById("compras").value);

      if (p < 1 || c < 1) {
        alert("Los valores de puntos y compras deben ser mayores a 0.");
        return;
      }

      const datos = new FormData();
      datos.append("action", "create");
      datos.append("nombre", document.getElementById("nombre").value);
      datos.append("puntos", p);
      datos.append("compras", c);
      datos.append("descripcion", document.getElementById("descripcion").value);

      const res = await fetch(API, { method: "POST", body: datos });
      const json = await res.json();

      if (json.success) {
        alert("Nivel creado correctamente.");
        window.location.href = "NivelesAdmin.php";
      } else {
        alert("Error: " + json.error);
      }
    });
  }
});

/* ============================================================
    CARGAR DATOS EN EDITAR NIVEL
============================================================ */
async function initEditarNivelPage() {
  const form = document.getElementById("formEditarNivel");
  if (!form) return;

  const id = document.getElementById("nivelId").value;

  const res = await fetch(`${API}?action=getById&id=${id}`);
  const json = await res.json();

  if (!json.success) {
    alert("No se pudo cargar el nivel.");
    return;
  }

  const nivel = json.data;

  document.getElementById("nombre").value = nivel.nombre;
  document.getElementById("puntos").value = nivel.puntos_minimos;
  document.getElementById("compras").value = nivel.compras_necesarias;
  document.getElementById("descripcion").value = nivel.beneficios;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const p = parseInt(document.getElementById("puntos").value);
    const c = parseInt(document.getElementById("compras").value);

    if (p < 1 || c < 1) {
      alert("Los valores de puntos y compras deben ser mayores a 0.");
      return;
    }

    const datos = new FormData();
    datos.append("action", "update");
    datos.append("nivelId", id);
    datos.append("nombre", document.getElementById("nombre").value);
    datos.append("puntos", p);
    datos.append("compras", c);
    datos.append("descripcion", document.getElementById("descripcion").value);

    const res = await fetch(API, { method: "POST", body: datos });
    const json = await res.json();

    if (json.success) {
      alert("Nivel actualizado correctamente.");
      window.location.href = "NivelesAdmin.php";
    } else {
      alert("Error: " + json.error);
    }
  });
}

/* ============================================================
    ELIMINAR NIVEL
============================================================ */
async function eliminarNivel(id, nombre) {
  if (!confirm(`¿Seguro que deseas eliminar "${nombre}"?`)) return;

  const datos = new FormData();
  datos.append("action", "delete");
  datos.append("id", id);

  const res = await fetch(API, { method: "POST", body: datos });
  const json = await res.json();

  if (json.success) {
    alert("Nivel eliminado.");
    cargarNivelesAdmin();
  } else {
    if (json.error === "has_clients") {
      alert("No puedes eliminar este nivel porque hay clientes asignados.");
    } else {
      alert("Error eliminando nivel.");
    }
  }
}

async function cargarNivelesClientes() {
  const cont = document.getElementById("nivelesClientesContainer");
  if (!cont) return;

  const res = await fetch(`${API}?action=getAll`);
  const json = await res.json();

  if (!json.success) return;

  const niveles = json.data;
  cont.innerHTML = "";

  niveles.forEach((nivel) => {
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

    cont.appendChild(card);
  });
}

document.addEventListener("DOMContentLoaded", () => {
  const path = window.location.pathname;

  if (path.includes("NivelesAdmin.php")) cargarNivelesAdmin();
  if (path.includes("EditarNivel.php")) initEditarNivelPage();
  if (path.includes("NivelesClientes.php")) cargarNivelesClientes();
});

function editarNivel(id) {
  window.location.href = `EditarNivel.php?id=${id}`;
}

function verClientes(id, nombre) {
  window.location.href = `ClientesNivel.php?id=${id}&nombre=${encodeURIComponent(
    nombre
  )}`;
}
