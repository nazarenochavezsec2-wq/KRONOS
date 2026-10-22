<?php 
require_once(__DIR__ . "/../../includes/autenticacion.php"); 

include("../../bd.php"); 

// Escapa texto antes de imprimirlo en pantalla
if (!function_exists("escapar")) {
    function escapar($valor) {
        return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
    }
}

$mensajeError = ""; 
$nombre = ""; 

if ($_POST) { 
    $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : ""; 

    if ($nombre === "") { 
        $mensajeError = "Ingresá el nombre de la categoría."; 
    } elseif (mb_strlen($nombre, "UTF-8") > 100) { 
        $mensajeError = "El nombre no puede superar los 100 caracteres."; 
    } else { 
        // Evitamos categorías repetidas 
        $sentencia = $conexion->prepare( "SELECT COUNT(*) FROM tbl_categorias WHERE nombre = :nombre" ); 
        $sentencia->bindParam(":nombre", $nombre); 
        $sentencia->execute(); 

        if ((int)$sentencia->fetchColumn() > 0) { 
            $mensajeError = "Ya existe una categoría con ese nombre."; 
        } else { 
            $sentencia = $conexion->prepare( 
                "INSERT INTO tbl_categorias (id, nombre) VALUES (NULL, :nombre)" 
            ); 
            $sentencia->bindParam(":nombre", $nombre); 
            $sentencia->execute(); 

            header("Location: index.php?mensaje=creado"); 
            exit; 
        } 
    } 
} 
include("../../templates/header.php"); 
?> 

<section class="admin-page-head">
    <span class="admin-eyebrow">/// CATEGORÍAS</span>
    <h1>Nueva categoría</h1>
</section>

<?php if ($mensajeError !== "") { ?>
    <div class="alert alert-danger" role="alert">
        <?php echo escapar($mensajeError); ?>
    </div>
<?php } ?>

<div class="card">
    <div class="card-header">Datos de la categoría</div>
    <div class="card-body">
        <form action="" method="post">

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input
                    type="text"
                    class="form-control"
                    name="nombre"
                    id="nombre"
                    value="<?php echo escapar($nombre); ?>" 
                    placeholder="Ej: Hoodies"
                    maxlength="100"
                    required
                    autofocus
                />
            </div>

            <div class="admin-form-actions">
                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Agregar
                </button>

                <a class="btn btn-secondary" href="index.php">Cancelar</a>
            </div>
            
        </form>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>