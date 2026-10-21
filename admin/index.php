<?php require_once(__DIR__ . "/includes/autenticacion.php"); ?> 
<?php include("templates/header.php"); ?> 

<section class="admin-welcome">
    <span class="admin-eyebrow">/// CONTROL PANEL</span>
    <h1>Bienvenidos al administrador</h1> 
    <p> 
        Desde este panel podrá controlar todo el contenido del sitio web. 
    </p> 
</section>

<section class="admin-grid">
    <a class="admin-tile" href="/website/admin/secciones/servicios/">
        <div>
            <h2>Servicios</h2>
            <p>Administrá los servicios del sitio.</p>
        </div>
        <span class="admin-tile-cta">ENTRAR →</span>
    </a>

    <a class="admin-tile" href="/Kronos/admin/secciones/productos/">
        <div>
            <h2>Productos</h2>
            <p>Gestioná los productos publicados.</p>
        </div>
        <span class="admin-tile-cta">ENTRAR →</span>
    </a>

    <a class="admin-tile" href="/Kronos/admin/secciones/entradas/">
        <div>
            <h2>Entradas</h2>
            <p>Creá y editá las entradas del blog.</p>
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

    <a class="admin-tile" href="/Kronos/admin/secciones/configuraciones/">
        <div>
            <h2>Configuraciones</h2>
            <p>Ajustá los datos generales del sitio.</p>
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
</section>

<?php include("templates/footer.php"); ?>