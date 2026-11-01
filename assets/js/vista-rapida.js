/* =====================================================
   VISTA RÁPIDA DEL PRODUCTO
   Toma los datos de la tarjeta y los muestra en un modal.
   El botón de agregar usa el de la tarjeta, así se reutiliza
   la lógica del carrito que ya existe.
===================================================== */

(function () {
    "use strict";

    var overlay = document.getElementById("qvOverlay");
    if (!overlay) return;

    var dialog       = overlay.querySelector(".qv-dialog");
    var btnCerrar    = document.getElementById("qvClose");
    var imagen       = document.getElementById("qvImage");
    var codigo       = document.getElementById("qvCode");
    var temporadaTop = document.getElementById("qvSeasonTop");
    var etiquetas    = document.getElementById("qvTags");
    var titulo       = document.getElementById("qvTitle");
    var precio       = document.getElementById("qvPrice");
    var descripcion  = document.getElementById("qvDesc");
    var datos        = document.getElementById("qvSpecs");
    var stock        = document.getElementById("qvStock");
    var bloqueTalles = document.getElementById("qvSizesBlock");
    var talles       = document.getElementById("qvSizes");
    var btnAgregar   = document.getElementById("qvAdd");

    var tarjetaActual = null;
    var disparador = null;

    function vaciar(elemento) {
        while (elemento.firstChild) {
            elemento.removeChild(elemento.firstChild);
        }
    }

    function crearEtiqueta(texto, clase) {
        var span = document.createElement("span");
        span.className = "qv-tag" + (clase ? " " + clase : "");
        span.textContent = texto;
        return span;
    }

    function agregarDato(nombre, valor) {
        if (!valor) return;

        var dt = document.createElement("dt");
        dt.textContent = nombre;

        var dd = document.createElement("dd");
        dd.textContent = valor;

        datos.appendChild(dt);
        datos.appendChild(dd);
    }

    function abrir(tarjeta, origen) {
        var d = tarjeta.dataset;

        tarjetaActual = tarjeta;
        disparador = origen || null;

        // Foto y encabezado del marco
        imagen.src = d.image || "";
        imagen.alt = d.title || "";
        codigo.textContent = "HIVE® / " + String(d.id || "").padStart(3, "0");
        temporadaTop.textContent = d.seasonName ? d.seasonName.toUpperCase() : "";

        // Etiquetas: categoría y descuento
        vaciar(etiquetas);
        if (d.categoryName) {
            etiquetas.appendChild(crearEtiqueta(d.categoryName.toUpperCase()));
        }
        var oferta = tarjeta.querySelector(".brutal-badge-sale");
        if (oferta) {
            etiquetas.appendChild(crearEtiqueta(oferta.textContent.trim(), "qv-tag--sale"));
        }

        // Nombre y precio (se copia el precio ya formateado de la tarjeta)
        titulo.textContent = d.title || "";
        var precioTarjeta = tarjeta.querySelector(".card-price");
        precio.innerHTML = precioTarjeta ? precioTarjeta.innerHTML : "";

        // Descripción
        descripcion.textContent = d.description || "";
        descripcion.style.display = d.description ? "" : "none";

        // Datos del producto (solo los que existen)
        vaciar(datos);
        agregarDato("CATEGORÍA", d.categoryName);
        agregarDato("TEMPORADA", d.seasonName);
        agregarDato("PESO", d.weight);
        agregarDato("MEDIDAS", d.dimensions);
        datos.style.display = datos.children.length ? "" : "none";

        // Aviso de stock
        var unidades = parseInt(d.stock, 10);
        var aviso = "";
        if (!(unidades > 0)) {
            aviso = "PRODUCTO AGOTADO";
        } else if (unidades === 1) {
            aviso = "ÚLTIMA UNIDAD DISPONIBLE";
        } else if (unidades <= 5) {
            aviso = "ÚLTIMAS " + unidades + " UNIDADES";
        }
        stock.textContent = aviso;
        stock.style.display = aviso ? "" : "none";

        // Talles: al elegir uno se selecciona también en la tarjeta
        var botonesTarjeta = tarjeta.querySelectorAll(".s-btn");
        vaciar(talles);

        botonesTarjeta.forEach(function (botonTarjeta) {
            var boton = document.createElement("button");
            boton.type = "button";
            boton.className = "qv-size" + (botonTarjeta.classList.contains("active") ? " active" : "");
            boton.textContent = botonTarjeta.textContent.trim();

            boton.addEventListener("click", function () {
                botonTarjeta.click();

                talles.querySelectorAll(".qv-size").forEach(function (b) {
                    b.classList.remove("active");
                });
                boton.classList.add("active");
            });

            talles.appendChild(boton);
        });
        bloqueTalles.style.display = botonesTarjeta.length ? "" : "none";

        // Botón de agregar
        var botonAgregarTarjeta = tarjeta.querySelector(".brutal-add-btn");
        var agotado = !botonAgregarTarjeta || botonAgregarTarjeta.disabled;

        btnAgregar.disabled = agotado;
        btnAgregar.innerHTML = agotado
            ? "AGOTADO"
            : 'AGREGAR A LA BOLSA <i class="fa-solid fa-arrow-right"></i>';

        // Mostrar
        overlay.classList.add("open");
        overlay.setAttribute("aria-hidden", "false");
        document.documentElement.classList.add("qv-lock");

        setTimeout(function () {
            btnCerrar.focus();
        }, 30);
    }

    function cerrar(devolverFoco) {
        overlay.classList.remove("open");
        overlay.setAttribute("aria-hidden", "true");
        document.documentElement.classList.remove("qv-lock");

        if (devolverFoco && disparador && disparador.focus) {
            disparador.focus();
        }

        tarjetaActual = null;
    }

    // Abrir: clic en la foto, el nombre o el botón "Vista rápida"
    document.addEventListener("click", function (evento) {
        var disparo = evento.target.closest(".quick-btn, .card-img-wrap, .card-title");
        if (!disparo) return;

        var tarjeta = disparo.closest(".brutal-card");
        if (!tarjeta) return;

        evento.preventDefault();
        abrir(tarjeta, tarjeta.querySelector(".quick-btn"));
    });

    // Agregar a la bolsa (usa el botón de la tarjeta)
    btnAgregar.addEventListener("click", function () {
        if (!tarjetaActual) return;

        var botonTarjeta = tarjetaActual.querySelector(".brutal-add-btn");

        if (botonTarjeta && !botonTarjeta.disabled) {
            cerrar(false);
            botonTarjeta.click();
        }
    });

    // Cerrar: botón, clic afuera o tecla Esc
    btnCerrar.addEventListener("click", function () {
        cerrar(true);
    });

    overlay.addEventListener("click", function (evento) {
        if (evento.target === overlay) cerrar(true);
    });

    document.addEventListener("keydown", function (evento) {
        if (!overlay.classList.contains("open")) return;

        if (evento.key === "Escape") {
            cerrar(true);
            return;
        }

        // El foco queda dentro del modal mientras está abierto
        if (evento.key === "Tab") {
            var enfocables = dialog.querySelectorAll("button:not([disabled])");
            if (!enfocables.length) return;

            var primero = enfocables[0];
            var ultimo = enfocables[enfocables.length - 1];

            if (evento.shiftKey && document.activeElement === primero) {
                evento.preventDefault();
                ultimo.focus();
            } else if (!evento.shiftKey && document.activeElement === ultimo) {
                evento.preventDefault();
                primero.focus();
            }
        }
    });

})();