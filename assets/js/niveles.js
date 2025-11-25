/* ============================
   NIVELES - ADMIN (LocalStorage)
   ============================ */
(function () {
  const LS_KEY = 'niveles';

  // Datos simulados iniciales
  const nivelesIniciales = [
    { id: 1, nombre: "Plata", puntos: 10, compras: 0, descripcion: "Nivel inicial. Ganas 10 puntos por compra." },
    { id: 2, nombre: "Oro", puntos: 15, compras: 5, descripcion: "Se desbloquea tras 5 compras. Aquí recibes 15 puntos por compra y pronto disfrutarás de un 10% de descuento." },
    { id: 3, nombre: "Platino", puntos: 20, compras: 10, descripcion: "Lo alcanzas con 10 compras. Obtienes 20 puntos por compra y próximamente tendrás un 15% de descuento." }
  ];

  function readNiveles() {
    const raw = localStorage.getItem(LS_KEY);
    if (!raw) {
      // guardar iniciales
      localStorage.setItem(LS_KEY, JSON.stringify(nivelesIniciales));
      return [...nivelesIniciales];
    }
    try {
      return JSON.parse(raw) || [];
    } catch (e) {
      console.error('Error parseando niveles desde localStorage:', e);
      return [];
    }
  }

  function writeNiveles(niveles) {
    localStorage.setItem(LS_KEY, JSON.stringify(niveles));
  }

  // Cargar la vista principal (NivelesAdmin.php)
  function cargarNivelesAdmin() {
    const niveles = readNiveles();
    const container = document.getElementById('nivelesContainer');
    if (!container) return;

    container.innerHTML = '';

    if (!niveles || niveles.length === 0) {
      container.innerHTML = `
        <div style="text-align: center; padding: 40px; color: #cfcfcf;">
          <p>No hay niveles registrados. Haz clic en "Agregar" para crear uno.</p>
        </div>`;
      return;
    }

    // Crear tarjetas respetando tus clases CSS
    niveles.forEach(nivel => {
      const card = document.createElement('div');
      card.className = 'nivel-card-admin';
      card.dataset.nivelId = nivel.id;
      card.innerHTML = `
        <div class="nivel-card-header">
          <h3>${escapeHtml(nivel.nombre)}</h3>
        </div>
        <div class="nivel-card-info">
          <p>${escapeHtml(nivel.descripcion)}</p>
          <p style="margin-top:8px; color:#d6d6d6; font-size:0.9rem;">
            <strong>Puntos:</strong> ${Number(nivel.puntos)} · <strong>Compras:</strong> ${Number(nivel.compras)}
          </p>
        </div>
        <div class="nivel-card-buttons">
          <button class="btn-nivel btn-editar" data-action="editar" data-id="${nivel.id}">Editar</button>
          <button class="btn-nivel btn-clientes" data-action="clientes" data-nombre="${encodeURIComponent(nivel.nombre)}">Ver Clientes</button>
          <button class="btn-nivel btn-eliminar" data-action="eliminar" data-id="${nivel.id}" data-nombre="${escapeHtml(nivel.nombre)}">Eliminar</button>
        </div>
      `;
      container.appendChild(card);
    });

    // Delegación de eventos en el contenedor para evitar onclick inline
    container.addEventListener('click', nivelesContainerClickHandler);
    console.log('Niveles cargados:', niveles);
  }

  // Maneja clicks en botones dentro de #nivelesContainer
  function nivelesContainerClickHandler(e) {
    const btn = e.target.closest('button[data-action]');
    if (!btn) return;

    const action = btn.dataset.action;
    if (action === 'editar') {
      const id = parseInt(btn.dataset.id, 10);
      if (!isNaN(id)) editarNivelRedirect(id);
    } else if (action === 'clientes') {
      const nombre = decodeURIComponent(btn.dataset.nombre || '');
      verClientesRedirect(nombre);
    } else if (action === 'eliminar') {
      const id = parseInt(btn.dataset.id, 10);
      const nombre = btn.dataset.nombre || '';
      if (!isNaN(id)) eliminarNivelConfirm(id, nombre);
    }
  }

  // Redirige a EditarNivel.php con id
  function editarNivelRedirect(id) {
    window.location.href = `EditarNivel.php?id=${id}`;
  }

  // Redirige a ClientesNivel.php con nombre
  function verClientesRedirect(nombreNivel) {
    window.location.href = `ClientesNivel.php?nivel=${encodeURIComponent(nombreNivel)}`;
  }

  // Confirmar y eliminar
  function eliminarNivelConfirm(id, nombre) {
    if (!confirm(`¿Estás seguro de que deseas eliminar el nivel "${nombre}"?\n\nEsta acción no se puede deshacer.`)) return;
    const niveles = readNiveles().filter(n => n.id !== id);
    writeNiveles(niveles);
    alert('Nivel eliminado correctamente');
    // recargar vista
    cargarNivelesAdmin();
  }

  // Página AgregarNivel.php - manejo del formulario
  function initAgregarNivelPage() {
    const form = document.getElementById('formAgregarNivel');
    if (!form) return;
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const nombre = (document.getElementById('nombre') || {}).value || '';
      const puntos = parseInt((document.getElementById('puntos') || {}).value || '0', 10);
      const compras = parseInt((document.getElementById('compras') || {}).value || '0', 10);
      const descripcion = (document.getElementById('descripcion') || {}).value || '';

      if (!nombre.trim() || !descripcion.trim()) {
        alert('Por favor completa todos los campos');
        return;
      }
      if (isNaN(puntos) || puntos <= 0 || isNaN(compras) || compras < 0) {
        alert('Los puntos deben ser mayores a 0 y las compras no pueden ser negativas');
        return;
      }

      const niveles = readNiveles();
      const existe = niveles.some(n => n.nombre.trim().toLowerCase() === nombre.trim().toLowerCase());
      if (existe) {
        alert('Ya existe un nivel con ese nombre');
        return;
      }

      const nuevoId = niveles.length > 0 ? Math.max(...niveles.map(n => n.id)) + 1 : 1;
      const nuevoNivel = { id: nuevoId, nombre: nombre.trim(), puntos, compras, descripcion: descripcion.trim() };
      niveles.push(nuevoNivel);
      writeNiveles(niveles);
      alert('Nivel agregado correctamente');
      window.location.href = 'NivelesAdmin.php';
    });
  }

  // Página EditarNivel.php - cargar datos y guardar cambios
  function initEditarNivelPage() {
    const urlParams = new URLSearchParams(window.location.search);
    const nivelId = parseInt(urlParams.get('id'), 10);
    if (isNaN(nivelId)) {
      alert('ID de nivel no válido');
      window.location.href = 'NivelesAdmin.php';
      return;
    }

    // rellenar formulario
    const niveles = readNiveles();
    const nivel = niveles.find(n => n.id === nivelId);
    if (!nivel) {
      alert('Nivel no encontrado');
      window.location.href = 'NivelesAdmin.php';
      return;
    }

    const nombreEl = document.getElementById('nombre');
    const puntosEl = document.getElementById('puntos');
    const comprasEl = document.getElementById('compras');
    const descripcionEl = document.getElementById('descripcion');

    if (nombreEl) nombreEl.value = nivel.nombre;
    if (puntosEl) puntosEl.value = nivel.puntos;
    if (comprasEl) comprasEl.value = nivel.compras;
    if (descripcionEl) descripcionEl.value = nivel.descripcion;

    const form = document.getElementById('formEditarNivel');
    if (!form) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const nombre = (nombreEl.value || '').trim();
      const puntos = parseInt(puntosEl.value || '0', 10);
      const compras = parseInt(comprasEl.value || '0', 10);
      const descripcion = (descripcionEl.value || '').trim();

      if (!nombre || !descripcion) {
        alert('Por favor completa todos los campos');
        return;
      }
      if (isNaN(puntos) || puntos <= 0 || isNaN(compras) || compras < 0) {
        alert('Los puntos deben ser mayores a 0 y las compras no pueden ser negativas');
        return;
      }

      const nivelesActual = readNiveles();
      const existeNombre = nivelesActual.some(n => n.id !== nivelId && n.nombre.trim().toLowerCase() === nombre.toLowerCase());
      if (existeNombre) {
        alert('Ya existe otro nivel con ese nombre');
        return;
      }

      const idx = nivelesActual.findIndex(n => n.id === nivelId);
      if (idx === -1) {
        alert('No se encontró el nivel');
        return;
      }

      nivelesActual[idx] = { id: nivelId, nombre, puntos, compras, descripcion };
      writeNiveles(nivelesActual);
      alert('Nivel actualizado correctamente');
      window.location.href = 'NivelesAdmin.php';
    });
  }

  // Página ClientesNivel.php - cargar tabla (usa datos simulados clientesPorNivel)
  function initClientesNivelPage() {
    // datos simulados (puedes actualizarlos si quieres)
    const clientesPorNivel = {
      "Plata": [
          { id: 1, nombre: "Carlos Hernández", correo: "Carlos@gmail.com" },
          { id: 2, nombre: "María López", correo: "Maria@gmail.com" },
          { id: 3, nombre: "Juan Pérez", correo: "Juan@gmail.com" }
      ],
      "Oro": [
          { id: 7, nombre: "Pedro Sánchez", correo: "Pedro@gmail.com" },
          { id: 8, nombre: "Laura Jiménez", correo: "Laura@gmail.com" }
      ],
      "Platino": [
          { id: 11, nombre: "Sofia Ramírez", correo: "Sofia@gmail.com" }
      ]
    };

    const urlParams = new URLSearchParams(window.location.search);
    const nombreNivel = urlParams.get('nivel') || 'Plata';
    const displayEl = document.getElementById('nombreNivelDisplay');
    if (displayEl) displayEl.textContent = nombreNivel;

    const tbody = document.getElementById('tablaClientesBody');
    if (!tbody) return;

    const clientes = clientesPorNivel[nombreNivel] || [];
    tbody.innerHTML = '';

    if (clientes.length === 0) {
      tbody.innerHTML = `<tr><td colspan="3" style="text-align:center; padding:30px; color:#cfcfcf;">No hay clientes en este nivel</td></tr>`;
      return;
    }

    clientes.forEach(c => {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${escapeHtml(String(c.id))}</td><td>${escapeHtml(c.nombre)}</td><td>${escapeHtml(c.correo)}</td>`;
      tbody.appendChild(tr);
    });
  }

  // pequeños helpers
  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Inicialización por página (solo se ejecuta lo necesario)
  document.addEventListener('DOMContentLoaded', function () {
    const path = window.location.pathname;
    if (path.includes('NivelesAdmin.php')) {
      cargarNivelesAdmin();
    } else if (path.includes('AgregarNivel.php')) {
      initAgregarNivelPage();
    } else if (path.includes('EditarNivel.php')) {
      initEditarNivelPage();
    } else if (path.includes('ClientesNivel.php')) {
      initClientesNivelPage();
    }
  });

  // Mantener compatibilidad si en tu HTML hay llamadas globales (onclick)
  // Exponer solo lo necesario
  window.editarNivel = function(id) { editarNivelRedirect(id); };
  window.verClientes = function(nombre) { verClientesRedirect(nombre); };
  window.eliminarNivel = function(id, nombre) { eliminarNivelConfirm(id, nombre); };

})();
