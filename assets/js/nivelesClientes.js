(function () {
    const LS_KEY = 'niveles';

    function readNiveles() {
        try {
            const raw = localStorage.getItem(LS_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            console.error("Error leyendo niveles:", e);
            return [];
        }
    }

    function cargarNivelesClientes() {
        const niveles = readNiveles();
        const container = document.getElementById("nivelesClientesContainer");
        if (!container) return;

        container.innerHTML = "";

        if (niveles.length === 0) {
            container.innerHTML = `
                <p style="text-align:center; padding:20px; color:#cfcfcf;">
                    No hay niveles registrados en este momento.
                </p>
            `;
            return;
        }

        niveles.forEach(n => {
            const card = document.createElement("div");
            card.className = "nivel-card";

            card.innerHTML = `
                <h3>${n.nombre}</h3>
                <p>${n.descripcion}</p>
                <p style="margin-top:8px; color:#d6d6d6; font-size:0.9rem;">
                    <strong>Puntos por compra:</strong> ${n.puntos} · 
                    <strong>Compras necesarias:</strong> ${n.compras}
                </p>
            `;

            container.appendChild(card);
        });
    }

    document.addEventListener("DOMContentLoaded", cargarNivelesClientes);
})();
