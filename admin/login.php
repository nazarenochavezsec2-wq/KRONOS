<?php 
    session_start(); 
    include("bd.php"); 
    $mensajeError = ""; 

    if ($_POST) { 
        $usuario = isset($_POST["usuario"])? trim($_POST["usuario"]): ""; 
        $passwordIngresada = isset($_POST["password"])? $_POST["password"]: ""; 

        $sentencia = $conexion->prepare( "SELECT usuario, password, rol FROM tbl_usuarios WHERE usuario = :usuario LIMIT 1" ); 
        $sentencia->bindParam(":usuario", $usuario); 
        $sentencia->execute(); 
        $registro = $sentencia->fetch(PDO::FETCH_ASSOC);

        if ( $registro && password_verify($passwordIngresada, $registro["password"]) ) { 
            session_regenerate_id(true); 
            $_SESSION["autenticado"] = true; 
            $_SESSION["usuario"] = $registro["usuario"]; 
            $_SESSION["rol"] = $registro["rol"]; 

            // El admin va al panel; los demás, a la página normal
            if ($registro["rol"] === "admin") {
                header("Location: index.php"); 
            } else {
                header("Location: ../index.php"); 
            }
            exit;
        } else { 
            $mensajeError = "Credenciales incorrectas. Acceso denegado.";
        } 
    } 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HIVE® | CONTROL PANEL LOGIN</title>

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
            <span>✦ SECURE ACCESS 2026</span>
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

    <!-- LOGIN MAIN CONTAINER -->
    <main class="admin-login-main">
        <div class="admin-login-wrapper">

            <section class="admin-login-card">
                
                <div class="admin-card-header">
                    <div class="sub-heading">
                        <span class="status-dot"></span>
                        SECURITY LEVEL / ADMIN
                    </div>
                    <h1>ADMIN<br><span>LOGIN.</span></h1>
                    <p>Ingresá tus credenciales para administrar el catálogo y sistema de HIVE.</p>
                </div>

                <div class="admin-card-body">

                    <?php if ($mensajeError !== "") { ?>
                        <div class="admin-error" role="alert">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <?php echo htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php } ?>

                    <form action="" method="post">

                        <div class="admin-form-group">
                            <label for="usuario" class="admin-form-label">USUARIO</label>
                            <input 
                                type="text" 
                                class="admin-form-control" 
                                name="usuario" 
                                id="usuario" 
                                placeholder="ej. admin_hive" 
                                autocomplete="username" 
                                required 
                                autofocus
                            >
                        </div>

                        <div class="admin-form-group">
                            <label for="password" class="admin-form-label">CONTRASEÑA</label>
                            <input 
                                type="password" 
                                class="admin-form-control" 
                                name="password" 
                                id="password" 
                                placeholder="••••••••••••" 
                                autocomplete="current-password" 
                                required
                            >
                        </div>

                        <button type="submit" class="admin-submit-btn">
                            ACCEDER AL SISTEMA <i class="fa-solid fa-arrow-right"></i>
                        </button>

                    </form>

                </div>

                <a class="admin-alt-link" href="registro.php">¿No tenés cuenta? Registrate</a>

                <div class="admin-card-footer">
                    <span>HIVE® ARCHIVE SYSTEM</span>
                    <span>MMXXVI</span>
                </div>

            </section>

        </div>
    </main>

</body>
</html>