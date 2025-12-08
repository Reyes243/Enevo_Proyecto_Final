document.addEventListener("DOMContentLoaded", cargarClientesPorNivel);

async function cargarClientesPorNivel() {
  const params = new URLSearchParams(window.location.search);
  const nivelId = params.get("id");

  const tbody = document.getElementById("tablaClientesBody");
  if (!tbody) return;

  const res = await fetch(
    `../assets/app/controllers/NivelController.php?action=clientesByNivel&id=${nivelId}`
  );

  const json = await res.json();

  tbody.innerHTML = "";

  if (!json.success || !json.data.length) {
    tbody.innerHTML = `
            <tr>
                <td colspan="3" style="text-align:center; padding:30px; color:#ccc;">
                    No hay clientes en este nivel.
                </td>
            </tr>
        `;
    return;
  }

  json.data.forEach((cliente) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
            <td>${cliente.id}</td>
            <td>${cliente.nombre}</td>
            <td>${cliente.email}</td>
        `;
    tbody.appendChild(tr);
  });
}
