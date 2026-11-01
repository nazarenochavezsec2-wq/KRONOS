<?php
require_once(__DIR__ . "/admin/bd.php");

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}

function formatoPrecio($valor) {
    return "$" . number_format((float)$valor, 0, ",", ".");
}

function slugCategoria($texto) {
    $slug = mb_strtolower(trim((string)$texto), "UTF-8");
    $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', $slug);
    return trim($slug, "-");
}

// Obtener productos activos
$sentencia = $conexion->prepare("
    SELECT p.*, c.nombre AS categoria, t.nombre AS temporada
    FROM tbl_productos p
    LEFT JOIN tbl_categorias c ON c.id = p.id_categoria
    LEFT JOIN tbl_temporadas t ON t.id = p.id_temporada
    WHERE p.estado = 1
    ORDER BY p.id DESC
");

$sentencia->execute();
$lista_productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Obtener categorías
$categorias = [];

// Obtener temporadas que tienen productos activos
$temporadas = [];

$sentencia = $conexion->prepare("
    SELECT DISTINCT t.id, t.nombre
    FROM tbl_temporadas t
    INNER JOIN tbl_productos p ON p.id_temporada = t.id
    WHERE p.estado = 1
    ORDER BY t.id ASC
");
$sentencia->execute();

foreach ($sentencia->fetchAll(PDO::FETCH_ASSOC) as $fila) {
    $slug = slugCategoria($fila["nombre"]);

    if ($slug !== "" && !isset($temporadas[$slug])) {
        $temporadas[$slug] = $fila["nombre"];
    }
}

foreach ($lista_productos as $producto) {
    $slug = slugCategoria($producto["categoria"] ?? "");

    if ($slug !== "" && !isset($categorias[$slug])) {
        $categorias[$slug] = $producto["categoria"];
    }
    
}
// Obtener integrantes del equipo
$equipo = [];

try {
    $sentencia = $conexion->prepare("SELECT id, nombre, cargo, foto FROM equipo ORDER BY id ASC");
    $sentencia->execute();
    $equipo = $sentencia->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $equipo = []; // si la tabla no existe, la sección simplemente no se muestra
}

$carpetaEquipo = "assets/img/equipo/";
$carpetaImagenes = "assets/img/productos/";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$logueado = !empty($_SESSION["autenticado"]);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HIVE® | MODA DIGITAL Y2K</title>

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- BARRA SUPERIOR -->
    <div class="brutal-ticker">
        <div class="ticker-content">
            <span>✦ BIENVENIDOS A HIVE DIGITAL WORLD</span>
            <span>✦ COLECCIÓN STREETWEAR Y2K</span>
            <span>✦ ENVÍOS A TODO EL MUNDO</span>
            <span>✦ NUEVA GENERACIÓN / NUEVO ESTILO</span>
            <span>✦ BIENVENIDOS A HIVE DIGITAL WORLD</span>
            <span>✦ COLECCIÓN STREETWEAR Y2K</span>
            <span>✦ ENVÍOS A TODO EL MUNDO</span>
        </div>
    </div>

    <!-- HEADER -->
    <header class="brutal-header">
        <div class="nav-wrapper">

            <a href="#index" class="brutal-logo">
                HIVE<span>®</span>
                <small>MODA DIGITAL</small>
            </a>

            <nav class="brutal-nav">
                <a href="#index">INICIO</a>
                <a href="#catalog">TIENDA</a>
                <a href="#manifesto">NOSOTROS</a>
                <?php if (count($equipo) > 0) { ?>
                    <a href="#equipo">EQUIPO</a>
                <?php } ?>
            </nav>

            <div class="nav-actions">

                <?php if ($logueado): ?>
                    <span class="user-badge" style="display: inline-flex; align-items: center; gap: 6px; font-family: 'Space Mono', monospace; font-size: 0.85rem; font-weight: 700; color: #fff;">
                        <i class="fa-solid fa-user"></i> Hola, <?= htmlspecialchars($_SESSION["usuario"], ENT_QUOTES, 'UTF-8') ?>
                    </span>

                    <?php if ($_SESSION["rol"] === "admin"): ?>
                        <a href="/Kronos/admin/index.php">
                            <button class="brutal-bag-btn">PANEL</button>
                        </a>
                    <?php endif; ?>

                    <a href="/Kronos/admin/cerrar.php">
                        <button class="brutal-bag-btn" style="border-color: #ff3333; color: #ff3333;">SALIR</button>
                    </a>
                <?php else: ?>
                    <a href="admin/login.php">
                        <button class="brutal-bag-btn" id="LoginLink">
                            <i class="fa-regular fa-user"></i> INGRESAR
                        </button>
                    </a>
                <?php endif; ?>

                <button class="brutal-bag-btn cart-button" id="cartToggle">
                    <i class="fa-solid fa-bag-shopping"></i>
                    BOLSA [<span id="cartCount">0</span>]
                </button>

            </div>
        </div>
    </header>

    <!-- HERO -->
    <main>

        <section id="index" class="editorial-hero">

            <div class="hero-decoration hero-decoration-one">✳</div>
            <div class="hero-decoration hero-decoration-two">✦</div>

            <div class="editorial-title-box">

                <div class="sub-heading">
                    <span class="status-dot"></span>
                    TIENDA ONLINE / DESDE 2026
                </div>

                <div class="hero-label">
                    <span>EL FUTURO ES AHORA</span>
                    <span>VOL. 01 — 2026</span>
                </div>

                <h1>
                    VESTITE<br>
                    COMO<br>
                    <span>EL FUTURO.</span>
                </h1>

                <p>
                    Sin reglas. Sin límites. Solo tu propia identidad.
                    Descubrí la nueva generación de streetwear.
                    Bienvenidos al universo HIVE.
                </p>

                <div class="hero-buttons">
                    <a href="#catalog" class="hero-cta">
                        EXPLORÁ LA COLECCIÓN
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                    <span class="hero-side-text">DESLIZÁ PARA DESCUBRIR ↓</span>
                </div>

                <div class="hero-bottom-info">
                    <div>
                        <span>COLECCIÓN</span>
                        <strong>Y2K / 001</strong>
                    </div>

                    <div>
                        <span>ESTILO</span>
                        <strong>STREET FUTURISTA</strong>
                    </div>

                    <div>
                        <span>ESTADO</span>
                        <strong>DISPONIBLE AHORA</strong>
                    </div>
                </div>

            </div>

            <div class="hero-image-editorial">

                <div class="hero-image-top">
                    <span>HIVE® EDITORIAL</span>
                    <span>001 / 026</span>
                </div>

                <div class="hero-image-frame">
                    <img src="img/portada.png" alt="Colección HIVE Y2K">
                </div>

                <div class="hero-image-bottom">
                    <span>GENERACIÓN FUTURO</span>
                    <span>DESDE MMXXVI ✦</span>
                </div>

                <div class="hero-floating-card">
                    <span>NUEVO DROP</span>
                    <strong>ENERGÍA<br>Y2K.</strong>
                    <i class="fa-solid fa-star"></i>
                </div>

            </div>

        </section>

        <!-- BARRA DECORATIVA -->
        <div class="collection-strip">
            <span>UNIVERSO HIVE</span>
            <span>✦</span>
            <span>NUEVA GENERACIÓN</span>
            <span>✦</span>
            <span>CULTURA URBANA</span>
            <span>✦</span>
            <span>Y2K PARA SIEMPRE</span>
        </div>

        <!-- CATÁLOGO -->
        <section id="catalog" class="brutal-catalog">

            <div class="catalog-intro">

                <div>
                    <span class="section-label">// 01 — LA COLECCIÓN</span>
                    <h2>ENCONTRÁ TU<br><span>NUEVA IDENTIDAD.</span></h2>
                </div>

                <div class="catalog-description">
                    <p>
                        Piezas seleccionadas para una nueva generación.
                        Explorá la colección HIVE y encontrá tu próxima declaración de estilo.
                    </p>
                    <span>ELEGÍ TU ESTILO ↓</span>
                </div>

            </div>

            <div class="catalog-header-bar">

                <div class="catalog-counter">
                    <span class="status-dot"></span>
                    PRODUCTOS DISPONIBLES
                </div>

                <div class="filter-stack">

                    <div class="filter-block">
                        <span class="filter-label">CATEGORÍA</span>

                        <div class="filter-group" id="filterCategorias">

                            <button class="f-btn active" data-filter="all">
                                TODOS LOS PRODUCTOS
                            </button>

                            <?php foreach ($categorias as $slug => $nombreCategoria) { ?>

                                <button class="f-btn" data-filter="<?php echo escapar($slug); ?>">
                                    <?php echo escapar(mb_strtoupper($nombreCategoria, "UTF-8")); ?>
                                </button>

                            <?php } ?>

                        </div>
                    </div>

                    <?php if (count($temporadas) > 0) { ?>

                        <div class="filter-block">
                            <span class="filter-label">TEMPORADA</span>

                            <div class="filter-group" id="filterTemporadas">

                                <button class="f-btn active" data-season="all">
                                    TODAS
                                </button>

                                <?php foreach ($temporadas as $slug => $nombreTemporada) { ?>

                                    <button class="f-btn" data-season="<?php echo escapar($slug); ?>">
                                        <?php echo escapar(mb_strtoupper($nombreTemporada, "UTF-8")); ?>
                                    </button>

                                <?php } ?>

                            </div>
                        </div>

                    <?php } ?>

                </div>

            </div>

            <div class="brutal-grid" id="productGrid">

                <?php if (count($lista_productos) === 0) { ?>

                    <p class="grid-empty">
                        NO HAY PRODUCTOS DISPONIBLES EN ESTE MOMENTO.
                    </p>

                <?php } ?>

                <?php foreach ($lista_productos as $producto) {

                    $precioBase = (int)$producto["precio_base"];
                    $precioDescuento = $producto["precio_descuento"];

                    $tieneDescuento = (
                        $precioDescuento !== null &&
                        $precioDescuento !== "" &&
                        (int)$precioDescuento > 0 &&
                        (int)$precioDescuento < $precioBase
                    );

                    $precioFinal = $tieneDescuento
                        ? (int)$precioDescuento
                        : $precioBase;

                    $porcentaje = $tieneDescuento
                        ? round((1 - ((int)$precioDescuento / $precioBase)) * 100)
                        : 0;

                    $sinStock = ((int)$producto["stock"] <= 0);

                    $imagen = $carpetaImagenes . $producto["imagen"];

                    $talles = ["S", "M", "L", "XL"];

                    if (!empty($producto["talles"])) {
                        $lista = array_values(
                            array_filter(
                                array_map("trim", explode(",", $producto["talles"]))
                            )
                        );

                        if (count($lista) > 0) {
                            $talles = $lista;
                        }
                    }

                    $descripcion = trim((string)($producto["descripcion"] ?? ""));
                    $peso = (float)($producto["peso"] ?? 0);
                    $dimensiones = (float)($producto["dimensiones"] ?? 0);

                    $pesoTexto = $peso > 0
                        ? rtrim(rtrim(number_format($peso, 2, ",", "."), "0"), ",") . " kg"
                        : "";

                    $medidasTexto = $dimensiones > 0
                        ? rtrim(rtrim(number_format($dimensiones, 2, ",", "."), "0"), ",") . " cm"
                        : "";
                ?>

                    <article class="brutal-card"
                        data-id="<?php echo escapar($producto['id']); ?>"
                        data-title="<?php echo escapar($producto['nombre']); ?>"
                        data-price="<?php echo $precioFinal; ?>"
                        data-image="<?php echo escapar($imagen); ?>"
                        data-category="<?php echo escapar(slugCategoria($producto['categoria'] ?? '')); ?>"
                        data-season="<?php echo escapar(slugCategoria($producto['temporada'] ?? '')); ?>"
                        data-category-name="<?php echo escapar($producto['categoria'] ?? ''); ?>"
                        data-season-name="<?php echo escapar($producto['temporada'] ?? ''); ?>"
                        data-description="<?php echo escapar($descripcion); ?>"
                        data-weight="<?php echo escapar($pesoTexto); ?>"
                        data-dimensions="<?php echo escapar($medidasTexto); ?>"
                        data-stock="<?php echo (int)$producto['stock']; ?>">

                        <div class="card-img-wrap">

                            <div class="product-number">
                                HIVE® / <?php echo str_pad($producto["id"], 3, "0", STR_PAD_LEFT); ?>
                            </div>

                            <?php if (!empty($producto["categoria"])) { ?>
                                <span class="brutal-badge">
                                    <?php echo escapar(mb_strtoupper($producto["categoria"], "UTF-8")); ?>
                                </span>
                            <?php } ?>

                            <?php if ($tieneDescuento) { ?>
                                <span class="brutal-badge brutal-badge-sale">
                                    -<?php echo $porcentaje; ?>%
                                </span>
                            <?php } ?>

                            <img
                                src="<?php echo escapar($imagen); ?>"
                                alt="<?php echo escapar($producto['nombre']); ?>"
                                loading="lazy"
                            >

                            <button type="button" class="quick-btn">VISTA RÁPIDA +</button>

                        </div>

                        <div class="card-body">

                            <div class="card-meta">

                                <div class="product-title-row">
                                    <h3 class="card-title">
                                        <?php echo escapar($producto["nombre"]); ?>
                                    </h3>

                                    <span class="product-star">✳</span>
                                </div>

                                <?php if (!empty($producto["temporada"])) { ?>
                                    <div class="card-season">
                                        <?php echo escapar(mb_strtoupper($producto["temporada"], "UTF-8")); ?>
                                    </div>
                                <?php } ?>

                                <div class="card-price">

                                    <?php if ($tieneDescuento) { ?>
                                        <span class="card-price-old">
                                            <?php echo formatoPrecio($precioBase); ?>
                                        </span>
                                    <?php } ?>

                                    <?php echo formatoPrecio($precioFinal); ?>

                                </div>

                            </div>

                            <div class="card-actions">

                                <div class="size-label">
                                    ELEGÍ TU TALLE
                                </div>

                                <div class="brutal-sizes">

                                    <?php foreach ($talles as $i => $talle) { ?>

                                        <button
                                            type="button"
                                            class="s-btn <?php echo ($i === 0) ? 'active' : ''; ?>"
                                        >
                                            <?php echo escapar($talle); ?>
                                        </button>

                                    <?php } ?>

                                </div>

                                <?php if ($sinStock) { ?>

                                    <button type="button" class="brutal-add-btn" disabled>
                                        AGOTADO
                                    </button>

                                <?php } else { ?>

                                    <button type="button" class="brutal-add-btn">
                                        AGREGAR A LA BOLSA <i class="fa-solid fa-arrow-right"></i>
                                    </button>

                                <?php } ?>

                            </div>

                        </div>

                    </article>

                <?php } ?>

            </div>

            <p class="grid-empty" id="noItems" style="display:none;">
                NO HAY PRODUCTOS EN ESTA SELECCIÓN.
            </p>

        </section>

        <!-- MANIFIESTO -->
        <section id="manifesto" class="brutal-manifesto">

            <div class="manifesto-visual">
                <span class="manifesto-star">✳</span>
                <span class="manifesto-small">BIENVENIDOS AL FUTURO</span>
                <h2>SÉ<br>TU<br><span>MISMO.</span></h2>
                <span class="manifesto-number">HIVE® / 001</span>
            </div>

            <div class="manifesto-content">

                <span class="manifesto-tag">// 02 — SOBRE HIVE</span>

                <h3>MÁS QUE MODA.<br>UNA ACTITUD TOTALMENTE NUEVA.</h3>

                <p>
                    HIVE nace de la conexión entre la cultura urbana,
                    la estética digital y la libertad de expresión.
                    Creemos que la moda es una forma de identidad:
                    una manera de mostrar quién sos sin necesidad de
                    seguir las reglas.
                </p>

                <p>
                    Inspirados en la energía de los años 2000 y
                    reinterpretados para una nueva generación.
                    Esta es tu generación. Esto es HIVE.
                </p>

                <a href="#catalog" class="manifesto-link">
                    DESCUBRÍ LA COLECCIÓN
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>

            </div>

        </section>

        <!-- EQUIPO -->
        <?php if (count($equipo) > 0) { ?>

        <section id="equipo" class="brutal-crew">

            <div class="catalog-intro">

                <div>
                    <span class="section-label">// 03 — EL EQUIPO</span>
                    <h2>LAS PERSONAS<br><span>DETRÁS DE HIVE.</span></h2>
                </div>

                <div class="catalog-description">
                    <p>
                        Un equipo que vive la moda y la cultura urbana.
                        Conocé a quienes hacen que HIVE exista.
                    </p>
                    <span>EL EQUIPO HIVE ✦</span>
                </div>

            </div>

            <div class="crew-grid">

                <?php foreach ($equipo as $i => $miembro) { ?>

                    <article class="crew-card">

                        <div class="crew-bar">
                            <span>HIVE® / <?php echo str_pad($i + 1, 2, "0", STR_PAD_LEFT); ?></span>
                            <span>EQUIPO</span>
                        </div>

                        <div class="crew-photo">
                            <?php if (!empty($miembro["foto"])) { ?>
                                <img
                                    src="<?php echo escapar($carpetaEquipo . $miembro['foto']); ?>"
                                    alt="<?php echo escapar($miembro['nombre']); ?>"
                                    loading="lazy"
                                >
                            <?php } else { ?>
                                <div class="crew-initial">
                                    <?php echo escapar(mb_strtoupper(mb_substr($miembro["nombre"], 0, 1, "UTF-8"), "UTF-8")); ?>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="crew-info">
                            <h3 class="crew-name"><?php echo escapar($miembro["nombre"]); ?></h3>
                            <p class="crew-role"><?php echo escapar($miembro["cargo"]); ?></p>
                        </div>

                    </article>

                <?php } ?>

            </div>

        </section>

        <?php } ?>

    </main>

    <!-- VISTA RÁPIDA DEL PRODUCTO -->
    <div class="qv-overlay" id="qvOverlay" aria-hidden="true">

        <div class="qv-dialog" role="dialog" aria-modal="true" aria-labelledby="qvTitle">

            <button type="button" class="qv-close" id="qvClose" aria-label="Cerrar vista rápida">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="qv-media">
                <div class="qv-frame">
                    <div class="qv-frame-bar">
                        <span id="qvCode">HIVE® / 000</span>
                        <span id="qvSeasonTop"></span>
                    </div>

                    <div class="qv-image">
                        <img id="qvImage" src="" alt="">
                    </div>

                    <div class="qv-frame-bar">
                        <span>VISTA RÁPIDA</span>
                        <span>HIVE® ✦</span>
                    </div>
                </div>
            </div>

            <div class="qv-info">

                <div class="qv-tags" id="qvTags"></div>

                <h2 id="qvTitle"></h2>

                <div class="qv-price" id="qvPrice"></div>

                <p class="qv-desc" id="qvDesc"></p>

                <dl class="qv-specs" id="qvSpecs"></dl>

                <div class="qv-stock" id="qvStock"></div>

                <div class="qv-sizes-block" id="qvSizesBlock">
                    <span class="size-label">ELEGÍ TU TALLE</span>
                    <div class="qv-sizes" id="qvSizes"></div>
                </div>

                <button type="button" class="qv-add" id="qvAdd">
                    AGREGAR A LA BOLSA <i class="fa-solid fa-arrow-right"></i>
                </button>

            </div>

        </div>

    </div>

    <!-- CARRITO -->
    <div class="editorial-drawer" id="cartDrawer">

        <div class="drawer-header">
            <div>
                <span class="drawer-small">HIVE® / TU SELECCIÓN</span>
                <h3>BOLSA DE COMPRAS</h3>
            </div>

            <button id="closeCart">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="drawer-items" id="cartItems">
            <p class="empty-text">TU BOLSA ESTÁ VACÍA.</p>
        </div>

        <div class="drawer-footer">

            <div class="total-row">
                <span>SUBTOTAL</span>
                <span id="cartTotalPrice">$0</span>
            </div>

            <button class="checkout-brutal-btn" id="checkoutBtn">
                FINALIZAR COMPRA POR WHATSAPP
                <i class="fa-brands fa-whatsapp"></i>
            </button>

            <p class="drawer-note">
                GRACIAS POR SER PARTE DE HIVE.
            </p>

        </div>

    </div>

    <div class="drawer-overlay" id="cartOverlay"></div>

    <!-- FOOTER -->
    <footer class="brutal-footer">

        <div class="footer-top">

            <div class="footer-brand">
                <a href="#index">HIVE<span>®</span></a>
                <p>EL FUTURO ES TUYO.</p>
            </div>

            <div class="footer-grid">

                <div>
                    <h4>EXPLORAR</h4>
                    <a href="#index">Inicio</a>
                    <a href="#catalog">Tienda</a>
                    <a href="#manifesto">Nosotros</a>
                </div>

                <div>
                    <h4>SEGUINOS</h4>
                    <a href="#">Instagram ↗</a>
                    <a href="#">TikTok ↗</a>
                    <a href="#">WhatsApp ↗</a>
                </div>

                <div>
                    <h4>UBICACIÓN</h4>
                    <p>Santiago del Estero / Capital, UCSE</p>
                    <p>Argentina</p>
                    <p>Envíos a todo el país</p>
                </div>

            </div>

        </div>

        <div class="footer-bottom">
            <span>© 2026 HIVE® MODA DIGITAL.</span>
            <span>HECHO PARA LA NUEVA GENERACIÓN ✳</span>
        </div>

    </footer>

    <script src="assets/js/script.js"></script>
    <script src="assets/js/filtros.js"></script>
    <script src="assets/js/vista-rapida.js"></script>

</body>
</html>