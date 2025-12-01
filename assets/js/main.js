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
    const alertDiv = document.createElement("div");
    alertDiv.className = "alert-error";
    alertDiv.innerHTML = `
      <span>${errorMessages[error]}</span>
      <button class="alert-close">&times;</button>
    `;

    const loginBox =
      document.querySelector(".login-box") ||
      document.querySelector(".register-box");
    if (loginBox) {
      loginBox.insertBefore(alertDiv, loginBox.firstChild);
    } else {
      document.body.insertBefore(alertDiv, document.body.firstChild);
    }

    const closeBtn = alertDiv.querySelector(".alert-close");
    closeBtn.addEventListener("click", () => {
      alertDiv.remove();
      window.history.replaceState({}, document.title, window.location.pathname);
    });

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

      // Redirigir al logout de PHP
      window.location.href = "../assets/app/controllers/LogoutController.php";
    });
  }

  // ===========================================================
  //          MODAL DE CARRITO (SOLO INDEX.HTML)
  // ===========================================================
  const btnCarrito = document.querySelector(".btn-carrito");
  const modal = document.getElementById("modalCarrito");
  const btnAceptar = document.getElementById("btnAceptar");

  if (btnCarrito && modal && btnAceptar) {
    btnCarrito.addEventListener("click", (e) => {
      e.preventDefault();
      modal.classList.remove("oculto");
    });

    btnAceptar.addEventListener("click", () => {
      modal.classList.add("oculto");
    });
  }
});
