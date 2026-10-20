<?php 
// Iniciamos o recuperamos la sesión 
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
} 
// Comprobamos si el usuario inició sesión 
if ( !isset($_SESSION["autenticado"]) || 
    $_SESSION["autenticado"] !== true 
) { 
    header("Location: /website/admin/login.php"); 
    exit; 
}