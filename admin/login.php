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

<meta name="viewport" content="width=device-width, initial-scale=1.0" > 

<title>Iniciar sesión</title> 

<!-- Bootstrap CSS --> 
<link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
> 
</head> 

<body> 
    <main class="container py-5"> 
        <div class="row justify-content-center"> 
            <div class="col-12 col-md-6 col-lg-4"> 
    
                <div class="card"> 
                    <div class="card-header"> 
                        Iniciar sesión 
                    </div> 
                    <div class="card-body"> 
                        <?php if ($mensajeError != "") { ?> 
 
                            <div class="alert alert-danger" role="alert"> 
                                <?php echo $mensajeError; ?> 
                            </div> 
                        
                        <?php } ?> 
                        <form action="" method="post"> 
                            <div class="mb-3"> 
                                <label for="usuario" class="form-label"> Usuario </label> 
                            
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="usuario" 
                                    id="usuario" 
                                    placeholder="Ingrese su usuario" 
                                    autocomplete="username" 
                                    required 
                                > 
                            </div>

                            <div class="mb-3"> 
                                <label for="password" class="form-label"> Contraseña </label> 
                            
                                <input type="password" class="form-control" name="password" id="password" placeholder="Ingrese su contraseña" 
                                autocomplete="current-password" required > 
                            </div> 

                            <button 
                            type="submit" 
                            class="btn btn-primary w-100" 
                            > 
                            Entrar 
                            </button>
                        </form> 
                    </div> 
                </div>
            </div> 

        </div> 
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" ></script> 
</body> 
</html>