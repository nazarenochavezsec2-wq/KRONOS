<?php 
require_once(__DIR__ . "/../../includes/autenticacion.php"); 

include("../../bd.php"); 

require_once(__DIR__ . "/../../includes/configuracion.php"); 

// Escapa texto antes de imprimirlo en pantalla
if (!function_exists("escapar")) {
    function escapar($valor) {
        return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
    }
}

// Dibuja un campo de texto simple (evita repetir el mismo bloque 12 veces)
function campoTexto($clave, $etiqueta, $config, $limites, $columna = "col-md-6", $placeholder = "") {
    ?>
    <div class="<?php echo $columna; ?>">
        <label for="<?php echo $clave; ?>" class="form-label"><?php echo $etiqueta; ?></label>
        <input
            type="text"
            class="form-control"
            name="<?php echo $clave; ?>"
            id="<?php echo $clave; ?>"
            value="<?php echo escapar($config[$clave]); ?>"
            maxlength="<?php echo (int)$limites[$clave]; ?>"
            placeholder="<?php echo escapar($placeholder); ?>"
        />
    </div>
    <?php
}

$config = cargarConfiguracion($conexion); 
$limites = configuracionLimites(); 
$mensajeError = ""; 

// Carpeta (en el disco) donde se guardan las imágenes de portada 
$carpetaPortada = "../../../assets/img/portada/"; 

if ($_POST) { 

    // Capturamos los textos (solo los campos permitidos) 
    $datos = []; 
    foreach ($limites as $clave => $maximo) { 
        $valor = isset($_POST[$clave]) ? trim($_POST[$clave]) : ""; 
        $valor = str_replace("\r\n", "\n", $valor); 
        $datos[$clave] = mb_substr($valor, 0, $maximo, "UTF-8"); 
    } 

    // La imagen actual se lee de la BD (no se confía en el formulario) 
    $imagenAnterior = $config["hero_imagen"]; 
    $imagen = $imagenAnterior; 

    // Si se subió una imagen nueva, la validamos y la guardamos 
    if ( isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK ) { 
        $extension = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION)); 
        $permitidas = ["jpg", "jpeg", "png", "webp", "gif"]; 

        if ( !in_array($extension, $permitidas, true) || @getimagesize($_FILES["imagen"]["tmp_name"]) === false ) { 
            $mensajeError = "La imagen debe ser un archivo JPG, PNG, WEBP o GIF válido."; 
        } else { 
            if (!is_dir($carpetaPortada)) { 
                mkdir($carpetaPortada, 0777, true); 
            } 

            // Nombre propio (evita nombres raros y que el navegador muestre una imagen vieja en caché) 
            $nuevoNombre = "portada_" . time() . "." . $extension; 

            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $carpetaPortada . $nuevoNombre)) { 
                $imagen = $nuevoNombre; 
            } else { 
                $mensajeError = "No se pudo guardar la imagen en el servidor."; 
            } 
        } 
    } elseif ( isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] !== UPLOAD_ERR_NO_FILE ) { 
        $mensajeError = "Ocurrió un error al subir la imagen."; 
    } 

    if ($mensajeError === "") { 

        // REPLACE guarda la fila única (id = 1), exista o no todavía 
        $columnas = array_keys($datos); 
        $columnas[] = "hero_imagen"; 

        $sentencia = $conexion->prepare( 
            "REPLACE INTO tbl_configuraciones (id, " . implode(", ", $columnas) . ") 
             VALUES (1, :" . implode(", :", $columnas) . ")" 
        ); 

        foreach ($datos as $clave => $valor) { 
            $sentencia->bindValue(":" . $clave, $valor); 
        } 
        $sentencia->bindValue(":hero_imagen", $imagen); 

        $sentencia->execute(); 

        // Borramos la imagen anterior si fue reemplazada (solo dentro de la carpeta de portadas) 
        if ($imagenAnterior !== "" && $imagenAnterior !== $imagen) { 
            $rutaAnterior = $carpetaPortada . basename($imagenAnterior); 
            if (is_file($rutaAnterior)) { 
                unlink($rutaAnterior); 
            } 
        } 

        header("Location: index.php?mensaje=actualizado"); 
        exit; 
    } 

    // Si hubo un error, volvemos a mostrar el formulario con lo que escribió 
    $config = array_merge($config, $datos); 
} 

$imagenPortada = ($config["hero_imagen"] !== "") 
    ? "/Kronos/assets/img/portada/" . $config["hero_imagen"] 
    : "/Kronos/img/portada.png"; 

include("../../templates/header.php"); 
?> 

<section class="admin-page-head">
    <span class="admin-eyebrow">/// CONFIGURACIONES</span>
    <h1>Editar portada</h1>
</section>

<?php if ($mensajeError !== "") { ?>
    <div class="alert alert-danger" role="alert">
        <?php echo escapar($mensajeError); ?>
    </div>
<?php } ?>

