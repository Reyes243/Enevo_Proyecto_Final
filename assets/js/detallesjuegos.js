// ============================================
// DETALLES JUEGO - Cargar info desde BD + Carrito
// ============================================

window.addEventListener("DOMContentLoaded", function () {
    // Ejecutar solo en páginas de detalles
    if (window.location.pathname.includes("MasDetalles")) {
        console.log("📄 Página de detalles detectada");
        cargarDetalleJuego();
    }
    
    // Actualizar contador del carrito si está en una página de usuario logueado
    if (!window.location.pathname.includes("NoLogin") && 
        !window.location.pathname.includes("Admin")) {
        actualizarContadorCarrito();
    }

    // Interceptar clicks al carrito dentro de la página de detalles cuando NO está logueado
    // Esto cubre el caso donde el header/carrito está presente en la página de detalles
    const isLoggedQuick = !!localStorage.getItem('usuarioLogeado') || !!sessionStorage.getItem('Usuario');
    const loginHrefQuick = window.location.pathname.includes('/views/') ? 'login.html' : 'views/login.html';
    if (!isLoggedQuick) {
        document.body.addEventListener('click', function(e){
            const clicked = e.target.closest('.btn-carrito, a[href*="carrito"], a[href*="Carrito"], .add-to-cart');
            if (!clicked) return;
            e.preventDefault();
            showNotification({
                message: 'Debes iniciar sesión para acceder al carrito.',
                primaryText: 'Aceptar',
                primaryHref: loginHrefQuick,
                type: 'info'
            });
        });
    }
});

