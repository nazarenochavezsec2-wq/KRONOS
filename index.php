
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

foreach ($lista_productos as $producto) {
    $slug = slugCategoria($producto["categoria"] ?? "");

    if ($slug !== "" && !isset($categorias[$slug])) {
        $categorias[$slug] = $producto["categoria"];
    }
}

$carpetaImagenes = "assets/img/productos/";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HIVE® | Y2K DIGITAL FASHION</title>

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- BARRA SUPERIOR -->
    <div class="brutal-ticker">
        <div class="ticker-content">
            <span>✦ WELCOME TO HIVE DIGITAL WORLD</span>
            <span>✦ Y2K STREETWEAR COLLECTION</span>
            <span>✦ WORLDWIDE SHIPPING</span>
            <span>✦ NEW GENERATION / NEW STYLE</span>
            <span>✦ WELCOME TO HIVE DIGITAL WORLD</span>
            <span>✦ Y2K STREETWEAR COLLECTION</span>
            <span>✦ WORLDWIDE SHIPPING</span>
        </div>
    </div>

    <!-- HEADER -->
    <header class="brutal-header">
        <div class="nav-wrapper">

            <a href="#index" class="brutal-logo">
                HIVE<span>®</span>
                <small>DIGITAL FASHION</small>
            </a>

            <nav class="brutal-nav">
                <a href="#index">HOME</a>
                <a href="#catalog">SHOP</a>
                <a href="#manifesto">ABOUT</a>
            </nav>

            <div class="nav-actions">

                <a href="admin/login.php">
                    <button class="brutal-bag-btn" id="LoginLink">
                        <i class="fa-regular fa-user"></i> LOGIN
                    </button>
                </a>

                <button class="brutal-bag-btn cart-button" id="cartToggle">
                    <i class="fa-solid fa-bag-shopping"></i>
                    BAG [<span id="cartCount">0</span>]
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
                    ONLINE STORE / EST. 2026
                </div>

                <div class="hero-label">
                    <span>THE FUTURE IS NOW</span>
                    <span>VOL. 01 — 2026</span>
                </div>

                <h1>
                    DRESS<br>
                    LIKE<br>
                    <span>THE FUTURE.</span>
                </h1>

                <p>
                    No rules. No limits. Just your own identity.
                    Discover the new generation of streetwear.
                    Welcome to the HIVE universe.
                </p>

                <div class="hero-buttons">
                    <a href="#catalog" class="hero-cta">
                        EXPLORE COLLECTION
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                    <span class="hero-side-text">SCROLL TO DISCOVER ↓</span>
                </div>

                <div class="hero-bottom-info">
                    <div>
                        <span>COLLECTION</span>
                        <strong>Y2K / 001</strong>
                    </div>

                    <div>
                        <span>STYLE</span>
                        <strong>FUTURE STREET</strong>
                    </div>

                    <div>
                        <span>STATUS</span>
                        <strong>AVAILABLE NOW</strong>
                    </div>
                </div>

            </div>

            <div class="hero-image-editorial">

                <div class="hero-image-top">
                    <span>HIVE® EDITORIAL</span>
                    <span>001 / 026</span>
                </div>

                <div class="hero-image-frame">
                    <img src="img/portada.png" alt="HIVE Y2K Collection">
                </div>

                <div class="hero-image-bottom">
                    <span>FUTURE GENERATION</span>
                    <span>EST. MMXXVI ✦</span>
                </div>

                <div class="hero-floating-card">
                    <span>NEW DROP</span>
                    <strong>Y2K<br>ENERGY.</strong>
                    <i class="fa-solid fa-star"></i>
                </div>

            </div>

        </section>

        <!-- BARRA DECORATIVA -->
        <div class="collection-strip">
            <span>HIVE UNIVERSE</span>
            <span>✦</span>
            <span>NEW GENERATION</span>
            <span>✦</span>
            <span>STREET CULTURE</span>
            <span>✦</span>
            <span>Y2K FOREVER</span>
        </div>

        <!-- CATÁLOGO -->
        <section id="catalog" class="brutal-catalog">

            <div class="catalog-intro">

                <div>
                    <span class="section-label">// 01 — THE COLLECTION</span>
                    <h2>FIND YOUR<br><span>NEW IDENTITY.</span></h2>
                </div>

                <div class="catalog-description">
                    <p>
                        Curated pieces for a new generation.
                        Explore the HIVE collection and find your next statement.
                    </p>
                    <span>SELECT YOUR STYLE ↓</span>
                </div>

            </div>

            <div class="catalog-header-bar">

                <div class="catalog-counter">
                    <span class="status-dot"></span>
                    AVAILABLE PRODUCTS
                </div>

                <div class="filter-group">

                    <button class="f-btn active" data-filter="all">
                        ALL PRODUCTS
                    </button>

                    <?php foreach ($categorias as $slug => $nombreCategoria) { ?>

                        <button class="f-btn" data-filter="<?php echo escapar($slug); ?>">
                            <?php echo escapar(mb_strtoupper($nombreCategoria, "UTF-8")); ?>
                        </button>

                    <?php } ?>

                </div>

            </div>

            <div class="brutal-grid" id="productGrid">

                <?php if (count($lista_productos) === 0) { ?>

                    <p class="grid-empty">
                        NO PRODUCTS AVAILABLE AT THE MOMENT.
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
                ?>

                    <article class="brutal-card"
                        data-id="<?php echo escapar($producto['id']); ?>"
                        data-title="<?php echo escapar($producto['nombre']); ?>"
                        data-price="<?php echo $precioFinal; ?>"
                        data-image="<?php echo escapar($imagen); ?>"
                        data-category="<?php echo escapar(slugCategoria($producto['categoria'] ?? '')); ?>">

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

                            <div class="image-hover-text">
                                HIVE / FUTURE GENERATION
                            </div>

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
                                    SELECT SIZE
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
                                        SOLD OUT
                                    </button>

                                <?php } else { ?>

                                    <button type="button" class="brutal-add-btn">
                                        ADD TO BAG <i class="fa-solid fa-arrow-right"></i>
                                    </button>

                                <?php } ?>

                            </div>

                        </div>

                    </article>

                <?php } ?>

            </div>

            <p class="grid-empty" id="noItems" style="display:none;">
                NO PRODUCTS FOUND IN THIS SELECTION.
            </p>

        </section>

        <!-- MANIFIESTO -->
        <section id="manifesto" class="brutal-manifesto">

            <div class="manifesto-visual">
                <span class="manifesto-star">✳</span>
                <span class="manifesto-small">WELCOME TO THE FUTURE</span>
                <h2>BE<br>YOUR<br><span>OWN.</span></h2>
                <span class="manifesto-number">HIVE® / 001</span>
            </div>

            <div class="manifesto-content">

                <span class="manifesto-tag">// 02 — ABOUT HIVE</span>

                <h3>MORE THAN FASHION.<br>A WHOLE NEW ATTITUDE.</h3>

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
                    This is your generation. This is HIVE.
                </p>

                <a href="#catalog" class="manifesto-link">
                    DISCOVER THE COLLECTION
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>

            </div>

        </section>

    </main>

    <!-- CARRITO -->
    <div class="editorial-drawer" id="cartDrawer">

        <div class="drawer-header">
            <div>
                <span class="drawer-small">HIVE® / YOUR SELECTION</span>
                <h3>SHOPPING BAG</h3>
            </div>

            <button id="closeCart">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="drawer-items" id="cartItems">
            <p class="empty-text">YOUR BAG IS EMPTY.</p>
        </div>

        <div class="drawer-footer">

            <div class="total-row">
                <span>SUBTOTAL</span>
                <span id="cartTotalPrice">$0</span>
            </div>

            <button class="checkout-brutal-btn" id="checkoutBtn">
                CHECKOUT VIA WHATSAPP
                <i class="fa-brands fa-whatsapp"></i>
            </button>

            <p class="drawer-note">
                THANK YOU FOR BEING PART OF HIVE.
            </p>

        </div>

    </div>

    <div class="drawer-overlay" id="cartOverlay"></div>

    <!-- FOOTER -->
    <footer class="brutal-footer">

        <div class="footer-top">

            <div class="footer-brand">
                <a href="#index">HIVE<span>®</span></a>
                <p>THE FUTURE BELONGS TO YOU.</p>
            </div>

            <div class="footer-grid">

                <div>
                    <h4>EXPLORE</h4>
                    <a href="#index">Home</a>
                    <a href="#catalog">Shop</a>
                    <a href="#manifesto">About us</a>
                </div>

                <div>
                    <h4>FOLLOW US</h4>
                    <a href="#">Instagram ↗</a>
                    <a href="#">TikTok ↗</a>
                    <a href="#">WhatsApp ↗</a>
                </div>

                <div>
                    <h4>LOCATION</h4>
                    <p>Santiago del Estero / Buenos Aires</p>
                    <p>Argentina</p>
                    <p>Shipping nationwide</p>
                </div>

            </div>

        </div>

        <div class="footer-bottom">
            <span>© 2026 HIVE® DIGITAL FASHION.</span>
            <span>MADE FOR THE NEXT GENERATION ✳</span>
        </div>

    </footer>

    <script src="assets/js/script.js"></script>

</body>
</html>
