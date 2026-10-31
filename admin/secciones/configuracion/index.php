<?php require_once(__DIR__ . "/../../includes/autenticacion.php"); ?>
<?php include(__DIR__ . "/../../templates/header.php"); ?>

<section class="admin-welcome">
    <span class="admin-eyebrow">/// CONFIGURACIONES ///</span>
    <h1>Configuraciones</h1>
    <p>Elegí qué querés administrar.</p>
</section>

<section class="admin-grid">

    <a class="admin-tile" href="/Kronos/admin/secciones/categorias/">
        <div>
            <h2>Categorías</h2>
            <p>Creá y editá las categorías de los productos.</p>
        </div>
        <span class="admin-tile-cta">ENTRAR →</span>
    </a>

    <a class="admin-tile" href="/Kronos/admin/secciones/temporadas/">
        <div>
            <h2>Temporadas</h2>
            <p>Gestioná las temporadas disponibles.</p>
        </div>
        <span class="admin-tile-cta">ENTRAR →</span>
    </a>

</section>

<?php include(__DIR__ . "/../../templates/footer.php"); ?>