/* =========================================================
   CLIENTES.JS
   Gestión dinámica del módulo Clientes (localStorage)
   ========================================================= */

/* =========================================================
   1. Obtener clientes desde localStorage
   ========================================================= */
function obtenerClientes() {
  let clientes = localStorage.getItem("clientes");

  if (!clientes) {
    // datos iniciales
    clientes = [
      {
        id: 1,
        nombre: "Carlos Hernandez",
        correo: "CarlosH@gmail.com",
        nivel: "Plata",
      },
      {
        id: 2,
        nombre: "Maria Lopez",
        correo: "maria@example.com",
        nivel: "Oro",
      },
    ];
    localStorage.setItem("clientes", JSON.stringify(clientes));
  } else {
    clientes = JSON.parse(clientes);
  }

  return clientes;
}

/* =========================================================
   2. Guardar clientes en localStorage
   ========================================================= */
function guardarClientes(lista) {
  localStorage.setItem("clientes", JSON.stringify(lista));
}

/* =========================================================
   3. Cargar lista en ClientesAdmin.php
   ========================================================= */
function cargarClientes() {
  const tabla = document.getElementById("clienteListado");
  if (!tabla) return; // Si no está en la vista clientes, no ejecuta

  const clientes = obtenerClientes();
  tabla.innerHTML = "";

  clientes.forEach((cliente) => {
    const tr = document.createElement("tr");

    tr.innerHTML = `
    <td>${cliente.id}</td>
    <td>${cliente.nombre}</td>
    <td>${cliente.nivel}</td>
    <td>${cliente.correo}</td>
    <td>
        <button class="btn-cliente-editar" onclick="irEditarCliente(${cliente.id})">Editar</button>
    </td>
    <td>
        <button class="btn-eliminar-cliente" onclick="eliminarCliente(${cliente.id})">Eliminar</button>
    </td>
`;

    tabla.appendChild(tr);
  });
}

/* =========================================================
   4. Ir a AgregarCliente.php
   ========================================================= */
function irAgregarCliente() {
  window.location.href = "AgregarCliente.php";
}

/* =========================================================
   5. Agregar un cliente
   ========================================================= */
function agregarCliente() {
  const nombre = document.getElementById("nuevoNombre").value.trim();
  const correo = document.getElementById("nuevoCorreo").value.trim();
  const pass = document.getElementById("nuevoPass").value;
  const pass2 = document.getElementById("nuevoPass2").value;

  if (nombre === "" || correo === "" || pass === "" || pass2 === "") {
    alert("Todos los campos son obligatorios");
    return;
  }

  if (pass !== pass2) {
    alert("Las contraseñas no coinciden");
    return;
  }

  let clientes = obtenerClientes();

  const nuevo = {
    id: clientes.length > 0 ? clientes[clientes.length - 1].id + 1 : 1,
    nombre,
    correo,
    nivel: "Bronce",
  };

  clientes.push(nuevo);
  guardarClientes(clientes);

  alert("Cliente agregado correctamente");
  window.location.href = "ClientesAdmin.php";
}

/* =========================================================
   6. Ir a EditarCliente.php
   ========================================================= */
function irEditarCliente(id) {
  window.location.href = `EditarCliente.php?id=${id}`;
}

/* =========================================================
   7. Cargar datos del cliente en EditarCliente.php
   ========================================================= */
function cargarEdicion() {
  const params = new URLSearchParams(window.location.search);
  const id = parseInt(params.get("id"));
  if (!id) return;

  const clientes = obtenerClientes();
  const cliente = clientes.find((c) => c.id === id);
  if (!cliente) return;

  // llenar inputs
  document.getElementById("editarNombre").value = cliente.nombre;
  document.getElementById("editarCorreo").value = cliente.correo;

  // llenar spans de la izquierda
  document.getElementById("nombreTexto").textContent = cliente.nombre;
  document.getElementById("correoTexto").textContent = cliente.correo;

  // llenar id oculto
  document.getElementById("clienteId").value = cliente.id;

  // datos extra
  document.getElementById("idClienteTexto").textContent = cliente.id;
  document.getElementById("nivelTexto").textContent = cliente.nivel;

  document.getElementById("sigNivelTexto").textContent =
    cliente.nivel === "Bronce"
      ? "Plata"
      : cliente.nivel === "Plata"
      ? "Oro"
      : "Diamante";

  document.getElementById("faltantesTexto").textContent = 1;
}


/* =========================================================
   8. Guardar cambios en cliente editado
   ========================================================= */
function guardarCambios() {
  const id = parseInt(document.getElementById("clienteId").value);
  const nombre = document.getElementById("editarNombre").value.trim();
  const correo = document.getElementById("editarCorreo").value.trim();

  if (nombre === "" || correo === "") {
    alert("No puedes dejar campos vacíos");
    return;
  }

  let clientes = obtenerClientes();
  let cliente = clientes.find((c) => c.id === id);

  cliente.nombre = nombre;
  cliente.correo = correo;

  guardarClientes(clientes);

  alert("Cambios guardados");
  window.location.href = "ClientesAdmin.php";
}

/* =========================================================
   9. Eliminar un cliente
   ========================================================= */
function eliminarCliente(id) {
  if (!confirm("¿Seguro que deseas eliminar este cliente?")) return;

  let clientes = obtenerClientes();
  clientes = clientes.filter((c) => c.id !== id);

  guardarClientes(clientes);
  cargarClientes(); // refresca tabla
}

/* =========================================================
   10. AUTO-EJECUCIONES SEGÚN LA PÁGINA
   ========================================================= */
document.addEventListener("DOMContentLoaded", () => {
  cargarClientes();
  cargarEdicion();
});
