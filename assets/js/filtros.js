/* =====================================================
   FILTROS DE CATÁLOGO: categoría + temporada
   Se combinan: un producto se muestra solo si cumple
   los dos filtros a la vez.
===================================================== */

(function () {
    "use strict";

    var barra = document.querySelector(".catalog-header-bar");
    if (!barra) return;

    var tarjetas = document.querySelectorAll(".brutal-card");
    var sinResultados = document.getElementById("noItems");

    var estado = {
        categoria: "all",
        temporada: "all"
    };

    function aplicarFiltros() {
        var visibles = 0;

        tarjetas.forEach(function (tarjeta) {
            var coincideCategoria =
                estado.categoria === "all" ||
                tarjeta.dataset.category === estado.categoria;

            var coincideTemporada =
                estado.temporada === "all" ||
                tarjeta.dataset.season === estado.temporada;

            var mostrar = coincideCategoria && coincideTemporada;

            tarjeta.style.display = mostrar ? "" : "none";

            if (mostrar) visibles++;
        });

        if (sinResultados) {
            sinResultados.style.display = visibles === 0 ? "block" : "none";
        }
    }

    function marcarActivo(boton) {
        var grupo = boton.closest(".filter-group");
        if (!grupo) return;

        grupo.querySelectorAll(".f-btn").forEach(function (b) {
            b.classList.remove("active");
        });

        boton.classList.add("active");
    }

    // Fase de captura: este código atiende los clics de los filtros
    // antes que cualquier otro script de la página.
    barra.addEventListener("click", function (evento) {
        var boton = evento.target.closest(".f-btn");
        if (!boton || !barra.contains(boton)) return;

        evento.stopPropagation();
        evento.preventDefault();

        if (boton.hasAttribute("data-season")) {
            estado.temporada = boton.getAttribute("data-season");
        } else {
            estado.categoria = boton.getAttribute("data-filter");
        }

        marcarActivo(boton);
        aplicarFiltros();
    }, true);

})();