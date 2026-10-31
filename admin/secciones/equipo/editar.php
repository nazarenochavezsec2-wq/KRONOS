<?php
require_once(__DIR__ . "/../../includes/autenticacion.php");
require_once(__DIR__ . "/../../bd.php");
require_once(__DIR__ . "/funciones.php");

$id = (int)($_GET["id"] ?? $_POST["id"] ?? 0);

$stmt = $conexion->prepare("SELECT * FROM equipo WHERE id = :id");
$stmt->execute([":id" => $id]);
$miembro = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe, volvemos al listado
if (!$miembro) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $cargo  = trim($_POST["cargo"] ?? "");

    if ($nombre === "" || $cargo === "") {
        $error = "Completá el nombre y el cargo.";
    } else {
        $fotoNueva = subirFoto($_FILES["foto"] ?? null);

        if ($fotoNueva === false) {
            $error = "No se pudo subir la foto. Usá un JPG, PNG o WEBP de hasta 2 MB.";
        } else {
            $foto = $miembro["foto"];

            // Si subió una foto nueva, borramos la anterior
            if ($fotoNueva !== null) {
                borrarFoto($miembro["foto"]);
                $foto = $fotoNueva;
            }

            $stmt = $conexion->prepare(
                "UPDATE equipo SET nombre = :nombre, cargo = :cargo, foto = :foto WHERE id = :id"
            );
            $stmt->execute([
                ":nombre" => $nombre,
                ":cargo"  => $cargo,
                ":foto"   => $foto,
                ":id"     => $id,
            ]);
            header("Location: index.php");
            exit;
        }
    }

    // Si hubo error, mantenemos lo que escribió la persona
    $miembro["nombre"] = $nombre;
    $miembro["cargo"]  = $cargo;
}

$inicial = mb_strtoupper(mb_substr($miembro["nombre"], 0, 1)) ?: "?";
?>
<?php include(__DIR__ . "/../../templates/header.php"); ?>

<section class="admin-welcome">
    <span class="admin-eyebrow">/// EQUIPO ///</span>
    <h1>Editar integrante</h1>
    <p>Modificá los datos y guardá los cambios.</p>
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
            <div class="team-preview-frame" id="team-preview-frame">
                <?php if ($miembro["foto"]) { ?>
                    <img src="/Kronos/assets/img/equipo/<?= htmlspecialchars($miembro["foto"]) ?>" alt="">
                <?php } else { ?>
                    <div class="team-initial"><?= htmlspecialchars($inicial) ?></div>
                <?php } ?>
            </div>
            <div class="team-preview-bottom">
                <span id="team-preview-name"><?= htmlspecialchars($miembro["nombre"]) ?></span>
                <span id="team-preview-role"><?= htmlspecialchars($miembro["cargo"]) ?></span>
            </div>
        </div>

        <!-- Formulario -->
        <div class="team-panel">
            <span class="team-panel-tag">/// Integrante #<?= $id ?></span>
            <h2>Datos de la persona</h2>

            <form method="post" enctype="multipart/form-data" class="team-form">
                <input type="hidden" name="id" value="<?= $id ?>">

                <?php if ($error) { ?>
                    <p class="team-error"><?= htmlspecialchars($error) ?></p>
                <?php } ?>

                <div class="team-field">
                    <label for="team-nombre">Nombre</label>
                    <input type="text" id="team-nombre" name="nombre" required maxlength="100"
                           value="<?= htmlspecialchars($miembro["nombre"]) ?>">
                </div>

                <div class="team-field">
                    <label for="team-cargo">Cargo</label>
                    <input type="text" id="team-cargo" name="cargo" required maxlength="100"
                           value="<?= htmlspecialchars($miembro["cargo"]) ?>">
                </div>

                <div class="team-field">
                    <label for="team-foto">Reemplazar foto (opcional)</label>
                    <input type="file" id="team-foto" name="foto" accept="image/jpeg,image/png,image/webp">
                    <span class="team-hint">JPG, PNG o WEBP · máximo 2 MB</span>
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