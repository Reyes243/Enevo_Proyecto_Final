// /* =========================================================
//    CLIENTES.JS
//    Gestión dinámica del módulo Clientes (localStorage)
//    ========================================================= */

// /* =========================================================
//    1. Obtener clientes desde localStorage
//    ========================================================= */
// function obtenerClientes() {
//   let clientes = localStorage.getItem("clientes");

//   if (!clientes) {
//     // datos iniciales
//     clientes = [
//       {
//         id: 1,
//         nombre: "Carlos Hernandez",
//         correo: "CarlosH@gmail.com",
//         nivel: "Plata",
//       },
//       {
//         id: 2,
//         nombre: "Maria Lopez",
//         correo: "maria@example.com",
//         nivel: "Oro",
//       },
//     ];
//     localStorage.setItem("clientes", JSON.stringify(clientes));
//   } else {
//     clientes = JSON.parse(clientes);
//   }

//   return clientes;
// }

// /* =========================================================
//    2. Guardar clientes en localStorage
//    ========================================================= */
// function guardarClientes(lista) {
//   localStorage.setItem("clientes", JSON.stringify(lista));
// }

// /* =========================================================
//    3. Cargar lista en ClientesAdmin.php
//    ========================================================= */
// function cargarClientes() {
//   const tabla = document.getElementById("clienteListado");
//   if (!tabla) return; // Si no está en la vista clientes, no ejecuta

//   const clientes = obtenerClientes();
//   tabla.innerHTML = "";

//   clientes.forEach((cliente) => {
//     const tr = document.createElement("tr");

//     tr.innerHTML = `
//     <td>${cliente.id}</td>
//     <td>${cliente.nombre}</td>
//     <td>${cliente.nivel}</td>
//     <td>${cliente.correo}</td>
//     <td>
//         <button class="btn-cliente-editar" onclick="irEditarCliente(${cliente.id})">Editar</button>
//     </td>
//     <td>
//         <button class="btn-eliminar-cliente" onclick="eliminarCliente(${cliente.id})">Eliminar</button>
//     </td>
// `;

//     tabla.appendChild(tr);
//   });
// }

// /* =========================================================
//    4. Ir a AgregarCliente.php
//    ========================================================= */
// function irAgregarCliente() {
//   window.location.href = "AgregarCliente.php";
// }

// /* =========================================================
//    5. Agregar un cliente
//    ========================================================= */
// function agregarCliente() {
//   const nombre = document.getElementById("nuevoNombre").value.trim();
//   const correo = document.getElementById("nuevoCorreo").value.trim();
//   const pass = document.getElementById("nuevoPass").value;
//   const pass2 = document.getElementById("nuevoPass2").value;

//   if (nombre === "" || correo === "" || pass === "" || pass2 === "") {
//     alert("Todos los campos son obligatorios");
//     return;
//   }

//   if (pass !== pass2) {
//     alert("Las contraseñas no coinciden");
//     return;
//   }

//   let clientes = obtenerClientes();

//   const nuevo = {
//     id: clientes.length > 0 ? clientes[clientes.length - 1].id + 1 : 1,
//     nombre,
//     correo,
//     nivel: "Bronce",
//   };

//   clientes.push(nuevo);
//   guardarClientes(clientes);

//   alert("Cliente agregado correctamente");
//   window.location.href = "ClientesAdmin.php";
// }

// /* =========================================================
//    6. Ir a EditarCliente.php
//    ========================================================= */
// function irEditarCliente(id) {
//   window.location.href = `EditarCliente.php?id=${id}`;
// }

// /* =========================================================
//    7. Cargar datos del cliente en EditarCliente.php
//    ========================================================= */
// function cargarEdicion() {
//   const params = new URLSearchParams(window.location.search);
//   const id = parseInt(params.get("id"));
//   if (!id) return;

//   const clientes = obtenerClientes();
//   const cliente = clientes.find((c) => c.id === id);
//   if (!cliente) return;

//   // llenar inputs
//   document.getElementById("editarNombre").value = cliente.nombre;
//   document.getElementById("editarCorreo").value = cliente.correo;

//   // llenar spans de la izquierda
//   document.getElementById("nombreTexto").textContent = cliente.nombre;
//   document.getElementById("correoTexto").textContent = cliente.correo;

//   // llenar id oculto
//   document.getElementById("clienteId").value = cliente.id;

//   // datos extra
//   document.getElementById("idClienteTexto").textContent = cliente.id;
//   document.getElementById("nivelTexto").textContent = cliente.nivel;

