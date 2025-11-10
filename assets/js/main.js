// assets/js/main.js
document.addEventListener("DOMContentLoaded", function () {
  // ====== VALIDACIÓN LOGIN ======
  const loginForm = document.querySelector(
    '.login-box form[action*="AuthController.php"][method="POST"][action$="AuthController.php"]'
  );
  if (
    loginForm &&
    loginForm.querySelector('input[name="action"][value="login"]')
  ) {
    const emailInput = loginForm.querySelector('input[type="email"]');
    const passInput = loginForm.querySelector('input[type="password"]');
    const submitBtn = loginForm.querySelector('button[type="submit"]');

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

      if (hasError) e.preventDefault();
    });
  }

  // ====== VALIDACIÓN REGISTRO ======
  const registerForm = document.querySelector(
    '.login-box form[action*="AuthController.php"][method="POST"]'
  );
  if (
    registerForm &&
    registerForm.querySelector('input[name="action"][value="register"]')
  ) {
    const nameInput = registerForm.querySelector('input[name="name"]');
    const emailInput = registerForm.querySelector('input[name="email"]');
    const passInput = registerForm.querySelector('input[name="password"]');
    const confirmInput = registerForm.querySelector(
      'input[name="confirm_password"]'
    );
    const submitBtn = registerForm.querySelector('button[type="submit"]');

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
        showError(passInput, "La contraseña debe tener al menos 6 caracteres.");
        hasError = true;
      }

      if (!confirmInput.value) {
        showError(confirmInput, "Debes confirmar tu contraseña.");
        hasError = true;
      } else if (confirmInput.value !== passInput.value) {
        showError(confirmInput, "Las contraseñas no coinciden.");
        hasError = true;
      }

      // 👇 AGREGA ESTE BLOQUE ABAJO
      if (!hasError) {
        e.preventDefault(); // evita que recargue la página
        alert("Registro exitoso. Serás redirigido al login.");
        setTimeout(() => {
          window.location.href = "login.html"; // redirige a login (misma carpeta)
        }, 1500);
      }

      if (hasError) e.preventDefault();
    });
  }

  // ====== CERRAR SESIÓN ======
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function (e) {
      e.preventDefault();
      sessionStorage.removeItem("Usuario");
      localStorage.removeItem("Usuario");
      window.location.href = "../index.html";
    });
  }
});
