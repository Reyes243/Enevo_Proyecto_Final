document.addEventListener("DOMContentLoaded", function () {
  // ===========================================================
  //       MOSTRAR ERRORES DE PHP EN REGISTRO Y LOGIN
  // ===========================================================
  const urlParams = new URLSearchParams(window.location.search);
  const error = urlParams.get("error");
  const registered = urlParams.get("registered");

  // Mensajes para errores del servidor
  const errorMessages = {
    // Errores de registro
    empty: "Todos los campos son obligatorios",
    invalid_email: "El formato del correo no es válido",
    weak_password: "La contraseña debe tener al menos 6 caracteres",
    password_mismatch: "Las contraseñas no coinciden",
    email_exists: "⚠️ Este correo electrónico ya está registrado",
    username_exists: "⚠️ Este nombre de usuario ya existe",
    duplicate_data: "⚠️ El correo o nombre de usuario ya están en uso",
    database: "Error de base de datos. Intenta nuevamente",
    register_failed: "No se pudo completar el registro",

    // Errores de login
    login: "Correo o contraseña incorrectos",

    // Errores generales
    server: "Error del servidor. Intenta más tarde",
    connection: "No se pudo conectar a la base de datos",
    invalid_action: "Acción no válida",
  };

  // Mostrar mensaje de error si existe
  if (error && errorMessages[error]) {
    // Crear elemento de alerta
    const alertDiv = document.createElement("div");
    alertDiv.className = "alert-error";
    alertDiv.innerHTML = `
      <span>${errorMessages[error]}</span>
      <button class="alert-close">&times;</button>
    `;

    // Insertar al inicio del body o dentro de .login-box o .register-box
    const loginBox =
      document.querySelector(".login-box") ||
      document.querySelector(".register-box");
    if (loginBox) {
      loginBox.insertBefore(alertDiv, loginBox.firstChild);
    } else {
      document.body.insertBefore(alertDiv, document.body.firstChild);
    }

    // Cerrar alerta al hacer clic en X
    const closeBtn = alertDiv.querySelector(".alert-close");
    closeBtn.addEventListener("click", () => {
      alertDiv.remove();
      // Limpiar URL
      window.history.replaceState({}, document.title, window.location.pathname);
    });

    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
      if (alertDiv.parentElement) {
        alertDiv.remove();
        window.history.replaceState(
          {},
          document.title,
          window.location.pathname
        );
      }
    }, 5000);
  }

  // Mostrar mensaje de éxito en registro
  if (registered === "1") {
    const successDiv = document.createElement("div");
    successDiv.className = "alert-success";
    successDiv.innerHTML = `
      <span>✓ Registro exitoso. Ya puedes iniciar sesión</span>
      <button class="alert-close">&times;</button>
    `;

    const loginBox =
      document.querySelector(".login-box") ||
      document.querySelector(".register-box");
    if (loginBox) {
      loginBox.insertBefore(successDiv, loginBox.firstChild);
    }

    const closeBtn = successDiv.querySelector(".alert-close");
    closeBtn.addEventListener("click", () => {
      successDiv.remove();
      window.history.replaceState({}, document.title, window.location.pathname);
    });

    setTimeout(() => {
      if (successDiv.parentElement) {
        successDiv.remove();
        window.history.replaceState(
          {},
          document.title,
          window.location.pathname
        );
      }
    }, 5000);
  }

  // ===========================================================
  //                    VALIDACIÓN LOGIN
  // ===========================================================
  const loginForm = document.querySelector(
    '.login-box form[action*="AuthController.php"][method="POST"]'
  );

  if (
    loginForm &&
    loginForm.querySelector('input[name="action"][value="login"]')
  ) {
    const emailInput = loginForm.querySelector('input[type="email"]');
    const passInput = loginForm.querySelector('input[type="password"]');

    // ---- Helpers de errores ----
    function createErrorEl(text) {
      const el = document.createElement("div");
      el.className = "input-error";
      el.innerText = text;
      return el;
    }

    function removeError(input) {
      const next = input.nextElementSibling;
      if (next && next.classList.contains("input-error")) next.remove();
      input.classList.remove("has-error");
    }

    function showError(input, text) {
      removeError(input);
      input.classList.add("has-error");
      input.insertAdjacentElement("afterend", createErrorEl(text));
    }

    function isValidEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    [emailInput, passInput].forEach((input) =>
      input.addEventListener("input", () => removeError(input))
    );

    loginForm.addEventListener("submit", function (e) {
      let hasError = false;

      if (!emailInput.value.trim()) {
        showError(emailInput, "El correo es obligatorio.");
        hasError = true;
      } else if (!isValidEmail(emailInput.value.trim())) {
        showError(emailInput, "El formato del correo no es válido.");
        hasError = true;
      }

      if (!passInput.value) {
        showError(passInput, "La contraseña es obligatoria.");
        hasError = true;
      }

      if (!hasError) {
        localStorage.setItem("usuarioLogeado", "true");
      }

      if (hasError) e.preventDefault();
    });
  }

  // ===========================================================
  //                 VALIDACIÓN REGISTRO
  // ===========================================================
  const registerForm = document.querySelector(
    '.login-box form[action*="AuthController.php"][method="POST"]'
  );

  if (
    registerForm &&
    registerForm.querySelector('input[name="action"][value="register"]')
  ) {
    const nameInput =
      registerForm.querySelector('input[name="name"]') ||
      registerForm.querySelector('input[name="nombre"]');
    const emailInput = registerForm.querySelector('input[name="email"]');
    const passInput = registerForm.querySelector('input[name="password"]');
    const confirmInput = registerForm.querySelector(
      'input[name="confirm_password"]'
    );

    // ---- Helpers de errores ----
    function createErrorEl(text) {
      const el = document.createElement("div");
      el.className = "input-error";
      el.innerText = text;
      return el;
    }

    function removeError(input) {
      const next = input.nextElementSibling;
      if (next && next.classList.contains("input-error")) next.remove();
      input.classList.remove("has-error");
    }

    function showError(input, text) {
      removeError(input);
      input.classList.add("has-error");
      input.insertAdjacentElement("afterend", createErrorEl(text));
    }

    function isValidEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    [nameInput, emailInput, passInput, confirmInput].forEach((input) =>
      input.addEventListener("input", () => removeError(input))
    );

    registerForm.addEventListener("submit", function (e) {
      let hasError = false;

      if (!nameInput.value.trim()) {
        showError(nameInput, "El nombre es obligatorio.");
        hasError = true;
      }

      if (!emailInput.value.trim()) {
        showError(emailInput, "El correo es obligatorio.");
        hasError = true;
      } else if (!isValidEmail(emailInput.value.trim())) {
        showError(emailInput, "El formato del correo no es válido.");
        hasError = true;
      }

      if (!passInput.value) {
        showError(passInput, "La contraseña es obligatoria.");
        hasError = true;
      } else if (passInput.value.length < 6) {
        showError(passInput, "Debe tener al menos 6 caracteres.");
        hasError = true;
      }

      if (!confirmInput.value) {
        showError(confirmInput, "Debes confirmar tu contraseña.");
        hasError = true;
      } else if (confirmInput.value !== passInput.value) {
        showError(confirmInput, "Las contraseñas no coinciden.");
        hasError = true;
      }

      if (!hasError) {
        // PERMITIR QUE PHP REGISTRE AL USUARIO REALMENTE
        return;
      }

      e.preventDefault();
    });
  }

  // ===========================================================
  //                     CERRAR SESIÓN
  // ===========================================================
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function (e) {
      e.preventDefault();

      // Limpiar localStorage
      sessionStorage.removeItem("Usuario");
      localStorage.removeItem("Usuario");
      localStorage.removeItem("usuarioLogeado");

      // Redirigir al logout de PHP para destruir la sesión
      window.location.href = "../assets/app/controllers/LogoutController.php";
    });
  }

