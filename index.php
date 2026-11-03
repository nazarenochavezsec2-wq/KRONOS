<?php
require_once(__DIR__ . "/admin/bd.php");
require_once(__DIR__ . "/admin/includes/configuracion.php");

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

// Obtener fotos del lookbook
$lookbook = [];

try {
    $sentencia = $conexion->prepare("SELECT id, imagen, titulo FROM lookbook ORDER BY orden ASC, id DESC");
    $sentencia->execute();
    $lookbook = $sentencia->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $lookbook = []; // si la tabla no existe, la sección simplemente no se muestra
}

$carpetaEquipo = "assets/img/equipo/";
$carpetaLookbook = "assets/img/lookbook/";
$carpetaImagenes = "assets/img/productos/";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$logueado = !empty($_SESSION["autenticado"]);

// Configuración editable de la portada (textos e imagen del panel)
$config = cargarConfiguracion($conexion);

$frasesTicker = lineasConfig($config["ticker_frases"]);
$frasesFranja = lineasConfig($config["franja_frases"]);

// Si no se subió ninguna imagen desde el panel, se usa la portada de siempre
$imagenPortada = ($config["hero_imagen"] !== "")
    ? "assets/img/portada/" . $config["hero_imagen"]
    : "img/portada.png";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HIVE® | MODA DIGITAL Y2K</title>

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-carrusel.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- BARRA SUPERIOR -->
    <?php if (count($frasesTicker) > 0) { ?>
        <div class="brutal-ticker">
            <div class="ticker-content">
                <?php for ($i = 0; $i < 4; $i++) { ?>
                    <?php foreach ($frasesTicker as $frase) { ?>
                        <span>✦ <?php echo escapar($frase); ?></span>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

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
                <?php if (count($lookbook) > 0) { ?>
                    <a href="#lookbook">LOOKBOOK</a>
                <?php } ?>
                <a href="#manifesto">NOSOTROS</a>
                <?php if (count($equipo) > 0) { ?>
                    <a href="#equipo">EQUIPO</a>
                <?php } ?>
            </nav>

            <div class="nav-actions">

                <?php if ($logueado): ?>
                    <span class="user-badge" style="display: inline-flex; align-items: center; gap: 6px; font-family: 'Space Mono', monospace; font-size: 0.85rem; font-weight: 700; color: var(--text-main);">
                        <i class="fa-solid fa-user"></i> Hola, <?= htmlspecialchars($_SESSION["usuario"] ?? "", ENT_QUOTES, 'UTF-8') ?>
                    </span>

                    <?php if (($_SESSION["rol"] ?? "") === "admin"): ?>
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
                    <?php for ($n = 1; $n <= 3; $n++) {
                        $etiqueta = $config["hero_info_" . $n . "_etiqueta"];
                        $valor    = $config["hero_info_" . $n . "_valor"];

                        if ($etiqueta === "" && $valor === "") {
                            continue;
                        } ?>
                        <div>
                            <span><?php echo escapar($etiqueta); ?></span>
                            <strong><?php echo escapar($valor); ?></strong>
                        </div>
                    <?php } ?>
                </div>

            </div>

            <div class="hero-image-editorial">

                <div class="hero-image-top">
                    <span><?php echo escapar($config["hero_img_sup_izq"]); ?></span>
                    <span><?php echo escapar($config["hero_img_sup_der"]); ?></span>
                </div>

                <div class="hero-image-frame">
                    <img src="<?php echo escapar($imagenPortada); ?>" alt="Portada HIVE">
                </div>

                <div class="hero-image-bottom">
                    <span><?php echo escapar($config["hero_img_inf_izq"]); ?></span>
                    <span><?php echo escapar($config["hero_img_inf_der"]); ?></span>
                </div>

                <div class="hero-floating-card">
                    <span><?php echo escapar($config["hero_card_etiqueta"]); ?></span>
                    <strong><?php echo nl2br(escapar($config["hero_card_titulo"])); ?></strong>
                    <i class="fa-solid fa-star"></i>
                </div>

            </div>

        </section>

        <!-- BARRA DECORATIVA -->
        <?php if (count($frasesFranja) > 0) { ?>
            <div class="collection-strip">
                <?php foreach ($frasesFranja as $i => $frase) { ?>
                    <?php if ($i > 0) { ?>
                        <span>✦</span>
                    <?php } ?>
                    <span><?php echo escapar($frase); ?></span>
                <?php } ?>
            </div>
        <?php } ?>

        <!-- CARRUSEL DE NUEVOS INGRESOS (Bootstrap) -->
        <?php
        $novedades = array_slice($lista_productos, 0, 5);
        if (count($novedades) > 0) {
        ?>
        <section id="novedades" class="novedades">

            <div class="catalog-intro">
                <div>
                    <span class="section-label">// NUEVOS INGRESOS</span>
                    <h2>LO ÚLTIMO<br><span>EN HIVE®.</span></h2>
                </div>
            </div>

            <div id="carruselNovedades" class="carousel slide novedades-carousel" data-bs-ride="carousel" data-bs-interval="5000" aria-label="Nuevos ingresos">

                <div class="carousel-indicators">
                    <?php foreach ($novedades as $n => $nov) { ?>
                        <button type="button" data-bs-target="#carruselNovedades" data-bs-slide-to="<?php echo $n; ?>"
                            class="<?php echo $n === 0 ? 'active' : ''; ?>"
                            <?php echo $n === 0 ? 'aria-current="true"' : ''; ?>
                            aria-label="Diapositiva <?php echo $n + 1; ?>"></button>
                    <?php } ?>
                </div>

                <div class="carousel-inner">
                    <?php foreach ($novedades as $n => $nov) {
                        $pBase = (int)$nov["precio_base"];
                        $pDesc = $nov["precio_descuento"];
                        $conDesc = ($pDesc !== null && $pDesc !== "" && (int)$pDesc > 0 && (int)$pDesc < $pBase);
                        $pFinal = $conDesc ? (int)$pDesc : $pBase;
                    ?>
                        <div class="carousel-item <?php echo $n === 0 ? 'active' : ''; ?>">
                            <div class="novedad-slide">

                                <div class="novedad-foto">
                                    <img src="<?php echo escapar($carpetaImagenes . $nov['imagen']); ?>"
                                        alt="<?php echo escapar($nov['nombre']); ?>"
                                        <?php echo $n > 0 ? 'loading="lazy"' : ''; ?>>
                                </div>

                                <div class="novedad-info">
                                    <span class="novedad-num">HIVE® / <?php echo str_pad($nov["id"], 3, "0", STR_PAD_LEFT); ?></span>
                                    <?php if (!empty($nov["categoria"])) { ?>
                                        <span class="novedad-cat"><?php echo escapar(mb_strtoupper($nov["categoria"], "UTF-8")); ?></span>
                                    <?php } ?>
                                    <h3><?php echo escapar($nov["nombre"]); ?></h3>
                                    <p class="novedad-precio">
                                        <?php echo formatoPrecio($pFinal); ?>
                                        <?php if ($conDesc) { ?>
                                            <s><?php echo formatoPrecio($pBase); ?></s>
                                        <?php } ?>
                                    </p>
                                    <a href="#catalog" class="hero-cta">VER EN LA COLECCIÓN ↓</a>
                                </div>

                            </div>
                        </div>
                    <?php } ?>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carruselNovedades" data-bs-slide="prev">
                    <span class="novedad-flecha" aria-hidden="true">←</span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carruselNovedades" data-bs-slide="next">
                    <span class="novedad-flecha" aria-hidden="true">→</span>
                    <span class="visually-hidden">Siguiente</span>
                </button>

            </div>

        </section>
        <?php } ?>

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

        <!-- LOOKBOOK -->
        <?php if (count($lookbook) > 0) { ?>

        <section id="lookbook" class="brutal-look">

            <div class="catalog-intro">

                <div>
                    <span class="section-label">// 02 — LOOKBOOK</span>
                    <h2>EL FUTURO<br><span>SE VE ASÍ.</span></h2>
                </div>

                <div class="catalog-description">
                    <p>
                        Fotos, outfits y actitud. Inspirate con los looks
                        de la colección HIVE.
                    </p>
                    <span>TOCÁ UNA FOTO PARA AMPLIARLA ✦</span>
                </div>

            </div>

            <div class="look-grid">

                <?php foreach ($lookbook as $i => $foto) {
                    $rutaFoto = $carpetaLookbook . $foto["imagen"];
                    $leyenda  = trim((string)($foto["titulo"] ?? ""));
                ?>

                    <button type="button" class="look-item"
                        data-full="<?php echo escapar($rutaFoto); ?>"
                        data-caption="<?php echo escapar($leyenda); ?>">

                        <span class="look-frame">

                            <span class="look-bar">
                                <span>LOOK / <?php echo str_pad($i + 1, 2, "0", STR_PAD_LEFT); ?></span>
                                <span>HIVE® ✦</span>
                            </span>

                            <span class="look-photo">
                                <img
                                    src="<?php echo escapar($rutaFoto); ?>"
                                    alt="<?php echo escapar($leyenda !== '' ? $leyenda : 'Look HIVE'); ?>"
                                    decoding="async"
                                    <?php echo ($i >= 6) ? 'loading="lazy"' : ''; ?>
                                >
                            </span>

                            <?php if ($leyenda !== "") { ?>
                                <span class="look-caption"><?php echo escapar($leyenda); ?></span>
                            <?php } ?>

                        </span>

                    </button>

                <?php } ?>

            </div>

        </section>

        <?php } ?>

        <!-- MANIFIESTO -->
        <section id="manifesto" class="brutal-manifesto">

            <div class="manifesto-visual">
                <span class="manifesto-star">✳</span>
                <span class="manifesto-small">BIENVENIDOS AL FUTURO</span>
                <h2>SÉ<br>TU<br><span>MISMO.</span></h2>
                <span class="manifesto-number">HIVE® / 001</span>
            </div>

            <div class="manifesto-content">

                <span class="manifesto-tag">// 03 — SOBRE HIVE</span>

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
                    <span class="section-label">// 04 — EL EQUIPO</span>
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

    <!-- VISOR DEL LOOKBOOK -->
    <?php if (count($lookbook) > 0) { ?>
    <div class="lb-overlay" id="lbOverlay" aria-hidden="true">

        <button type="button" class="lb-close" id="lbClose" aria-label="Cerrar visor">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <button type="button" class="lb-nav lb-prev" id="lbPrev" aria-label="Foto anterior">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <figure class="lb-figure">
            <div class="lb-frame">
                <img id="lbImg" src="" alt="">
            </div>
            <figcaption id="lbCaption"></figcaption>
        </figure>

        <button type="button" class="lb-nav lb-next" id="lbNext" aria-label="Foto siguiente">
            <i class="fa-solid fa-arrow-right"></i>
        </button>

    </div>
    <?php } ?>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/filtros.js"></script>
    <script src="assets/js/vista-rapida.js"></script>
    <script src="assets/js/lookbook.js"></script>

</body>
</html>