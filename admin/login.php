<?php 
    session_start(); 
    include("bd.php"); 
    $mensajeError = ""; 

    if ($_POST) { 
        $usuario = isset($_POST["usuario"])? trim($_POST["usuario"]): ""; 
        $passwordIngresada = isset($_POST["password"])? $_POST["password"]: ""; 

        $sentencia = $conexion->prepare( "SELECT password FROM tbl_usuarios WHERE usuario = :usuario LIMIT 1" ); 

        $sentencia->bindParam(":usuario", $usuario); 

        $sentencia->execute(); 

        $registro = $sentencia->fetch(PDO::FETCH_ASSOC);

        if ( $registro && password_verify($passwordIngresada, $registro["password"]) ) { 
            // Las credenciales son correctas 
            session_regenerate_id(true); 
            $_SESSION["autenticado"] = true; 

            header("Location: index.php"); 
            exit;
        } else { 
            // Las credenciales son incorrectas 
            $mensajeError = "El usuario o la contraseña son incorrectos.";
        } 
    } 
?> 

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Acceso administrativo | HIVE ARCHIVE</title>

    <!-- Tipografías de la tienda -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;700;800&display=swap"
          rel="stylesheet">

    <!-- Bootstrap: utilizado para el mensaje de error -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Estilos exclusivos del login -->
    <link rel="stylesheet" href="../assets/css/login.css">

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="admin-login">

    <header class="brutal-header">
        <div class="nav-wrapper">
            <a href="../index.html" class="brutal-logo">HIVE_ARCHIVE</a>
        </div>
    </header>

    <main class="admin-login-main">
        <div>
            <section class="admin-login-card">

                <div class="admin-card-header">
                    <span class="admin-eyebrow">
                        /// CONTROL PANEL
                    </span>

                    <h1>Iniciar sesión</h1>

                    <p>
                        Ingresá tus credenciales para administrar
                        la tienda HIVE ARCHIVE.
                    </p>
                </div>

                <div class="admin-card-body">

                    <?php if ($mensajeError !== "") { ?>
                        <div class="admin-error" role="alert">
                            <?php echo htmlspecialchars(
                                $mensajeError,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </div>
                    <?php } ?>

                    <form action="" method="post">

                        <div class="admin-form-group">
                            <label for="usuario" class="admin-form-label">
                                Usuario
                            </label>

                            <input
                                type="text"
                                class="admin-form-control"
                                name="usuario"
                                id="usuario"
                                placeholder="Nombre de usuario"
                                autocomplete="username"
                                required
                                autofocus
                            >
                        </div>

                        <div class="admin-form-group">
                            <label for="password" class="admin-form-label">
                                Contraseña
                            </label>

                            <input
                                type="password"
                                class="admin-form-control"
                                name="password"
                                id="password"
                                placeholder="Ingresá tu contraseña"
                                autocomplete="current-password"
                                required
                            >
                        </div>

                        <button type="submit" class="admin-submit">
                            ACCEDER AL PANEL →
                        </button>

                    </form>
                </div>

                <footer class="admin-card-footer">
                    ACCESO RESTRINGIDO · HIVE ARCHIVE
                </footer>

            </section>

            <div class="text-center">
                <a href="../index.html" class="admin-back">
                    ← VOLVER A LA TIENDA
                </a>
            </div>
        </div>
    </main>

</body>
</html>