<div class="card">
    <div class="card-header">Textos e imagen de la portada</div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">

            <!-- BARRA SUPERIOR -->
            <h2 class="admin-form-section-title">Barra superior (ticker)</h2>
            <div class="mb-3">
                <label for="ticker_frases" class="form-label">Frases</label>
                <textarea 
                class="form-control" 
                name="ticker_frases" 
                id="ticker_frases" 
                rows="5" 
                maxlength="<?php echo (int)$limites['ticker_frases']; ?>" 
                placeholder="Una frase por línea"
                ><?php echo escapar($config["ticker_frases"]); ?></textarea>
                <div class="form-text">Una frase por línea. El símbolo ✦ se agrega solo. Si lo dejás vacío, la barra no se muestra.</div>
            </div>

            <!-- INFORMACIÓN DEL HERO -->
            <h2 class="admin-form-section-title">Información del hero</h2>
            <?php for ($n = 1; $n <= 3; $n++) { ?>
                <div class="row g-3 mb-3">
                    <?php campoTexto("hero_info_" . $n . "_etiqueta", "Dato " . $n . " — etiqueta", $config, $limites, "col-md-4", "Ej: COLECCIÓN"); ?>
                    <?php campoTexto("hero_info_" . $n . "_valor", "Dato " . $n . " — valor", $config, $limites, "col-md-8", "Ej: Y2K / 001"); ?>
                </div>
            <?php } ?>

            <!-- IMAGEN DE PORTADA -->
            <h2 class="admin-form-section-title">Imagen de portada</h2>
            <div class="mb-3"> 
                <label class="form-label">Imagen actual</label> 
                <br> 
                <img 
                src="<?php echo escapar($imagenPortada); ?>" 
                alt="Imagen de portada actual" 
                class="img-thumbnail mb-2" 
                width="160" 
                > 
                <p class="admin-file-name"> 
                    <?php echo ($config["hero_imagen"] !== "") ? escapar($config["hero_imagen"]) : "portada original (img/portada.png)"; ?> 
                </p> 
                <label for="imagen" class="form-label"> 
                    Seleccionar una nueva imagen 
                </label> 
                <input 
                type="file" 
                class="form-control" 
                name="imagen" 
                id="imagen" 
                accept="image/*" 
                > 
                <div class="form-text">JPG, PNG, WEBP o GIF. Si no elegís ninguna, se mantiene la actual.</div>
            </div>

            <div class="row g-3 mb-3">
                <?php campoTexto("hero_img_sup_izq", "Texto arriba izquierda", $config, $limites, "col-md-6", "Ej: HIVE® EDITORIAL"); ?>
                <?php campoTexto("hero_img_sup_der", "Texto arriba derecha", $config, $limites, "col-md-6", "Ej: 001 / 026"); ?>
            </div>

            <div class="row g-3 mb-3">
                <?php campoTexto("hero_img_inf_izq", "Texto abajo izquierda", $config, $limites, "col-md-6", "Ej: GENERACIÓN FUTURO"); ?>
                <?php campoTexto("hero_img_inf_der", "Texto abajo derecha", $config, $limites, "col-md-6", "Ej: DESDE MMXXVI ✦"); ?>
            </div>

            <!-- TARJETA FLOTANTE -->
            <h2 class="admin-form-section-title">Tarjeta flotante</h2>
            <div class="row g-3 mb-3">
                <?php campoTexto("hero_card_etiqueta", "Etiqueta", $config, $limites, "col-md-6", "Ej: NUEVO DROP"); ?>

                <div class="col-md-6">
                    <label for="hero_card_titulo" class="form-label">Título</label>
                    <textarea 
                    class="form-control" 
                    name="hero_card_titulo" 
                    id="hero_card_titulo" 
                    rows="2" 
                    maxlength="<?php echo (int)$limites['hero_card_titulo']; ?>" 
                    placeholder="ENERGÍA&#10;Y2K."
                    ><?php echo escapar($config["hero_card_titulo"]); ?></textarea>
                    <div class="form-text">Cada línea es un salto de línea en la tarjeta.</div>
                </div>
            </div>

            <!-- FRANJA DECORATIVA -->
            <h2 class="admin-form-section-title">Franja decorativa</h2>
            <div class="mb-3">
                <label for="franja_frases" class="form-label">Frases</label>
                <textarea 
                class="form-control" 
                name="franja_frases" 
                id="franja_frases" 
                rows="4" 
                maxlength="<?php echo (int)$limites['franja_frases']; ?>" 
                placeholder="Una frase por línea"
                ><?php echo escapar($config["franja_frases"]); ?></textarea>
                <div class="form-text">Una frase por línea. El símbolo ✦ entre frases se agrega solo. Si lo dejás vacío, la franja no se muestra.</div>
            </div>

            <div class="admin-form-actions">
                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Actualizar 
                </button>

                <a class="btn btn-secondary" href="index.php">Cancelar</a>
            </div>
            
        </form>
    </div>
</div>
<?php include("../../templates/footer.php"); ?>