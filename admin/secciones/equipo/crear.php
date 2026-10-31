<?php
require_once(__DIR__ . "/../../includes/autenticacion.php");
require_once(__DIR__ . "/../../bd.php");
require_once(__DIR__ . "/funciones.php");

$error  = "";
$nombre = "";
$cargo  = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $cargo  = trim($_POST["cargo"] ?? "");

    if ($nombre === "" || $cargo === "") {
        $error = "Completá el nombre y el cargo.";
    } else {
        $foto = subirFoto($_FILES["foto"] ?? null);

        if ($foto === false) {
            $error = "No se pudo subir la foto. Usá un JPG, PNG o WEBP de hasta 2 MB.";
        } else {
            $stmt = $conexion->prepare(
                "INSERT INTO equipo (nombre, cargo, foto) VALUES (:nombre, :cargo, :foto)"
            );
            $stmt->execute([
                ":nombre" => $nombre,
                ":cargo"  => $cargo,
                ":foto"   => $foto,
            ]);
            header("Location: index.php");
            exit;
        }
    }
}

$inicial = mb_strtoupper(mb_substr($nombre, 0, 1)) ?: "?";
?>
<?php include(__DIR__ . "/../../templates/header.php"); ?>

<section class="admin-welcome">
    <span class="admin-eyebrow">/// EQUIPO ///</span>
    <h1>Agregar integrante</h1>
    <p>Cargá los datos de la nueva persona.</p>
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
                <span>Nuevo</span>
            </div>
            <div class="team-preview-frame" id="team-preview-frame">
                <div class="team-initial"><?= htmlspecialchars($inicial) ?></div>
            </div>
            <div class="team-preview-bottom">
                <span id="team-preview-name"><?= htmlspecialchars($nombre !== "" ? $nombre : "Nombre") ?></span>
                <span id="team-preview-role"><?= htmlspecialchars($cargo !== "" ? $cargo : "Cargo") ?></span>
            </div>
        </div>

        <!-- Formulario -->
        <div class="team-panel">
            <span class="team-panel-tag">/// Nuevo integrante</span>
            <h2>Datos de la persona</h2>

            <form method="post" enctype="multipart/form-data" class="team-form">

                <?php if ($error) { ?>
                    <p class="team-error"><?= htmlspecialchars($error) ?></p>
                <?php } ?>

                <div class="team-field">
                    <label for="team-nombre">Nombre</label>
                    <input type="text" id="team-nombre" name="nombre" required maxlength="100"
                           value="<?= htmlspecialchars($nombre) ?>">
                </div>

                <div class="team-field">
                    <label for="team-cargo">Cargo</label>
                    <input type="text" id="team-cargo" name="cargo" required maxlength="100"
                           value="<?= htmlspecialchars($cargo) ?>">
                </div>

                <div class="team-field">
                    <label for="team-foto">Foto (opcional)</label>
                    <input type="file" id="team-foto" name="foto" accept="image/jpeg,image/png,image/webp">
                    <span class="team-hint">JPG, PNG o WEBP · máximo 2 MB</span>
                </div>

                <div class="team-form-actions">
                    <button type="submit" class="team-pill">Guardar</button>
                    <a href="index.php" class="team-pill team-pill--ghost">Cancelar</a>
                </div>

            </form>
        </div>

    </div>
</section>

<script>
(function () {
    const nombre = document.getElementById("team-nombre");
    const cargo  = document.getElementById("team-cargo");
    const file   = document.getElementById("team-foto");
    const frame  = document.getElementById("team-preview-frame");
    const pvName = document.getElementById("team-preview-name");
    const pvRole = document.getElementById("team-preview-role");

    function renderInicial() {
        if (frame.querySelector("img")) return;
        let el = frame.querySelector(".team-initial");
        if (!el) {
            el = document.createElement("div");
            el.className = "team-initial";
            frame.appendChild(el);
        }
        el.textContent = (nombre.value.trim().charAt(0) || "?").toUpperCase();
    }

    nombre.addEventListener("input", function () {
        pvName.textContent = nombre.value.trim() || "Nombre";
        renderInicial();
    });

    cargo.addEventListener("input", function () {
        pvRole.textContent = cargo.value.trim() || "Cargo";
    });

    file.addEventListener("change", function () {
        const f = file.files[0];
        if (!f) return;
        frame.innerHTML = "";
        const img = document.createElement("img");
        img.src = URL.createObjectURL(f);
        img.alt = "";
        frame.appendChild(img);
    });
})();
</script>

<?php include(__DIR__ . "/../../templates/footer.php"); ?>