// ===========================================================
//          SISTEMA DE DETALLES DINÁMICOS (JUEGOS)
// ===========================================================
document.querySelectorAll(".btn-mas-detalles").forEach((btn) => {
  btn.addEventListener("click", function () {
    const juegoID = this.dataset.juego;
    localStorage.setItem("juegoSeleccionado", juegoID);

    // Detectar si viene de principal
    const vieneDePrincipal =
      window.location.pathname.includes("principal") ||
      window.location.pathname.includes("PrincipalUser");

    localStorage.setItem(
      "vieneDePrincipal",
      vieneDePrincipal ? "true" : "false"
    );

    // 👉 Redirección correcta SIEMPRE a .php
    window.location.href = "../views/MasDetalles.php";
  });
});

// ===========================================================
//      CARGAR DATOS EN MasDetalles.php
// ===========================================================
if (window.location.pathname.includes("MasDetalles.php")) {
  const usuarioLogeado = localStorage.getItem("usuarioLogeado") === "true";
  const vieneDePrincipal = localStorage.getItem("vieneDePrincipal") === "true";

  if (!usuarioLogeado && !vieneDePrincipal) {
    const barraUsuario = document.getElementById("barra-usuario");
    if (barraUsuario) barraUsuario.style.display = "none";
  }

  const juegos = {
    darksouls: {
      titulo: "Dark Souls: Remastered",
      img: "../assets/img/DarksoulsRemastered.jpg",
      precio: "Mex$ 549.00 o 45 Puntos",
      descripcion: `
        Entonces, hubo fuego. Vuelve a experimentar el juego que definió un género,
        remasterizado en alta definición y funcionando a 60 FPS.
      `,
      requisitos: [
        "SO: Windows 7 64-bit",
        "CPU: Intel i5-2300 / AMD FX-6300",
        "RAM: 8 GB",
        "GPU: GTX 460 / Radeon HD 6870",
        "DirectX 11",
        "8 GB de espacio disponible",
      ],
    },

    expedition33: {
      titulo: "Clair Obscur: Expedition 33",
      img: "../assets/img/Logo33.png",
      precio: "Mex$ 710.00 o 65 Puntos",
      descripcion: `
        Un RPG artístico con combate por turnos y un mundo misterioso por descubrir.
      `,
      requisitos: [
        "SO: Windows 10 64-bit",
        "CPU: Ryzen 5 / i5 9th Gen",
        "RAM: 16 GB",
        "GPU: GTX 1070 / RX 590",
        "DirectX 12",
        "30 GB disponibles",
      ],
    },

    risk2: {
      titulo: "Risk of Rain 2",
      img: "../assets/img/LogoRisk.png",
      precio: "Mex$ 233.00 o 20 Puntos",
      descripcion: `
        Un roguelike cooperativo en 3D donde cada partida es diferente.
      `,
      requisitos: [
        "SO: Windows 7 64-bit",
        "CPU: Intel Core i3-6100",
        "RAM: 4 GB",
        "GPU: GTX 580",
        "DirectX 11",
        "4 GB disponibles",
      ],
    },
  };

  const gameID = localStorage.getItem("juegoSeleccionado");
  const game = juegos[gameID];

  if (game) {
    document.getElementById("game-title").innerText = game.titulo;
    document.getElementById("game-img").src = game.img;
    document.getElementById("game-price").innerText = game.precio;
    document.getElementById("buy-title").innerText = "Comprar " + game.titulo;
    document.getElementById("game-desc").innerHTML = game.descripcion;

    const reqList = document.getElementById("req-list");
    reqList.innerHTML = "";
    game.requisitos.forEach((req) => {
      const li = document.createElement("li");
      li.textContent = req;
      reqList.appendChild(li);
    });
  }
}

});