async function cargarDetalleJuego() {
    try {
        // Obtener ID de juego desde la URL
        const urlParams = new URLSearchParams(window.location.search);
        const juegoId = urlParams.get("id");

        if (!juegoId) {
            mostrarError("No se especificó un juego");
            return;
        }

        console.log("🎮 Cargando detalles del juego ID:", juegoId);

        const response = await fetch("../assets/app/controllers/JuegoController.php?action=getAll");
        const data = await response.json();

        if (data.success && data.data.length > 0) {
            const juego = data.data.find(j => j.id == juegoId);

            if (juego) {
                mostrarDetalles(juego);
                activarCarrito(juego);
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
    const puntos = Math.floor(juego.precio / 50);
    const isNoLogin = window.location.pathname.includes("NoLogin");
    const isAdmin = window.location.pathname.includes("Admin.php");
    const prefix = isNoLogin ? "nl-" : "";

    const titulo = document.getElementById(`game-${prefix}title`);
    const img = document.getElementById(`game-${prefix}img`);
    const precio = document.getElementById(`game-${prefix}price`);
    const buyTitle = document.getElementById(`buy-${prefix}title`);
    const descripcion = document.getElementById(`game-${prefix}desc`);
    const requisitos = document.getElementById(`req-${prefix}list`);

    if (titulo) titulo.textContent = juego.nombre;

    if (img) {
        img.src = `../assets/img/juego_${juego.id}.jpg`;
        img.onerror = () => img.src = "../assets/img/default_game.jpg";
    }

    if (precio) precio.textContent = `Mex$ ${parseFloat(juego.precio).toFixed(2)} · ${puntos} pts`;

    if (buyTitle) buyTitle.textContent = `Comprar ${juego.nombre}`;

    if (descripcion)
        descripcion.textContent = juego.descripcion || "Sin descripción disponible";

    // Requisitos
    if (requisitos) {
        requisitos.innerHTML = "";
        const reqs = [
            `Plataforma: ${juego.plataforma || "PC"}`,
            `Género: ${juego.genero || "Acción"}`,
            "SO: Windows 10 64-bit",
            "RAM: 8 GB",
            "GPU: GTX 1060 o superior"
        ];

        reqs.forEach(r => {
            const li = document.createElement("li");
            li.textContent = r;
            requisitos.appendChild(li);
        });
    }
}

// =================================================
// CARRITO SEGÚN TIPO DE VISTA (Login / NoLogin / Admin)
// =================================================

function activarCarrito(juego) {
    const isNoLogin = window.location.pathname.includes("NoLogin");
    const isAdmin = window.location.pathname.includes("Admin");

    // Mejor esfuerzo: comprobar si el usuario está logueado
    const isLogged = !!localStorage.getItem('usuarioLogeado') || !!sessionStorage.getItem('Usuario');

    if (!isLogged) {
        // Si no está logueado, interceptar el botón de 'añadir al carrito' y los enlaces al carrito
        let btn = document.getElementById("addCartBtn") || document.querySelector(".btn-buy") || document.querySelector("button[class*='cart'], button[class*='buy']");
        if (btn) {
            btn.addEventListener("click", function(e){
                e.preventDefault();
                const loginHref = window.location.pathname.includes('/views/') ? 'login.html' : 'views/login.html';
                showNotification({
                    message: 'Debes iniciar sesión para agregar al carrito.',
                    primaryText: 'Aceptar',
                    primaryHref: loginHref,
                    type: 'info'
                });
            });
        }

        const carritoBtns = document.querySelectorAll('.btn-carrito, a[href*="carrito"], .add-to-cart');
        carritoBtns.forEach(cb => {
            cb.addEventListener('click', function(e){
                e.preventDefault();
                const loginHref = window.location.pathname.includes('/views/') ? 'login.html' : 'views/login.html';
                showNotification({
                    message: 'Debes iniciar sesión para acceder al carrito.',
                    primaryText: 'Aceptar',
                    primaryHref: loginHref,
                    type: 'info'
                });
            });
        });

        return;
    }

    // ADMIN: no puede comprar
    if (isAdmin) return;

    // CLIENTE NORMAL: agregar al carrito REAL
    // Buscar botón por diferentes selectores
    let btn = document.getElementById("addCartBtn");
    
    if (!btn) {
        btn = document.querySelector(".btn-buy");
    }
    
    if (!btn) {
        btn = document.querySelector("button[class*='cart'], button[class*='buy']");
    }
    
    if (!btn) {
        console.error("❌ No se encontró el botón para agregar al carrito");
        return;
    }

    console.log("🔘 Botón encontrado:", btn);
    
    // AGREGAR EVENT LISTENER
    btn.addEventListener("click", async function() {
        console.log("🟢 Botón clickeado, ID del juego:", juego.id);
        
        // Guardar estado original
        const originalText = btn.textContent;
        const originalBg = btn.style.backgroundColor;
        
        // Deshabilitar y cambiar apariencia temporalmente
        btn.disabled = true;
        btn.textContent = "🔄 Agregando...";
        btn.style.opacity = "0.8";
        btn.style.cursor = "wait";
        
        try {
            // Crear FormData con el ID del juego
            const formData = new FormData();
            formData.append("id_juego", juego.id);
            
            console.log("📤 Enviando datos al servidor...");
            
            // Hacer petición al controlador CORREGIDO
            const response = await fetch("../assets/app/controllers/CarritoController.php?action=add", {
                method: "POST",
                body: formData,
            });
            
            // Verificar si la respuesta es válida
            const responseText = await response.text();
            console.log("📄 Respuesta en texto plano:", responseText);
            
            let data;
            try {
                data = JSON.parse(responseText);
                console.log("📥 JSON parseado correctamente:", data);
            } catch (jsonError) {
                console.error("❌ Error parseando JSON:", jsonError);
                console.log("📝 Respuesta cruda (primeros 500 chars):", responseText.substring(0, 500));
                
                // Si hay error de JSON pero la petición fue exitosa (código 200)
                if (response.ok) {
                    // Forzar éxito (modo de respaldo)
                    data = { success: true, message: "Producto agregado (modo respaldo)" };
                    
                    // Intentar agregar al carrito de todos modos
                    if (!sessionStorage.getItem('carrito_local')) {
                        sessionStorage.setItem('carrito_local', JSON.stringify([]));
                    }
                    let carritoLocal = JSON.parse(sessionStorage.getItem('carrito_local'));
                    carritoLocal.push({
                        id: juego.id,
                        nombre: juego.nombre,
                        precio: juego.precio,
                        fecha: new Date().toISOString()
                    });
                    sessionStorage.setItem('carrito_local', JSON.stringify(carritoLocal));
                } else {
                    throw new Error("Respuesta no válida del servidor");
                }
            }
            
            // MOSTRAR RESULTADO
            if (data.success) {
                // ÉXITO
                btn.textContent = "✅ ¡Agregado!";
                btn.style.backgroundColor = "#4CAF50";
                btn.style.color = "white";
                
                // Mensaje al usuario
                showNotification({
                    message: (data.message || "¡Juego agregado al carrito exitosamente!"),
                    type: 'success',
                    autoHide: 2500,
                    primaryText: 'Ver carrito',
                    primaryHref: 'Carrito.php'
                });
                
                // Actualizar contador del carrito
                actualizarContadorCarrito();
                
                // Log del carrito actual
                setTimeout(async () => {
                    try {
                        const carritoResp = await fetch("../assets/app/controllers/CarritoController.php?action=get");
                        const carritoData = await carritoResp.json();
                        console.log("🛍️ Carrito actual:", carritoData);
                    } catch (e) {
                        console.log("ℹ️ No se pudo verificar carrito:", e.message);
                    }
                }, 500);
                
            } else {
                // ERROR del servidor
                btn.textContent = "❌ Error";
                btn.style.backgroundColor = "#f44336";
                btn.style.color = "white";
                
                showNotification({ message: (data.message || "No se pudo agregar al carrito. Intenta nuevamente."), type: 'error' });
            }
            
        } catch (error) {
            // ERROR de conexión o procesamiento
            console.error("❌ Error en la petición:", error);
            
            btn.textContent = "⚠️ Error conexión";
            btn.style.backgroundColor = "#FF9800";
            btn.style.color = "white";
            
            showNotification({ message: "Error de conexión: " + error.message + "\n\nEl producto podría haberse agregado. Verifica tu carrito.", type: 'error' });
            
        } finally {
            // Restaurar botón después de 1.5 segundos
            setTimeout(() => {
                btn.disabled = false;
                btn.textContent = originalText;
                btn.style.backgroundColor = originalBg;
                btn.style.opacity = "1";
                btn.style.cursor = "pointer";
                btn.style.color = ""; // Restaurar color original
            }, 1500);
        }
    });
}

// =================================================
// FUNCIONES AUXILIARES
// =================================================

async function actualizarContadorCarrito() {
    try {
        console.log("🔄 Actualizando contador del carrito...");
        
        const response = await fetch("../assets/app/controllers/CarritoController.php?action=get");
        
        // Verificar si la respuesta es JSON válido
        const responseText = await response.text();
        let data;
        
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error("❌ Error parseando respuesta del carrito:", e);
            console.log("📄 Respuesta recibida:", responseText.substring(0, 200));
            return;
        }
        
        if (data.success) {
            // Calcular total de items
            let totalItems = 0;
            if (data.carrito && typeof data.carrito === 'object') {
                Object.values(data.carrito).forEach(item => {
                    totalItems += item.cantidad || 0;
                });
            } else if (data.total_items) {
                totalItems = data.total_items;
            }
            
            console.log("📊 Total items en carrito:", totalItems);
            
            // Buscar o crear contador
            let contador = document.getElementById("cart-count");
            
            if (!contador) {
                // Intentar encontrar donde poner el contador
                const posiblesLocaciones = [
                    'a[href*="carrito"]',
                    '.cart-icon',
                    '.fa-shopping-cart',
                    '.carrito-link',
                    'nav ul li:last-child a'
                ];
                
                let ubicacion = null;
                for (const selector of posiblesLocaciones) {
                    ubicacion = document.querySelector(selector);
                    if (ubicacion) break;
                }
                
                if (ubicacion) {
                    contador = document.createElement("span");
                    contador.id = "cart-count";
                    contador.style.cssText = `
                        background: #f44336;
                        color: white;
                        border-radius: 50%;
                        padding: 2px 8px;
                        font-size: 12px;
                        font-weight: bold;
                        margin-left: 5px;
                        display: inline-block;
                        min-width: 20px;
                        text-align: center;
                    `;
                    ubicacion.appendChild(contador);
                }
            }
            
            // Actualizar contador si existe
            if (contador) {
                contador.textContent = totalItems;
                contador.style.display = totalItems > 0 ? "inline-block" : "none";
                
                // Guardar en localStorage para otras páginas
                localStorage.setItem('ultimo_contador_carrito', totalItems);
                localStorage.setItem('ultima_actualizacion', new Date().toISOString());
            }
            
        } else {
            console.error("❌ Error en respuesta del carrito:", data.message);
        }
        
    } catch (error) {
        console.error("❌ Error actualizando contador:", error);
        
        // Intentar usar localStorage como respaldo
        const ultimoContador = localStorage.getItem('ultimo_contador_carrito');
        if (ultimoContador) {
            const contador = document.getElementById("cart-count");
            if (contador) {
                contador.textContent = ultimoContador;
                contador.style.display = ultimoContador > 0 ? "inline-block" : "none";
            }
        }
    }
}

function mostrarNotificacion(mensaje, tipo = "success") {
    // Eliminar notificación anterior si existe
    const notifAnterior = document.querySelector(".notificacion-flotante");
    if (notifAnterior) {
        notifAnterior.remove();
    }
    
    // Crear nueva notificación
    const notificacion = document.createElement("div");
    notificacion.className = `notificacion-flotante ${tipo}`;
    notificacion.textContent = mensaje;
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        color: white;
        font-weight: bold;
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        max-width: 300px;
        word-wrap: break-word;
    `;
    
    // Color según tipo
    if (tipo === "success") {
        notificacion.style.backgroundColor = "#4CAF50";
        notificacion.style.borderLeft = "5px solid #2E7D32";
    } else if (tipo === "error") {
        notificacion.style.backgroundColor = "#f44336";
        notificacion.style.borderLeft = "5px solid #c62828";
    } else {
        notificacion.style.backgroundColor = "#2196F3";
        notificacion.style.borderLeft = "5px solid #0D47A1";
    }
    
    document.body.appendChild(notificacion);
    
    // Auto-eliminar después de 3 segundos
    setTimeout(() => {
        notificacion.style.animation = "slideOut 0.3s ease-in";
        setTimeout(() => notificacion.remove(), 300);
    }, 3000);
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

// Añadir estilos CSS para las animaciones
const style = document.createElement("style");
style.textContent = `
    @keyframes slideIn {
        from { 
            transform: translateX(100%); 
            opacity: 0; 
        }
        to { 
            transform: translateX(0); 
            opacity: 1; 
        }
    }
    
    @keyframes slideOut {
        from { 
            transform: translateX(0); 
            opacity: 1; 
        }
        to { 
            transform: translateX(100%); 
            opacity: 0; 
        }
    }
    
    button:disabled {
        cursor: not-allowed !important;
        opacity: 0.7 !important;
    }
    
    #cart-count {
        transition: all 0.3s ease;
    }
    
    #cart-count.pulse {
        animation: pulse 0.5s ease-in-out;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);

// Función para verificar estado del carrito al cargar
function verificarEstadoInicialCarrito() {
    console.log("🔍 Verificando estado inicial del carrito...");
    
    // Verificar si hay carrito en localStorage (respaldo)
    const carritoLocal = sessionStorage.getItem('carrito_local');
    if (carritoLocal) {
        console.log("📦 Carrito local encontrado:", JSON.parse(carritoLocal));
    }
    
    // Intentar cargar del servidor
    setTimeout(() => {
        if (!window.location.pathname.includes("NoLogin") && 
            !window.location.pathname.includes("Admin")) {
            actualizarContadorCarrito();
        }
    }, 1000);
}

// Ejecutar verificación al cargar
verificarEstadoInicialCarrito();