//   document.getElementById("sigNivelTexto").textContent =
//     cliente.nivel === "Bronce"
//       ? "Plata"
//       : cliente.nivel === "Plata"
//       ? "Oro"
//       : "Diamante";

//   document.getElementById("faltantesTexto").textContent = 1;
// }

// /* =========================================================
//    8. Guardar cambios en cliente editado
//    ========================================================= */
// function guardarCambios() {
//   const id = parseInt(document.getElementById("clienteId").value);
//   const nombre = document.getElementById("editarNombre").value.trim();
//   const correo = document.getElementById("editarCorreo").value.trim();

//   if (nombre === "" || correo === "") {
//     alert("No puedes dejar campos vacíos");
//     return;
//   }

//   let clientes = obtenerClientes();
//   let cliente = clientes.find((c) => c.id === id);

//   cliente.nombre = nombre;
//   cliente.correo = correo;

//   guardarClientes(clientes);

//   alert("Cambios guardados");
//   window.location.href = "ClientesAdmin.php";
// }

// /* =========================================================
//    9. Eliminar un cliente
//    ========================================================= */
// function eliminarCliente(id) {
//   if (!confirm("¿Seguro que deseas eliminar este cliente?")) return;

//   let clientes = obtenerClientes();
//   clientes = clientes.filter((c) => c.id !== id);

//   guardarClientes(clientes);
//   cargarClientes(); // refresca tabla
// }

// /* =========================================================
//    10. AUTO-EJECUCIONES SEGÚN LA PÁGINA
//    ========================================================= */
// document.addEventListener("DOMContentLoaded", () => {
//   cargarClientes();
//   cargarEdicion();
// });

/**
 * clientes.js
 * Manejo de operaciones CRUD de clientes desde el frontend
 */

/**
 * clientes.js
 * Manejo de operaciones CRUD de clientes desde el frontend
 */

// URL del controlador
const CLIENTES_API = "../assets/app/controllers/ClientesController.php";

// ============================================================================
// LISTAR CLIENTES (ClientesAdmin.php)
// ============================================================================
function cargarClientes() {
  fetch(`${CLIENTES_API}?accion=listar`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        mostrarClientes(data.clientes);
      } else {
        console.error("Error al cargar clientes:", data.message);
      }
    })
    .catch((error) => {
      console.error("Error en la petición:", error);
    });
}

function mostrarClientes(clientes) {
  const tbody = document.getElementById("clienteListado");

  if (!tbody) return;

  tbody.innerHTML = "";

  if (clientes.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="6" style="text-align:center;">No hay clientes registrados</td></tr>';
    return;
  }

  clientes.forEach((cliente) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
            <td>${cliente.id}</td>
            <td>${cliente.nombre}</td>
            <td>${cliente.nivel_nombre}</td>
            <td>${cliente.email}</td>
            <td>
                <button class="btn-cliente-editar" onclick="irEditarCliente(${cliente.id})">Editar</button>
            </td>
            <td>
                <button class="btn-eliminar-cliente" onclick="confirmarEliminar(${cliente.id}, '${cliente.nombre}')">Eliminar</button>
            </td>
        `;
    tbody.appendChild(tr);
  });
}

// ============================================================================
// AGREGAR CLIENTE (AgregarCliente.php)
// ============================================================================
function agregarCliente() {
  const nombre = document.getElementById("nuevoNombre").value.trim();
  const email = document.getElementById("nuevoCorreo").value.trim();
  const password = document.getElementById("nuevoPass").value;
  const password2 = document.getElementById("nuevoPass2").value;

  // Validaciones
  if (!nombre || !email || !password || !password2) {
    showNotification({ message: "Todos los campos son obligatorios", type: 'error' });
    return;
  }

  if (password !== password2) {
    showNotification({ message: "Las contraseñas no coinciden", type: 'error' });
    return;
  }

  if (password.length < 6) {
    showNotification({ message: "La contraseña debe tener al menos 6 caracteres", type: 'error' });
    return;
  }

  // Enviar datos
  const formData = new FormData();
  formData.append("accion", "crear");
  formData.append("nombre", nombre);
  formData.append("email", email);
  formData.append("password", password);

  fetch(CLIENTES_API, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification({ message: "Cliente agregado exitosamente", type: 'success', autoHide: 1500 });
        setTimeout(() => { window.location.href = "ClientesAdmin.php"; }, 1400);
      } else {
        showNotification({ message: "Error: " + data.message, type: 'error' });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error al agregar el cliente");
    });
}

// ============================================================================
// EDITAR CLIENTE (EditarCliente.php)
// ============================================================================
function cargarDatosCliente() {
  // Obtener ID del cliente de la URL
  const urlParams = new URLSearchParams(window.location.search);
  const clienteId = urlParams.get("id");

  if (!clienteId) {
    showNotification({ message: "ID de cliente no válido", type: 'error' });
    setTimeout(() => { window.location.href = "ClientesAdmin.php"; }, 1200);
    return;
  }

  fetch(`${CLIENTES_API}?accion=obtener&id=${clienteId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        mostrarDatosClienteParaEditar(data);
      } else {
        showNotification({ message: "Error al cargar datos del cliente", type: 'error' });
        setTimeout(() => { window.location.href = "ClientesAdmin.php"; }, 1200);
      }
    })
      .catch((error) => {
      console.error("Error:", error);
      showNotification({ message: "Error al cargar datos del cliente", type: 'error' });
    });
}

