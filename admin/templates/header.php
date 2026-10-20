<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador del sitio web</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" > 

    <!-- DataTables CSS --> 
  <link href="https://cdn.datatables.net/v/dt/dt-3.0.4/datatables.min.css" rel="stylesheet" >
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="/website/admin/index.php">Administrador</a>
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
            <a class="nav-link" href="/website/admin/cerrar.php" >Cerrar Sesion</a> 
        </li> 
      </ul>
    </div>
  </div>
</nav>

<!--Apertura del contenido principal-->
    
<main class="container py-5">