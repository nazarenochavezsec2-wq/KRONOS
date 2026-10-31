<?php
require_once(__DIR__ . "/../../includes/autenticacion.php");
require_once(__DIR__ . "/../../bd.php");

// Usuario con la sesión iniciada (ajustá la clave si tu login usa otra)
$actual = $_SESSION["usuario"] ?? null;
$error  = "";

// Eliminar usuario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar_id"])) {
    $id = (int)$_POST["eliminar_id"];

    $stmt = $conexion->prepare("SELECT usuario FROM tbl_usuarios WHERE id = :id");
    $stmt->execute([":id" => $id]);
    $objetivo = $stmt->fetch(PDO::FETCH_ASSOC);

    $cantidad = (int)$conexion->query("SELECT COUNT(*) FROM tbl_usuarios")->fetchColumn();

    if (!$objetivo) {
        header("Location: index.php");
        exit;
    } elseif ($cantidad <= 1) {
        $error = "No se puede eliminar al único usuario del panel.";
    } elseif ($actual !== null && $objetivo["usuario"] === $actual) {
        $error = "No podés eliminar tu propio usuario mientras tenés la sesión iniciada.";
    } else {
        $stmt = $conexion->prepare("DELETE FROM tbl_usuarios WHERE id = :id");
        $stmt->execute([":id" => $id]);
        header("Location: index.php?ok=eliminado");
        exit;
    }
}

$mensajes = [
    "editado"   => "Cambios guardados.",
    "eliminado" => "Usuario eliminado.",
];
$ok = $mensajes[$_GET["ok"] ?? ""] ?? "";

// Nunca seleccionamos la contraseña
$usuarios = $conexion->query("SELECT id, usuario, correo FROM tbl_usuarios ORDER BY id ASC")
                     ->fetchAll(PDO::FETCH_ASSOC);
$total = count($usuarios);
?>
<?php include(__DIR__ . "/../../templates/header.php"); ?>

<section class="admin-welcome">
    <span class="admin-eyebrow">/// USUARIOS ///</span>
    <h1>Usuarios</h1>
    <p>Controlá quién accede al panel.</p>
</section>

<section class="team-wrap">

    <div class="team-bar">
        <div class="team-counter">
            <span class="team-dot"></span>
            <?= $total ?> <?= $total === 1 ? "usuario registrado" : "usuarios registrados" ?>
        </div>
        <div class="team-bar-actions">
            <a class="team-pill team-pill--ghost" href="/Kronos/admin/index.php">← Volver al panel</a>
        </div>
    </div>

    <?php if ($ok) { ?>
        <p class="team-notice"><?= htmlspecialchars($ok) ?></p>
    <?php } ?>

    <?php if ($error) { ?>
        <p class="team-error team-error--block"><?= htmlspecialchars($error) ?></p>
    <?php } ?>

    <?php if ($total === 0) { ?>

        <div class="team-empty">
            <p>Todavía no hay usuarios registrados.</p>
        </div>

    <?php } else { ?>

        <div class="user-card">
            <div class="user-table-wrap">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th class="user-col-actions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u) { ?>
                            <tr>
                                <td class="user-id"><?= str_pad($u["id"], 2, "0", STR_PAD_LEFT) ?></td>
                                <td>
                                    <div class="user-person">
                                        <span class="user-avatar">
                                            <?= htmlspecialchars(mb_strtoupper(mb_substr($u["usuario"], 0, 1))) ?>
                                        </span>
                                        <span class="user-name"><?= htmlspecialchars($u["usuario"]) ?></span>
                                        <?php if ($actual !== null && $u["usuario"] === $actual) { ?>
                                            <span class="user-badge">Tu sesión</span>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td class="user-mail"><?= htmlspecialchars($u["correo"]) ?></td>
                                <td class="user-col-actions">
                                    <div class="user-actions">
                                        <a class="team-pill team-pill--small"
                                           href="editar.php?id=<?= $u["id"] ?>">Editar</a>
                                        <form method="post"
                                              onsubmit="return confirm('¿Eliminar a este usuario?');">
                                            <input type="hidden" name="eliminar_id" value="<?= $u["id"] ?>">
                                            <button type="submit"
                                                    class="team-pill team-pill--danger team-pill--small">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php } ?>
</section>

<?php include(__DIR__ . "/../../templates/footer.php"); ?>