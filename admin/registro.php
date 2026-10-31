<?php
include("bd.php");

$mensajeError = "";
$exito        = false;
$usuario      = "";
$correo       = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $correo  = trim($_POST["correo"] ?? "");
    $pass    = $_POST["password"] ?? "";
    $pass2   = $_POST["password2"] ?? "";

    if ($usuario === "" || $correo === "" || $pass === "") {
        $mensajeError = "Completá todos los campos.";
    } elseif (mb_strlen($usuario) < 3) {
        $mensajeError = "El usuario debe tener al menos 3 caracteres.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensajeError = "El correo no es válido.";
    } elseif (strlen($pass) < 8) {
        $mensajeError = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($pass !== $pass2) {
        $mensajeError = "Las contraseñas no coinciden.";
    } else {
        $sentencia = $conexion->prepare(
            "SELECT COUNT(*) FROM tbl_usuarios WHERE usuario = :usuario OR correo = :correo"
        );
        $sentencia->execute([":usuario" => $usuario, ":correo" => $correo]);

        if ((int)$sentencia->fetchColumn() > 0) {
            $mensajeError = "Ya existe un usuario con ese nombre o ese correo.";
        } else {
            $sentencia = $conexion->prepare(
                "INSERT INTO tbl_usuarios (usuario, password, correo, rol)
                 VALUES (:usuario, :password, :correo, 'usuario')"
            );
            $sentencia->execute([
                ":usuario"  => $usuario,
                ":password" => password_hash($pass, PASSWORD_DEFAULT),
                ":correo"   => $correo,
            ]);
            $exito = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HIVE® | REGISTRO DE ACCESO</title>

    <!-- Tipografías oficiales de la tienda -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Estilos generales y específicos -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body class="admin-login-body">

    <!-- BARRA SUPERIOR -->
    <div class="brutal-ticker">
        <div class="ticker-content">
            <span>✦ RESTRICTED AREA</span>
            <span>✦ HIVE DIGITAL WORLD ADMIN</span>
            <span>✦ NEW ACCESS 2026</span>
            <span>✦ RESTRICTED AREA</span>
            <span>✦ HIVE DIGITAL WORLD ADMIN</span>
        </div>
    </div>

    <!-- HEADER -->
    <header class="brutal-header">
        <div class="nav-wrapper">
            <a href="../index.php" class="brutal-logo">
                HIVE<span>®</span>
                <small>CONTROL PANEL</small>
            </a>
            <div class="nav-actions">
                <a href="../index.php" class="brutal-bag-btn">
                    <i class="fa-solid fa-arrow-left"></i> TIENDA
                </a>
            </div>
        </div>
    </header>

    <!-- REGISTRO MAIN CONTAINER -->
    <main class="admin-login-main">
        <div class="admin-login-wrapper">

            <section class="admin-login-card">

                <div class="admin-card-header">
                    <div class="sub-heading">
                        <span class="status-dot"></span>
                        SECURITY LEVEL / NEW ACCESS
                    </div>
                    <h1>CREAR<br><span>CUENTA.</span></h1>
                    <p>Registrate para acceder a tu cuenta de HIVE.</p>
                </div>

                <div class="admin-card-body">

                    <?php if ($exito) { ?>

                        <div class="admin-success" role="status">
                            <i class="fa-solid fa-circle-check"></i>
                            Cuenta creada. Ya podés iniciar sesión con tu usuario y contraseña.
                        </div>

                        <a href="login.php" class="admin-submit-btn">
                            IR AL LOGIN <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    <?php } else { ?>

                        <?php if ($mensajeError !== "") { ?>
                            <div class="admin-error" role="alert">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <?php echo htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        <?php } ?>

                        <form action="" method="post" autocomplete="off">

                            <div class="admin-form-group">
                                <label for="usuario" class="admin-form-label">USUARIO</label>
                                <input
                                    type="text"
                                    class="admin-form-control"
                                    name="usuario"
                                    id="usuario"
                                    placeholder="ej. nombre_usuario"
                                    maxlength="50"
                                    value="<?php echo htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8'); ?>"
                                    autocomplete="username"
                                    required
                                    autofocus
                                >
                            </div>

                            <div class="admin-form-group">
                                <label for="correo" class="admin-form-label">CORREO</label>
                                <input
                                    type="email"
                                    class="admin-form-control"
                                    name="correo"
                                    id="correo"
                                    placeholder="ej. nombre@correo.com"
                                    maxlength="100"
                                    value="<?php echo htmlspecialchars($correo, ENT_QUOTES, 'UTF-8'); ?>"
                                    autocomplete="email"
                                    required
                                >
                            </div>

                            <div class="admin-form-group">
                                <label for="password" class="admin-form-label">CONTRASEÑA</label>
                                <input
                                    type="password"
                                    class="admin-form-control"
                                    name="password"
                                    id="password"
                                    placeholder="Mínimo 8 caracteres"
                                    minlength="8"
                                    autocomplete="new-password"
                                    required
                                >
                            </div>

                            <div class="admin-form-group">
                                <label for="password2" class="admin-form-label">REPETIR CONTRASEÑA</label>
                                <input
                                    type="password"
                                    class="admin-form-control"
                                    name="password2"
                                    id="password2"
                                    placeholder="••••••••••••"
                                    minlength="8"
                                    autocomplete="new-password"
                                    required
                                >
                            </div>

                            <button type="submit" class="admin-submit-btn">
                                CREAR CUENTA <i class="fa-solid fa-arrow-right"></i>
                            </button>

                        </form>

                    <?php } ?>

                </div>

                <a class="admin-alt-link" href="login.php">← Ya tengo cuenta, iniciar sesión</a>

                <div class="admin-card-footer">
                    <span>HIVE® ARCHIVE SYSTEM</span>
                    <span>MMXXVI</span>
                </div>

            </section>

        </div>
    </main>

</body>
</html>