function mostrarDatosClienteParaEditar(data) {
  const cliente = data.cliente;
  const siguienteNivel = data.siguiente_nivel;
  const comprasFaltantes = data.compras_faltantes;

  // Actualizar textos informativos
  document.getElementById("idClienteTexto").textContent = cliente.id;
  document.getElementById("nivelTexto").textContent = cliente.nivel_nombre;

  if (siguienteNivel) {
    document.getElementById("sigNivelTexto").textContent =
      siguienteNivel.nombre;
    document.getElementById("faltantesTexto").textContent = comprasFaltantes;
  } else {
    document.getElementById("sigNivelTexto").textContent =
      "Nivel máximo alcanzado";
    document.getElementById("faltantesTexto").textContent = "0";
  }

  // Llenar inputs de edición
  document.getElementById("clienteId").value = cliente.id;
  document.getElementById("editarNombre").value = cliente.nombre;
  document.getElementById("editarCorreo").value = cliente.email;
}

function guardarCambios() {
  const id = document.getElementById("clienteId").value;
  const nombre = document.getElementById("editarNombre").value.trim();
  const email = document.getElementById("editarCorreo").value.trim();

  if (!nombre || !email) {
    showNotification({ message: "El nombre y el correo son obligatorios", type: 'error' });
    return;
  }

  const formData = new FormData();
  formData.append("accion", "actualizar");
  formData.append("id", id);
  formData.append("nombre", nombre);
  formData.append("email", email);

  fetch(CLIENTES_API, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification({ message: "Cliente actualizado exitosamente", type: 'success', autoHide: 1400 });
        setTimeout(() => { window.location.href = "ClientesAdmin.php"; }, 1300);
      } else {
        showNotification({ message: "Error: " + data.message, type: 'error' });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification({ message: "Error al actualizar el cliente", type: 'error' });
    });
}

// ============================================================================
// ELIMINAR CLIENTE
// ============================================================================
function confirmarEliminar(id, nombre) {
  showNotification({
    message: `¿Está seguro de eliminar al cliente "${nombre}"?\n\nEsta acción también eliminará:\n- Su usuario asociado\n- Todo su historial de compras\n\n¡Esta acción no se puede deshacer!`,
    type: 'info',
    primaryText: 'Eliminar',
    onPrimary: function(){ eliminarCliente(id); },
    secondaryText: 'Cancelar'
  });
}

function eliminarCliente(id) {
  const formData = new FormData();
  formData.append("accion", "eliminar");
  formData.append("id", id);

  fetch(CLIENTES_API, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification({ message: "Cliente eliminado exitosamente", type: 'success', autoHide: 1200 });
        cargarClientes(); // Recargar la lista
      } else {
        showNotification({ message: "Error: " + data.message, type: 'error' });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification({ message: "Error al eliminar el cliente", type: 'error' });
    });
}

// ============================================================================
// NAVEGACIÓN
// ============================================================================
function irAgregarCliente() {
  window.location.href = "AgregarCliente.php";
}

function irEditarCliente(id) {
  window.location.href = `EditarCliente.php?id=${id}`;
}

// ============================================================================
// INICIALIZACIÓN
// ============================================================================
document.addEventListener("DOMContentLoaded", function () {
  // Detectar en qué página estamos y cargar lo necesario
  const path = window.location.pathname;

  if (path.includes("ClientesAdmin.php")) {
    cargarClientes();
  } else if (path.includes("EditarCliente.php")) {
    cargarDatosCliente();
  }
});
