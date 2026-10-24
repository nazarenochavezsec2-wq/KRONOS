<?php 
// Conexión a la base de datos (la misma que usa el panel administrativo)
require_once(__DIR__ . "/admin/bd.php"); 

// Escapa texto antes de imprimirlo en pantalla
function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}

// Formatea precios: 48900 -> $48.900
function formatoPrecio($valor) {
    return "$" . number_format((float)$valor, 0, ",", ".");
}

// Convierte una categoría en un identificador para los filtros: "Pantalones Cargo" -> "pantalones-cargo"
function slugCategoria($texto) {
    $slug = mb_strtolower(trim((string)$texto), "UTF-8");
    $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', $slug);
    return trim($slug, "-");
}

// Solo se muestran los productos activos (estado = 1)
$sentencia = $conexion->prepare( "SELECT p.*, c.nombre AS categoria, t.nombre AS temporada FROM tbl_productos p LEFT JOIN tbl_categorias c ON c.id = p.id_categoria LEFT JOIN tbl_temporadas t ON t.id = p.id_temporada WHERE p.estado = 1 ORDER BY p.id DESC" ); 
$sentencia->execute(); 
$lista_productos = $sentencia->fetchAll(PDO::FETCH_ASSOC); 

// Categorías únicas para armar los botones de filtro
$categorias = []; 
foreach ($lista_productos as $producto) { 
    $slug = slugCategoria($producto["categoria"]); 
    if ($slug !== "" && !isset($categorias[$slug])) { 
        $categorias[$slug] = $producto["categoria"]; 
    } 
} 

