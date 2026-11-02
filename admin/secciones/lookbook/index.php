<?php
require_once(__DIR__ . "/../../includes/autenticacion.php");
require_once(__DIR__ . "/../../bd.php");
require_once(__DIR__ . "/funciones.php");

// Eliminar foto
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar_id"])) {
    $id = (int)$_POST["eliminar_id"];

    $stmt = $conexion->prepare("SELECT imagen FROM lookbook WHERE id = :id");
    $stmt->execute([":id" => $id]);
    $foto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($foto) {
        borrarImagenLookbook($foto["imagen"]);
        $stmt = $conexion->prepare("DELETE FROM lookbook WHERE id = :id");
        $stmt->execute([":id" => $id]);
    }
    header("Location: index.php?ok=eliminado");
    exit;
}

// Mensajes de aviso
$mensaje = "";
$ok = $_GET["ok"] ?? "";
$n  = (int)($_GET["n"] ?? 0);
$f  = (int)($_GET["f"] ?? 0);

if ($ok === "subido") {
    $mensaje = $n === 1 ? "Se agregó 1 foto." : "Se agregaron " . $n . " fotos.";
} elseif ($ok === "parcial") {
    $mensaje = "Se agregaron " . $n . " foto(s), pero " . $f . " no se pudieron subir. Revisá el formato (JPG, PNG o WEBP) y que no pasen de 5 MB.";
} elseif ($ok === "editado") {
    $mensaje = "Cambios guardados.";
} elseif ($ok === "eliminado") {
    $mensaje = "Foto eliminada.";
}

$fotos = $conexion->query("SELECT id, imagen, titulo, orden FROM lookbook ORDER BY orden ASC, id DESC")
                  ->fetchAll(PDO::FETCH_ASSOC);
$total = count($fotos);
?>
<?php include(__DIR__ . "/../../templates/header.php"); ?>

<section class="admin-welcome">
    <span class="admin-eyebrow">/// LOOKBOOK ///</span>
    <h1>Lookbook</h1>
    <p>Administrá las fotos de la galería de looks.</p>
</section>

<section class="team-wrap">

    <div class="team-bar">
        <div class="team-counter">
            <span class="team-dot"></span>
            <?= $total ?> <?= $total === 1 ? "foto" : "fotos" ?>
        </div>
        <div class="team-bar-actions">
            <a class="team-pill team-pill--ghost" href="/Kronos/admin/index.php">← Volver al panel</a>
            <a class="team-pill" href="crear.php">+ Agregar fotos</a>
        </div>
    </div>

    <?php if ($mensaje) { ?>
        <p class="team-notice"><?= htmlspecialchars($mensaje) ?></p>
    <?php } ?>

    <?php if ($total === 0) { ?>

        <div class="team-empty">
            <p>Todavía no hay fotos en el lookbook.</p>
            <a class="team-pill" href="crear.php">Subir las primeras</a>
        </div>

    <?php } else { ?>

        <div class="team-grid">
            <?php foreach ($fotos as $f) { ?>
                <article class="team-card">

                    <div class="team-photo">
                        <img src="/Kronos/assets/img/lookbook/<?= htmlspecialchars($f["imagen"]) ?>"
                             alt="<?= htmlspecialchars($f["titulo"] ?? "Foto del lookbook") ?>">
                        <span class="team-id">N.º <?= str_pad($f["orden"], 2, "0", STR_PAD_LEFT) ?></span>
                    </div>

                    <div class="team-info">
                        <div>
                            <h3 class="team-name">
                                <?= $f["titulo"] ? htmlspecialchars($f["titulo"]) : "Sin título" ?>
                            </h3>
                            <p class="team-role">Posición <?= (int)$f["orden"] ?></p>
                        </div>

                        <div class="team-actions">
                            <a class="team-pill team-pill--small"
                               href="editar.php?id=<?= $f["id"] ?>">Editar</a>
                            <form method="post"
                                  onsubmit="return confirm('¿Eliminar esta foto del lookbook?');">
                                <input type="hidden" name="eliminar_id" value="<?= $f["id"] ?>">
                                <button type="submit" class="team-pill team-pill--danger team-pill--small">Eliminar</button>
                            </form>
                        </div>
                    </div>

                </article>
            <?php } ?>
        </div>

    <?php } ?>
</section>

<?php include(__DIR__ . "/../../templates/footer.php"); ?>