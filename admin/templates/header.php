<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador | HIVE ARCHIVE</title>

    <!-- Tipografías de la tienda -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;700;800&display=swap"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" > 

    <!-- DataTables CSS --> 
  <link href="https://cdn.datatables.net/v/dt/dt-3.0.4/datatables.min.css" rel="stylesheet" >

    <!-- Estilos de la tienda + estilos del panel (deben ir al final) -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">

    <div class="admin-strip">/// CONTROL PANEL · HIVE ARCHIVE</div>

    <nav class="navbar navbar-expand-lg admin-navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="/website/admin/index.php">HIVE_ARCHIVE / ADMIN</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Mostrar Navegacion" > 
        <span class="navbar-toggler-icon"></span> 
    </button> 
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="/website/admin/secciones/servicios/">Servicios</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/website/admin/secciones/portafolio/">Portafolio</a>
        </li>
        <li class="nav-item"> 
            <a class="nav-link" href="/website/admin/secciones/entradas/">Entradas</a> 
        </li>
        <li class="nav-item"> 
            <a class="nav-link" href="/website/admin/secciones/equipo/">Equipo</a> 
        </li> 
        <li class="nav-item"> 
            <a class="nav-link" href="/website/admin/secciones/configuraciones/">Configuraciones</a> 
        </li> 
        <li class="nav-item"> 
            <a class="nav-link" href="/website/admin/secciones/usuarios/">Usuarios</a> 
        </li> 
        <li class="nav-item"> 
            <a class="nav-link admin-nav-logout" href="/website/admin/cerrar.php" >Cerrar Sesion</a> 
        </li> 
      </ul>
    </div>
  </div>
</nav>

<!--Apertura del contenido principal-->
    
<main class="container py-5">