// Carpeta donde el panel guarda las imágenes de los productos
$carpetaImagenes = "assets/img/productos/"; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HIVE // MINIMAL ARCHIVE</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- TICKER SUPERIOR -->
    <div class="brutal-ticker">
        <div class="ticker-content">
            <span>[ SYSTEM_ONLINE // MINIMAL Y2K ARCHIVE ]</span>
            <span>[ WORLDWIDE SECURE SHIPPING ]</span>
            <span>[ LIMITED DROP 1/100 ]</span>
            <span>[ SYSTEM_ONLINE // MINIMAL Y2K ARCHIVE ]</span>
            <span>[ WORLDWIDE SECURE SHIPPING ]</span>
        </div>
    </div>

    <!-- HEADER / NAVEGACIÓN -->
    <header class="brutal-header">
        <div class="nav-wrapper">
            <a href="#" class="brutal-logo">HIVE</a>
            <nav class="brutal-nav">
                <a href="#index">01. INDEX</a>
                <a href="#catalog">02. CATÁLOGO</a>
                <a href="#manifesto">03. MANIFIESTO</a>
            </nav>
            <div class="nav-actions">
                <a href="admin/login.php">
                    <button class="brutal-bag-btn" id="LoginLink">
                    LOGIN 
                    </button>
                </a>

                <button class="brutal-bag-btn" id="cartToggle">
                    BAG [<span id="cartCount">0</span>]
                </button>
            </div>
        </div>
    </header>

    <!-- SECCIÓN HERO -->
    <section id="index" class="editorial-hero">
        <div class="editorial-title-box">
            <span class="sub-heading">SELECTION 2026 // MINIMAL DROP</span>
            <h1>RAW <br>CYBER <br>FORM</h1>
            <p>Indumentaria masculina de moldería estructural pesada con acabados minimalistas y estéticos. Diseñada para priorizar la silueta y la pureza visual.</p>
            
            
        </div>
        <div class="hero-image-editorial">
            <img src="img/portada.png" alt="Editorial Cover">
            <div class="image-caption">
                <span>FIG. 01 — MINIMAL CHROME STRUCTURE</span>
                <span>ARCHIVE.SYS</span>
            </div>
        </div>
    </section>

    <!-- CATÁLOGO -->
    <section id="catalog" class="brutal-catalog">
        <div class="catalog-header-bar">
            <h2>CATÁLOGO // SELECCIÓN</h2>
            <div class="filter-group">
                <button class="f-btn active" data-filter="all">ALL</button>
                <?php foreach ($categorias as $slug => $nombreCategoria) { ?>
                    <button class="f-btn" data-filter="<?php echo escapar($slug); ?>"><?php echo escapar(mb_strtoupper($nombreCategoria, "UTF-8")); ?></button>
                <?php } ?>
            </div>
        </div>

        <div class="brutal-grid" id="productGrid">

            <?php if (count($lista_productos) === 0) { ?>
                <p class="grid-empty">NO ITEMS AVAILABLE AT THE MOMENT.</p>
            <?php } ?>

            <?php foreach ($lista_productos as $producto) { 

                $precioBase = (int)$producto["precio_base"]; 
                $precioDescuento = $producto["precio_descuento"]; 

                // Hay descuento si existe, es mayor a 0 y es menor al precio base 
                $tieneDescuento = ($precioDescuento !== null && $precioDescuento !== "" 
                    && (int)$precioDescuento > 0 && (int)$precioDescuento < $precioBase); 

                $precioFinal = $tieneDescuento ? (int)$precioDescuento : $precioBase; 
                $porcentaje = $tieneDescuento ? round((1 - ((int)$precioDescuento / $precioBase)) * 100) : 0; 

                $sinStock = ((int)$producto["stock"] <= 0); 
                $imagen = $carpetaImagenes . $producto["imagen"]; 

                // Talles 
                $talles = ["S", "M", "L", "XL"]; 
                if (!empty($producto["talles"])) { 
                    $lista = array_values(array_filter(array_map("trim", explode(",", $producto["talles"])))); 
                    if (count($lista) > 0) { 
                        $talles = $lista; 
                    } 
                } 
            ?>
            <div class="brutal-card" 
                 data-id="<?php echo escapar($producto['id']); ?>" 
                 data-title="<?php echo escapar($producto['nombre']); ?>" 
                 data-price="<?php echo $precioFinal; ?>" 
                 data-image="<?php echo escapar($imagen); ?>" 
                 data-category="<?php echo escapar(slugCategoria($producto['categoria'])); ?>">

                <div class="card-img-wrap">
                    <?php if (!empty($producto["categoria"])) { ?>
                        <span class="brutal-badge"><?php echo escapar(mb_strtoupper($producto["categoria"], "UTF-8")); ?></span>
                    <?php } ?>
                    <?php if ($tieneDescuento) { ?>
                        <span class="brutal-badge brutal-badge-sale">-<?php echo $porcentaje; ?>%</span>
                    <?php } ?>
                    <img src="<?php echo escapar($imagen); ?>" alt="<?php echo escapar($producto['nombre']); ?>">
                </div>

                <div class="card-body">
                    <div class="card-meta">
                        <h3 class="card-title"><?php echo escapar($producto["nombre"]); ?></h3>
                        <?php if (!empty($producto["temporada"])) { ?>
                            <div class="card-season"><?php echo escapar(mb_strtoupper($producto["temporada"], "UTF-8")); ?></div>
                        <?php } ?>
                        <div class="card-price">
                            <?php if ($tieneDescuento) { ?>
                                <span class="card-price-old"><?php echo formatoPrecio($precioBase); ?></span>
                            <?php } ?>
                            <?php echo formatoPrecio($precioFinal); ?>
                        </div>
                    </div>

                    <div class="card-actions">
                        <div class="brutal-sizes">
                            <?php foreach ($talles as $i => $talle) { ?>
                                <button type="button" class="s-btn <?php echo ($i === 0) ? 'active' : ''; ?>"><?php echo escapar($talle); ?></button>
                            <?php } ?>
                        </div>

                        <?php if ($sinStock) { ?>
                            <button type="button" class="brutal-add-btn" disabled>SOLD OUT</button>
                        <?php } else { ?>
                            <button type="button" class="brutal-add-btn">ADD TO BAG</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>

        </div>

        <p class="grid-empty" id="noItems" style="display:none;">NO ITEMS FOUND IN THIS SELECTION.</p>
    </section>

    <!-- MANIFIESTO -->
    <section id="manifesto" class="brutal-manifesto">
        <div class="manifesto-content">
            <span class="manifesto-tag">// STATEMENT.SYS</span>
            <h3>LA ROPA COMO ARQUITECTURA PORTÁTIL.</h3>
            <p>Rechazamos la temporalidad rápida. Cada prenda de la serie utiliza algodón de alta densidad y molderías amplias que transforman la silueta humana en un bloque escultórico limpio.</p>
        </div>
    </section>

    <!-- CARRITO / DRAWER -->
    <div class="editorial-drawer" id="cartDrawer">
        <div class="drawer-header">
            <h3>SHOPPING BAG</h3>
            <button id="closeCart"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="drawer-items" id="cartItems">
            <p class="empty-text">BAG IS EMPTY.</p>
        </div>
        <div class="drawer-footer">
            <div class="total-row">
                <span>SUBTOTAL:</span>
                <span id="cartTotalPrice">$0</span>
            </div>
            <button class="checkout-brutal-btn" id="checkoutBtn">
                PROCESS ORDER VIA WHATSAPP
            </button>
        </div>
    </div>
    <div class="drawer-overlay" id="cartOverlay"></div>

    <!-- FOOTER -->
    <footer class="brutal-footer">
        <div class="footer-grid">
            <div>
                <h4>HIVE CHROME ARCHIVE</h4>
                <p>Santiago del Estero / Buenos Aires, ARG.</p>
                <p>Envios a Todo el Pais</p>
            </div>
            <div>
                <h4>NAVEGACIÓN</h4>
                <a href="#index">Home</a>
                <a href="#catalog">Catalogue</a>
                <a href="#manifesto">Manifesto</a>
            </div>
            <div>
                <h4>SOCIAL</h4>
                <a href="#">Instagram</a>
                <a href="#">TikTok</a>
                <a href="#">WhatsApp</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; 2026 HIVE CHROME ARCHIVE. ALL RIGHTS RESERVED.</span>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>