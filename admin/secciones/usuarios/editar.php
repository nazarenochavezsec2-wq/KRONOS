<?php
require_once(__DIR__ . "/../../includes/autenticacion.php");
require_once(__DIR__ . "/../../bd.php");

$id = (int)($_GET["id"] ?? $_POST["id"] ?? 0);

$stmt = $conexion->prepare("SELECT id, usuario, correo FROM tbl_usuarios WHERE id = :id");
$stmt->execute([":id" => $id]);
$registro = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe, volvemos al listado
if (!$registro) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $correo  = trim($_POST["correo"] ?? "");
    $pass    = $_POST["password"] ?? "";
    $pass2   = $_POST["password2"] ?? "";

    if ($usuario === "" || $correo === "") {
        $error = "Completá el usuario y el correo.";
    } elseif (mb_strlen($usuario) < 3) {
        $error = "El usuario debe tener al menos 3 caracteres.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo no es válido.";
    } elseif ($pass !== "" && strlen($pass) < 8) {
        $error = "La contraseña nueva debe tener al menos 8 caracteres.";
    } elseif ($pass !== $pass2) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Que no lo use otro usuario
        $stmt = $conexion->prepare(
            "SELECT COUNT(*) FROM tbl_usuarios
             WHERE (usuario = :usuario OR correo = :correo) AND id <> :id"
        );
        $stmt->execute([":usuario" => $usuario, ":correo" => $correo, ":id" => $id]);

        if ((int)$stmt->fetchColumn() > 0) {
            $error = "Ya existe otro usuario con ese nombre o ese correo.";
        } else {
            if ($pass !== "") {
                $stmt = $conexion->prepare(
                    "UPDATE tbl_usuarios
                     SET usuario = :usuario, correo = :correo, password = :password
                     WHERE id = :id"
                );
                $stmt->execute([
                    ":usuario"  => $usuario,
                    ":correo"   => $correo,
                    ":password" => password_hash($pass, PASSWORD_DEFAULT),
                    ":id"       => $id,
                ]);
            } else {
                // Contraseña vacía: se conserva la actual
                $stmt = $conexion->prepare(
                    "UPDATE tbl_usuarios SET usuario = :usuario, correo = :correo WHERE id = :id"
                );
                $stmt->execute([
                    ":usuario" => $usuario,
                    ":correo"  => $correo,
                    ":id"      => $id,
                ]);
            }
            header("Location: index.php?ok=editado");
            exit;
        }
    }

    // Si hubo error, mantenemos lo que escribió la persona
    $registro["usuario"] = $usuario;
    $registro["correo"]  = $correo;
}
?>
<?php include(__DIR__ . "/../../templates/header.php"); ?>

<section class="admin-welcome">
    <span class="admin-eyebrow">/// USUARIOS ///</span>
    <h1>Editar usuario</h1>
    <p>Modificá los datos de acceso.</p>
</section>

<section class="team-wrap">
    <div class="team-bar">
        <a class="team-pill team-pill--ghost" href="index.php">← Volver al listado</a>
    </div>

    <div class="team-panel team-panel--narrow">
        <span class="team-panel-tag">/// Usuario #<?= $id ?></span>
        <h2>Datos de acceso</h2>

        <form method="post" class="team-form" autocomplete="off">
            <input type="hidden" name="id" value="<?= $id ?>">

            <?php if ($error) { ?>
                <p class="team-error"><?= htmlspecialchars($error) ?></p>
            <?php } ?>

            <div class="team-field">
                <label for="u-usuario">Usuario</label>
                <input type="text" id="u-usuario" name="usuario" required maxlength="50"
                       value="<?= htmlspecialchars($registro["usuario"]) ?>">
            </div>

            <div class="team-field">
                <label for="u-correo">Correo</label>
                <input type="email" id="u-correo" name="correo" required maxlength="100"
                       value="<?= htmlspecialchars($registro["correo"]) ?>">
            </div>

            <div class="team-field">
                <label for="u-pass">Contraseña nueva (opcional)</label>
                <input type="password" id="u-pass" name="password"
                       minlength="8" autocomplete="new-password">
                <span class="team-hint">Dejala vacía para conservar la actual</span>
            </div>

            <div class="team-field">
                <label for="u-pass2">Repetir contraseña nueva</label>
                <input type="password" id="u-pass2" name="password2"
                       minlength="8" autocomplete="new-password">
            </div>

            <div class="team-form-actions">
                <button type="submit" class="team-pill">Guardar cambios</button>
                <a href="index.php" class="team-pill team-pill--ghost">Cancelar</a>
            </div>

        </form>
    </div>
</section>

<?php include(__DIR__ . "/../../templates/footer.php"); ?>