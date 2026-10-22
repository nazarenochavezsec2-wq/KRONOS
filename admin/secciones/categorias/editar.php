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

if ($_POST) { 
    // Recuperamos el ID y el nuevo nombre 
    $txtID = isset($_POST["txtID"]) ? (int)$_POST["txtID"] : 0; 
    $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : ""; 

    if ($nombre === "") { 
        $mensajeError = "Ingresá el nombre de la categoría."; 
    } elseif (mb_strlen($nombre, "UTF-8") > 100) { 
        $mensajeError = "El nombre no puede superar los 100 caracteres."; 
    } else { 
        // Evitamos repetir el nombre de OTRA categoría 
        $sentencia = $conexion->prepare( "SELECT COUNT(*) FROM tbl_categorias WHERE nombre = :nombre AND id <> :ID" ); 
        $sentencia->bindParam(":nombre", $nombre); 
        $sentencia->bindParam(":ID", $txtID); 
        $sentencia->execute(); 

        if ((int)$sentencia->fetchColumn() > 0) { 
            $mensajeError = "Ya existe otra categoría con ese nombre."; 
        } else { 
            $sentencia = $conexion->prepare( 
                "UPDATE tbl_categorias SET nombre = :nombre WHERE id = :ID" 
            ); 
            $sentencia->bindParam(":nombre", $nombre); 
            $sentencia->bindParam(":ID", $txtID); 
            $sentencia->execute(); 

            header("Location: index.php?mensaje=actualizado"); 
            exit; 
        } 
    } 

    // Si hubo un error, volvemos a mostrar el formulario con lo que escribió 
    $registro = ["id" => $txtID, "nombre" => $nombre]; 

} else { 
    $txtID = isset($_GET["txtID"]) ? (int)$_GET["txtID"] : 0; 

    $sentencia = $conexion->prepare( "SELECT * FROM tbl_categorias WHERE id = :ID" ); 
    $sentencia->bindParam(":ID", $txtID); 
    $sentencia->execute(); 
    $registro = $sentencia->fetch(PDO::FETCH_ASSOC); 

    // Si la categoría no existe, volvemos al listado 
    if (!$registro) { 
        header("Location: index.php"); 
        exit; 
    } 
} 

include("../../templates/header.php"); 
?> 

<section class="admin-page-head">
    <span class="admin-eyebrow">/// CATEGORÍAS</span>
    <h1>Editar categoría</h1>
</section>

<?php if ($mensajeError !== "") { ?>
    <div class="alert alert-danger" role="alert">
        <?php echo escapar($mensajeError); ?>
    </div>
<?php } ?>

<div class="card">
    <div class="card-header">Datos de la categoría #<?php echo escapar($registro['id']); ?></div>
    <div class="card-body">
        <form action="" method="post">

        <input type="hidden" name="txtID" value="<?php echo escapar($registro['id']); ?>"> 

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input
                    type="text"
                    class="form-control"
                    name="nombre"
                    id="nombre"
                    value="<?php echo escapar($registro['nombre']); ?>" 
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
                    Actualizar 
                </button>

                <a class="btn btn-secondary" href="index.php">Cancelar</a>
            </div>
            
        </form>
    </div>
</div>
<?php include("../../templates/footer.php"); ?>