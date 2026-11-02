<?php
require_once(__DIR__ . "/../../includes/autenticacion.php");
require_once(__DIR__ . "/../../bd.php");
require_once(__DIR__ . "/funciones.php");

$id = (int)($_GET["id"] ?? $_POST["id"] ?? 0);

$stmt = $conexion->prepare("SELECT * FROM lookbook WHERE id = :id");
$stmt->execute([":id" => $id]);
$registro = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe, volvemos al listado
if (!$registro) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"] ?? "");
    $orden  = (int)($_POST["orden"] ?? 0);

    if (mb_strlen($titulo) > 100) {
        $error = "El título puede tener hasta 100 caracteres.";
    } elseif ($orden < 0) {
        $error = "La posición no puede ser negativa.";
    } else {
        $nueva = subirImagenLookbook($_FILES["foto"] ?? null);

        if ($nueva === false) {
            $error = "No se pudo subir la foto. Usá un JPG, PNG o WEBP de hasta 5 MB.";
        } else {
            $imagen = $registro["imagen"];

            // Si subió una foto nueva, borramos la anterior
            if ($nueva !== null) {
                borrarImagenLookbook($imagen);
                $imagen = $nueva;
            }

            $stmt = $conexion->prepare(
                "UPDATE lookbook SET imagen = :imagen, titulo = :titulo, orden = :orden WHERE id = :id"
            );
            $stmt->execute([
                ":imagen" => $imagen,
                ":titulo" => ($titulo === "" ? null : $titulo),
                ":orden"  => $orden,
                ":id"     => $id,
            ]);
            header("Location: index.php?ok=editado");
            exit;
        }
    }

    // Si hubo error, mantenemos lo que escribió la persona
    $registro["titulo"] = $titulo;
    $registro["orden"]  = $orden;
}
?>
<?php include(__DIR__ . "/../../templates/header.php"); ?>

<section class="admin-welcome">
    <span class="admin-eyebrow">/// LOOKBOOK ///</span>
    <h1>Editar foto</h1>
    <p>Cambiá el título, la posición o reemplazá la foto.</p>
</section>

<section class="team-wrap">
    <div class="team-bar">
        <a class="team-pill team-pill--ghost" href="index.php">← Volver al listado</a>
    </div>

    <div class="team-editor">

        <!-- Vista previa -->
        <div class="team-preview">
            <div class="team-preview-top">
                <span>Vista previa</span>
                <span>ID <?= str_pad($id, 2, "0", STR_PAD_LEFT) ?></span>
            </div>
            <div class="team-preview-frame">
                <img id="lk-preview"
                     src="/Kronos/assets/img/lookbook/<?= htmlspecialchars($registro["imagen"]) ?>" alt="">
            </div>
            <div class="team-preview-bottom">
                <span>HIVE® LOOKBOOK</span>
                <span>✦</span>
            </div>
        </div>

        <!-- Formulario -->
        <div class="team-panel">
            <span class="team-panel-tag">/// Foto #<?= $id ?></span>
            <h2>Datos de la foto</h2>

            <form method="post" enctype="multipart/form-data" class="team-form">
                <input type="hidden" name="id" value="<?= $id ?>">

                <?php if ($error) { ?>
                    <p class="team-error"><?= htmlspecialchars($error) ?></p>
                <?php } ?>

                <div class="team-field">
                    <label for="lk-titulo">Título (opcional)</label>
                    <input type="text" id="lk-titulo" name="titulo" maxlength="100"
                           value="<?= htmlspecialchars($registro["titulo"] ?? "") ?>">
                    <span class="team-hint">Se muestra debajo de la foto en la tienda</span>
                </div>

                <div class="team-field">
                    <label for="lk-orden">Posición</label>
                    <input type="text" id="lk-orden" name="orden" inputmode="numeric" pattern="[0-9]*"
                           value="<?= (int)$registro["orden"] ?>">
                    <span class="team-hint">Las posiciones más bajas aparecen primero</span>
                </div>

                <div class="team-field">
                    <label for="lk-foto">Reemplazar foto (opcional)</label>
                    <input type="file" id="lk-foto" name="foto" accept="image/jpeg,image/png,image/webp">
                    <span class="team-hint">JPG, PNG o WEBP · máximo 5 MB</span>
                </div>

                <div class="team-form-actions">
                    <button type="submit" class="team-pill">Guardar cambios</button>
                    <a href="index.php" class="team-pill team-pill--ghost">Cancelar</a>
                </div>

            </form>
        </div>

    </div>
</section>

<script>
(function () {
    var entrada = document.getElementById("lk-foto");
    var vista = document.getElementById("lk-preview");

    entrada.addEventListener("change", function () {
        var archivo = entrada.files[0];
        if (archivo) {
            vista.src = URL.createObjectURL(archivo);
        }
    });
})();
</script>

<?php include(__DIR__ . "/../../templates/footer.php"); ?>