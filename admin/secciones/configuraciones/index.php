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

// Muestra "—" cuando un campo está vacío
function valorOVacio($valor) {
    return trim((string)$valor) !== "" ? escapar($valor) : "—";
}

$config = cargarConfiguracion($conexion); 

$frasesTicker = lineasConfig($config["ticker_frases"]); 
$frasesFranja = lineasConfig($config["franja_frases"]); 

$imagenPortada = ($config["hero_imagen"] !== "") 
    ? "/Kronos/assets/img/portada/" . $config["hero_imagen"] 
    : "/Kronos/img/portada.png"; 

include("../../templates/header.php"); 
?>

<section class="admin-page-head">
    <span class="admin-eyebrow">/// SECCIÓN</span>
    <h1>Configuraciones</h1>
</section>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Portada de la tienda</span>
        <a class="btn btn-primary" href="editar.php">Editar configuración</a> 
    </div>
    <div class="card-body">

        <h2 class="admin-form-section-title">Barra superior (ticker)</h2>
        <div class="admin-config-row">
            <div class="admin-config-label">Frases</div>
            <div class="admin-config-value">
                <?php if (count($frasesTicker) > 0) { ?>
                    <?php foreach ($frasesTicker as $frase) { ?>
                        <div>✦ <?php echo escapar($frase); ?></div>
                    <?php } ?>
                <?php } else { ?>
                    — (la barra no se muestra)
                <?php } ?>
            </div>
        </div>

        <h2 class="admin-form-section-title">Información del hero</h2>
        <?php for ($n = 1; $n <= 3; $n++) { ?>
            <div class="admin-config-row">
                <div class="admin-config-label"><?php echo valorOVacio($config["hero_info_" . $n . "_etiqueta"]); ?></div>
                <div class="admin-config-value"><?php echo valorOVacio($config["hero_info_" . $n . "_valor"]); ?></div>
            </div>
        <?php } ?>

        <h2 class="admin-form-section-title">Imagen de portada</h2>
        <div class="admin-config-row">
            <div class="admin-config-label">Imagen</div>
            <div class="admin-config-value">
                <img 
                src="<?php echo escapar($imagenPortada); ?>" 
                alt="Imagen de portada" 
                class="img-thumbnail" 
                width="160" 
                > 
                <p class="admin-file-name mt-2 mb-0"> 
                    <?php echo ($config["hero_imagen"] !== "") ? escapar($config["hero_imagen"]) : "portada original (img/portada.png)"; ?> 
                </p> 
            </div>
        </div>
        <div class="admin-config-row">
            <div class="admin-config-label">Texto arriba izquierda</div>
            <div class="admin-config-value"><?php echo valorOVacio($config["hero_img_sup_izq"]); ?></div>
        </div>
        <div class="admin-config-row">
            <div class="admin-config-label">Texto arriba derecha</div>
            <div class="admin-config-value"><?php echo valorOVacio($config["hero_img_sup_der"]); ?></div>
        </div>
        <div class="admin-config-row">
            <div class="admin-config-label">Texto abajo izquierda</div>
            <div class="admin-config-value"><?php echo valorOVacio($config["hero_img_inf_izq"]); ?></div>
        </div>
        <div class="admin-config-row">
            <div class="admin-config-label">Texto abajo derecha</div>
            <div class="admin-config-value"><?php echo valorOVacio($config["hero_img_inf_der"]); ?></div>
        </div>

        <h2 class="admin-form-section-title">Tarjeta flotante</h2>
        <div class="admin-config-row">
            <div class="admin-config-label">Etiqueta</div>
            <div class="admin-config-value"><?php echo valorOVacio($config["hero_card_etiqueta"]); ?></div>
        </div>
        <div class="admin-config-row">
            <div class="admin-config-label">Título</div>
            <div class="admin-config-value"><?php echo nl2br(valorOVacio($config["hero_card_titulo"])); ?></div>
        </div>

        <h2 class="admin-form-section-title">Franja decorativa</h2>
        <div class="admin-config-row">
            <div class="admin-config-label">Frases</div>
            <div class="admin-config-value">
                <?php echo (count($frasesFranja) > 0) ? implode(" ✦ ", array_map("escapar", $frasesFranja)) : "— (la franja no se muestra)"; ?>
            </div>
        </div>

    </div>
</div>

<?php include("../../templates/footer.php"); ?>