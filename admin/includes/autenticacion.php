<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1) Tiene que haber iniciado sesión
if (empty($_SESSION["autenticado"])) {
    header("Location: /Kronos/admin/login.php");
    exit;
}

// 2) Y tiene que ser admin; si no, a la página normal
if (($_SESSION["rol"] ?? "") !== "admin") {
    header("Location: /Kronos/index.php");
    exit;
}