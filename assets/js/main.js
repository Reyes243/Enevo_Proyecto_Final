//mezLsqc8B3X

// assets/js/main.js
document.addEventListener("DOMContentLoaded", function () {
  // ====== VALIDACIÓN DEL LOGIN ======
  const form = document.querySelector(".login-box form");
  if (form) {
    const emailInput = form.querySelector('input[type="email"]');
    const passInput = form.querySelector('input[type="password"]');
    const submitBtn = form.querySelector('button[type="submit"]');

    function createErrorEl(text) {
      const el = document.createElement("div");
      el.className = "input-error";
      el.innerText = text;
      return el;
    }

    function removeError(input) {
      const next = input.nextElementSibling;
      if (next && next.classList && next.classList.contains("input-error")) {
        next.remove();
      }
      input.classList.remove("has-error");
    }

    function showError(input, text) {
      removeError(input);
      input.classList.add("has-error");
      const err = createErrorEl(text);
      input.insertAdjacentElement("afterend", err);
    }

    function isValidEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(String(email).toLowerCase());
    }

    function setLoading(isLoading) {
      if (isLoading) {
        submitBtn.disabled = true;
        submitBtn.dataset.orig = submitBtn.innerText;
        submitBtn.innerText = "Validando...";
      } else {
        submitBtn.disabled = false;
        if (submitBtn.dataset.orig)
          submitBtn.innerText = submitBtn.dataset.orig;
      }
    }

    [emailInput, passInput].forEach((input) => {
      input.addEventListener("input", () => removeError(input));
    });

    form.addEventListener("submit", function (e) {
      removeError(emailInput);
      removeError(passInput);

      const email = emailInput.value.trim();
      const password = passInput.value;

      let hasError = false;

      if (!email) {
        e.preventDefault();
        showError(emailInput, "El correo es obligatorio.");
        hasError = true;
      }

      if (!password) {
        e.preventDefault();
        showError(passInput, "La contraseña es obligatoria.");
        hasError = true;
      }
    });
  }
});

// ====== CERRAR SESIÓN (para principal.html) ======
document.addEventListener("DOMContentLoaded", function () {
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function (e) {
      e.preventDefault();
      sessionStorage.removeItem("Usuario");
      localStorage.removeItem("Usuario");
      // redirige al index principal
      window.location.href = "../index.html";
    });
  }
});
