<?php
require_once(__DIR__ . "/../../includes/autenticacion.php");
require_once(__DIR__ . "/../../bd.php");
require_once(__DIR__ . "/funciones.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Si el total de la subida supera el límite de PHP, llega todo vacío
    if (empty($_POST) && empty($_FILES) && (int)($_SERVER["CONTENT_LENGTH"] ?? 0) > 0) {
        $error = "Las fotos pesan demasiado en total para el servidor. Subí menos fotos a la vez o ajustá el límite de PHP.";
    } else {
        $archivos = array_filter(
            normalizarArchivos($_FILES["fotos"] ?? []),
            function ($a) { return $a["error"] !== UPLOAD_ERR_NO_FILE; }
        );

        if (empty($archivos)) {
            $error = "Elegí al menos una foto.";
        } else {
            $siguiente = (int)$conexion->query("SELECT COALESCE(MAX(orden), 0) FROM lookbook")->fetchColumn();

            $stmt = $conexion->prepare(
                "INSERT INTO lookbook (imagen, titulo, orden) VALUES (:imagen, NULL, :orden)"
            );

            $guardadas = 0;
            $fallidas  = 0;

            foreach ($archivos as $archivo) {
                $nombre = subirImagenLookbook($archivo);

                if ($nombre) {
                    $siguiente++;
                    $stmt->execute([":imagen" => $nombre, ":orden" => $siguiente]);
                    $guardadas++;
                } else {
                    $fallidas++;
                }
            }

            if ($guardadas > 0 && $fallidas === 0) {
                header("Location: index.php?ok=subido&n=" . $guardadas);
                exit;
            } elseif ($guardadas > 0) {
                header("Location: index.php?ok=parcial&n=" . $guardadas . "&f=" . $fallidas);
                exit;
            } else {
                $error = "No se pudo subir ninguna foto. Usá JPG, PNG o WEBP de hasta 5 MB cada una.";
            }
        }
    }
}
?>
<?php include(__DIR__ . "/../../templates/header.php"); ?>

<section class="admin-welcome">
    <span class="admin-eyebrow">/// LOOKBOOK ///</span>
    <h1>Agregar fotos</h1>
    <p>Podés subir varias fotos a la vez.</p>
</section>

<section class="team-wrap">
    <div class="team-bar">
        <a class="team-pill team-pill--ghost" href="index.php">← Volver al listado</a>
    </div>

    <div class="team-panel team-panel--narrow">
        <span class="team-panel-tag">/// Nuevas fotos</span>
        <h2>Subir al lookbook</h2>

        <form method="post" enctype="multipart/form-data" class="team-form">
            <input type="hidden" name="enviado" value="1">

            <?php if ($error) { ?>
                <p class="team-error"><?= htmlspecialchars($error) ?></p>
            <?php } ?>

            <div class="team-field">
                <label for="lk-fotos">Fotos</label>
                <input type="file" id="lk-fotos" name="fotos[]" required multiple
                       accept="image/jpeg,image/png,image/webp">
                <span class="team-hint">Varias a la vez · JPG, PNG o WEBP · máximo 5 MB cada una</span>
            </div>

            <div class="lk-previews" id="lk-previews"></div>

            <div class="team-form-actions">
                <button type="submit" class="team-pill">Subir fotos</button>
                <a href="index.php" class="team-pill team-pill--ghost">Cancelar</a>
            </div>

        </form>
    </div>
</section>

<script>
(function () {
    var entrada = document.getElementById("lk-fotos");
    var contenedor = document.getElementById("lk-previews");

    entrada.addEventListener("change", function () {
        contenedor.innerHTML = "";

        Array.prototype.forEach.call(entrada.files, function (archivo) {
            if (archivo.type.indexOf("image/") !== 0) return;

            var img = document.createElement("img");
            img.src = URL.createObjectURL(archivo);
            img.alt = "";
            contenedor.appendChild(img);
        });
    });
})();
</script>

<?php include(__DIR__ . "/../../templates/footer.php"); ?>