<?php require_once(__DIR__ . "/includes/autenticacion.php"); ?> 
<?php include("templates/header.php"); ?> 

<section class="admin-welcome">
    <span class="admin-eyebrow">/// CONTROL PANEL ///</span>
    <h1>Bienvenidos al administrador</h1> 
    <p> 
        Desde este panel podrá controlar todo el contenido del sitio web. 
    </p> 
</section>

<section class="admin-grid">
   

    <a class="admin-tile" href="/Kronos/admin/secciones/productos/">
        <div>
            <h2>Productos</h2>
            <p>Gestioná los productos publicados.</p>
        </div>
        <span class="admin-tile-cta">ENTRAR →</span>
    </a>

    <a class="admin-tile" href="/Kronos/admin/secciones/equipo/">
        <div>
            <h2>Equipo</h2>
            <p>Mantené al día los integrantes.</p>
        </div>
        <span class="admin-tile-cta">ENTRAR →</span>
    </a>

    <a class="admin-tile" href="/Kronos/admin/secciones/usuarios/">
        <div>
            <h2>Usuarios</h2>
            <p>Controlá quién accede al panel.</p>
        </div>
        <span class="admin-tile-cta">ENTRAR →</span>
    </a>

    <a class="admin-tile" href="/Kronos/admin/secciones/configuracion/">
        <div>
            <h2>Configuraciones</h2>
            <p>Control de herramientas y funcionalidades de la pagina.</p>
        </div>
        <span class="admin-tile-cta">ENTRAR →</span>
    </a>

</section>

<?php include("templates/footer.php"); ?>