// ==========================================================
//  CARGAR DATOS EN MasDetallesNoLogin.html
// ==========================================================

(() => {
  console.log("Sistema NO LOGIN cargado sin interferir con el main viejo.");

  const juegosNoLogin = {
    darksouls_p: {
      titulo: "Dark Souls: Remastered",
      imagen: "../assets/img/DarksoulsRemastered.jpg",
      precio: "Mex$ 549.00 o 45 Puntos",
      descripcion:
        "Entonces, hubo fuego. Vuelve a experimentar el juego que definió un género , remasterizado en alta definición y funcionando a 60 FPS",
      requisitos: [
        "SO: Windows 7 64-bit",
        "CPU: Intel i5-2300 / AMD FX-6300",
        "RAM: 8 GB",
        "GPU: GTX 460 / Radeon HD 6870",
        "DirectX 11",
        "8 GB de espacio disponible",
      ],
    },

    expedition33_p: {
      titulo: "Expedition 33",
      imagen: "../assets/img/Logo33.png",
      precio: "Mex$ 710.00 o 65 Puntos",
      descripcion:
        "Un RPG artístico con combate por turnos y un mundo misterioso por descubrir.",
      requisitos: [
        "SO: Windows 10 64-bit",
        "CPU: Ryzen 5 / i5 9th Gen",
        "RAM: 16 GB",
        "GPU: GTX 1070 / RX 590",
        "DirectX 12",
        "30 GB disponibles",
      ],
    },

    risk2_p: {
      titulo: "Risk of Rain 2",
      imagen: "../assets/img/LogoRisk.png",
      precio: "Mex$ 233.00 o 20 Puntos",
      descripcion:
        "Un roguelike cooperativo en 3D donde cada partida es diferente",
      requisitos: [
        "SO: Windows 7 64-bit",
        "CPU: Intel Core i3-6100",
        "RAM: 4 GB",
        "GPU: GTX 580",
        "DirectX 11",
        "4 GB disponibles",
      ],
    },
  };

  // =============================
  //  BOTONES DEL INDEX
  // =============================

  document.addEventListener("DOMContentLoaded", () => {
    const botones = document.querySelectorAll(".btn-mas-detalles");

    botones.forEach((btn) => {
      const gameID = btn.getAttribute("data-juego");

      if (gameID && gameID.endsWith("_p")) {
        btn.addEventListener("click", () => {
          localStorage.setItem("juegoSeleccionadoNL", gameID);
          window.location.href = "views/MasDetallesNoLogin.html";
        });
      }
    });
  });

  // =============================
  //  MOSTRAR DETALLES
  // =============================
  if (window.location.pathname.includes("MasDetallesNoLogin")) {
    const id = localStorage.getItem("juegoSeleccionadoNL");

    if (!id || !juegosNoLogin[id]) {
      console.warn("Juego no encontrado en sistema NL");
      return;
    }

    const j = juegosNoLogin[id];

    document.getElementById("game-nl-title").textContent = j.titulo;
    document.getElementById("game-nl-img").src = j.imagen;
    document.getElementById("buy-nl-title").textContent = "Comprar " + j.titulo;
    document.getElementById("game-nl-price").textContent = j.precio;
    document.getElementById("game-nl-desc").textContent = j.descripcion;
    const reqList = document.getElementById("req-nl-list");
    reqList.innerHTML = "";

    j.requisitos.forEach((r) => {
      const li = document.createElement("li");
      li.textContent = r;
      reqList.appendChild(li);
    });
  }
})();
const btnCarrito = document.querySelector('.btn-carrito');
const modal = document.getElementById('modalCarrito');
const btnAceptar = document.getElementById('btnAceptar');

btnCarrito.addEventListener('click', (e) => {
  e.preventDefault();
  modal.classList.remove('oculto');
});

btnAceptar.addEventListener('click', () => {
  modal.classList.add('oculto');
});

