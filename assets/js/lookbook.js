/* =====================================================
   LOOKBOOK: visor de fotos a pantalla grande
===================================================== */

(function () {
    "use strict";

    var items = Array.prototype.slice.call(document.querySelectorAll(".look-item"));
    var overlay = document.getElementById("lbOverlay");

    if (!items.length || !overlay) return;

    var imagen    = document.getElementById("lbImg");
    var leyenda   = document.getElementById("lbCaption");
    var btnCerrar = document.getElementById("lbClose");
    var btnPrev   = document.getElementById("lbPrev");
    var btnNext   = document.getElementById("lbNext");

    var actual = 0;
    var disparador = null;
    var inicioToque = null;

    // Con una sola foto no hacen falta las flechas
    if (items.length < 2) {
        btnPrev.style.display = "none";
        btnNext.style.display = "none";
    }

    function dosDigitos(n) {
        return String(n).padStart(2, "0");
    }

    function mostrar(indice) {
        actual = (indice + items.length) % items.length;

        var item = items[actual];

        imagen.src = item.dataset.full;
        imagen.alt = item.dataset.caption || "Look HIVE";
        leyenda.textContent = item.dataset.caption || ("LOOK / " + dosDigitos(actual + 1));
    }

    function abrir(indice) {
        disparador = items[indice];
        mostrar(indice);

        overlay.classList.add("open");
        overlay.setAttribute("aria-hidden", "false");
        document.documentElement.classList.add("lb-lock");

        setTimeout(function () {
            btnCerrar.focus();
        }, 30);
    }

    function cerrar() {
        overlay.classList.remove("open");
        overlay.setAttribute("aria-hidden", "true");
        document.documentElement.classList.remove("lb-lock");

        if (disparador) disparador.focus();
    }

    items.forEach(function (item, indice) {
        item.addEventListener("click", function () {
            abrir(indice);
        });
    });

    btnCerrar.addEventListener("click", cerrar);
    btnPrev.addEventListener("click", function () { mostrar(actual - 1); });
    btnNext.addEventListener("click", function () { mostrar(actual + 1); });

    // Clic en el fondo oscuro cierra el visor
    overlay.addEventListener("click", function (evento) {
        if (evento.target === overlay) cerrar();
    });

    // Teclado: Esc, flechas y foco dentro del visor
    document.addEventListener("keydown", function (evento) {
        if (!overlay.classList.contains("open")) return;

        if (evento.key === "Escape") {
            cerrar();
        } else if (evento.key === "ArrowLeft") {
            mostrar(actual - 1);
        } else if (evento.key === "ArrowRight") {
            mostrar(actual + 1);
        } else if (evento.key === "Tab") {
            var botones = Array.prototype.filter.call(
                overlay.querySelectorAll("button"),
                function (b) { return b.style.display !== "none"; }
            );
            if (!botones.length) return;

            var primero = botones[0];
            var ultimo = botones[botones.length - 1];

            if (evento.shiftKey && document.activeElement === primero) {
                evento.preventDefault();
                ultimo.focus();
            } else if (!evento.shiftKey && document.activeElement === ultimo) {
                evento.preventDefault();
                primero.focus();
            }
        }
    });

    // En el celular: deslizar para cambiar de foto
    overlay.addEventListener("touchstart", function (evento) {
        inicioToque = evento.changedTouches[0].clientX;
    }, { passive: true });

    overlay.addEventListener("touchend", function (evento) {
        if (inicioToque === null || items.length < 2) return;

        var diferencia = evento.changedTouches[0].clientX - inicioToque;
        inicioToque = null;

        if (Math.abs(diferencia) > 50) {
            mostrar(diferencia > 0 ? actual - 1 : actual + 1);
        }
    }, { passive: true });

})();