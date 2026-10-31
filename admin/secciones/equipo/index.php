<?php
require_once(__DIR__ . "/../../includes/autenticacion.php");
require_once(__DIR__ . "/../../bd.php");
require_once(__DIR__ . "/funciones.php");

// Eliminar integrante
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar_id"])) {
    $id = (int)$_POST["eliminar_id"];

    $stmt = $conexion->prepare("SELECT foto FROM equipo WHERE id = :id");
    $stmt->execute([":id" => $id]);
    $miembro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($miembro) {
        borrarFoto($miembro["foto"]);
        $stmt = $conexion->prepare("DELETE FROM equipo WHERE id = :id");
        $stmt->execute([":id" => $id]);
    }
    header("Location: index.php");
    exit;
}

$integrantes = $conexion->query("SELECT * FROM equipo ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$total = count($integrantes);
?>
<?php include(__DIR__ . "/../../templates/header.php"); ?>

<section class="admin-welcome">
    <span class="admin-eyebrow">/// EQUIPO ///</span>
    <h1>Equipo</h1>
    <p>Mantené al día los integrantes.</p>
</section>

<section class="team-wrap">

    <div class="team-bar">
        <div class="team-counter">
            <span class="team-dot"></span>
            <?= $total ?> <?= $total === 1 ? "integrante" : "integrantes" ?>
        </div>
        <div class="team-bar-actions">
            <a class="team-pill team-pill--ghost" href="/Kronos/admin/index.php">← Volver al panel</a>
            <a class="team-pill" href="crear.php">+ Agregar integrante</a>
        </div>
    </div>

    <?php if ($total === 0) { ?>

        <div class="team-empty">
            <p>Todavía no hay integrantes cargados.</p>
            <a class="team-pill" href="crear.php">Cargar el primero</a>
        </div>

    <?php } else { ?>

        <div class="team-grid">
            <?php foreach ($integrantes as $i) { ?>
                <article class="team-card">

                    <div class="team-photo">
                        <?php if ($i["foto"]) { ?>
                            <img src="/Kronos/assets/img/equipo/<?= htmlspecialchars($i["foto"]) ?>"
                                 alt="<?= htmlspecialchars($i["nombre"]) ?>">
                        <?php } else { ?>
                            <div class="team-initial">
                                <?= htmlspecialchars(mb_strtoupper(mb_substr($i["nombre"], 0, 1))) ?>
                            </div>
                        <?php } ?>
                        <span class="team-id">ID <?= str_pad($i["id"], 2, "0", STR_PAD_LEFT) ?></span>
                    </div>

                    <div class="team-info">
                        <div>
                            <h3 class="team-name"><?= htmlspecialchars($i["nombre"]) ?></h3>
                            <p class="team-role"><?= htmlspecialchars($i["cargo"]) ?></p>
                        </div>

                        <div class="team-actions">
                            <a class="team-pill team-pill--small"
                               href="editar.php?id=<?= $i["id"] ?>">Editar</a>
                            <form method="post"
                                  onsubmit="return confirm('¿Eliminar a este integrante?');">
                                <input type="hidden" name="eliminar_id" value="<?= $i["id"] ?>">
                                <button type="submit" class="team-pill team-pill--danger team-pill--small">Eliminar</button>
                            </form>
                        </div>
                    </div>

                </article>
            <?php } ?>
        </div>

    <?php } ?>
</section>

