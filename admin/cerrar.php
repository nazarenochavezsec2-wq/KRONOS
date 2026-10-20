<?php 
session_start();

$_SESSION = []; 

if (ini_get("session.use_cookies")) { 
 
     // Recuperamos la configuración de la cookie actual 
    $parametros = session_get_cookie_params(); 
 
     // Volvemos a enviar la misma cookie vacía y con una fecha pasada 
    setcookie( 
        session_name(), 
        "", 
        time() - 42000, 
        $parametros["path"], 
        $parametros["domain"], 
        $parametros["secure"], 
        $parametros["httponly"] 
    ); 
}

session_destroy(); 

header("Location: login.php"); 
exit; 
?>