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

if ( isset($_POST["accion"]) && $_POST["accion"] === "eliminar" ) { 
    $txtID = isset($_POST["txtID"]) ? (int)$_POST["txtID"] : 0; 

    // Una categoría con productos asignados NO se puede eliminar 
    $sentencia = $conexion->prepare( "SELECT COUNT(*) FROM tbl_productos WHERE id_categoria = :ID" ); 
    $sentencia->bindParam(":ID", $txtID); 
    $sentencia->execute(); 
    $cantidad = (int)$sentencia->fetchColumn(); 

    if ($cantidad > 0) { 
        $mensajeError = "No se puede eliminar la categoría porque tiene " . $cantidad . " producto(s) asignado(s). Cambiá la categoría de esos productos o eliminalos primero."; 
    } else { 
        try { 
            $sentencia = $conexion->prepare( "DELETE FROM tbl_categorias WHERE id = :ID" ); 
            $sentencia->bindParam(":ID", $txtID); 
            $sentencia->execute(); 

            header("Location: index.php?mensaje=eliminado"); 
            exit; 
        } catch (PDOException $e) { 
            // Segunda barrera: la clave foránea de la base de datos también lo impide 
            $mensajeError = "No se puede eliminar la categoría porque está siendo utilizada por productos."; 
        } 
    } 
}

    // Cada categoría con la cantidad de productos que la usan 
    $sentencia = $conexion->prepare( 
        "SELECT t.id, t.nombre, COUNT(p.id) AS total_productos 
         FROM tbl_categorias t 
         LEFT JOIN tbl_productos p ON p.id_categoria = t.id 
         GROUP BY t.id, t.nombre 
         ORDER BY t.nombre" 
    ); 

    $sentencia->execute(); 

    $lista_categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC); 

    include("../../templates/header.php"); 
?>

<section class="admin-page-head">
    <span class="admin-eyebrow">/// SECCIÓN</span>
    <h1>Categorías</h1>
</section>

<?php if ($mensajeError !== "") { ?>
    <div class="alert alert-danger" role="alert">
        <?php echo escapar($mensajeError); ?>
    </div>
<?php } ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Listado de Categorías</span>
        <a class="btn btn-primary" href="crear.php">+ Agregar Categoría</a> 
    </div>
    <div class="card-body">
        <div
            class="table-responsive-sm"
        >
            <table
                class="table"
            >
                <thead>
                    <tr> 
                        <th scope="col">ID</th> 
                        <th scope="col">Nombre</th> 
                        <th scope="col">Productos asignados</th> 
                        <th scope="col">Acciones</th> 
                    </tr> 
                </thead>
                 <tbody> 
                    <?php foreach($lista_categorias as $categoria){?> 
                    <tr class=""> 
                        <td class="admin-cell-id"><?php echo escapar($categoria["id"]); ?></td> 

                        <td> 
                            <strong><?php echo escapar($categoria["nombre"]); ?></strong> 
                        </td> 

                        <td class="admin-cell-id"> 
                            <?php echo escapar($categoria["total_productos"]); ?> 
                        </td> 

                        <td> 
                            <div class="d-flex gap-2"> 
                                <a class="btn btn-info btn-sm" 
                                href="editar.php?txtID=<?php echo escapar($categoria['id']); ?>" > 
                                Editar 
                                </a>  
                                <form 
                                action="index.php" 
                                method="post" 
                                onsubmit="return confirm('¿Desea eliminar esta categoría?');" > 
                                <input 
                                type="hidden" 
                                name="txtID" 
                                value="<?php echo escapar($categoria['id']); ?>" > 
                                
                                <?php if ((int)$categoria["total_productos"] > 0) { ?> 
                                    <button 
                                    type="button" 
                                    class="btn btn-danger btn-sm" 
                                    disabled 
                                    title="No se puede eliminar: tiene productos asignados" > 
                                    Eliminar 
                                    </button> 
                                <?php } else { ?> 
                                    <button 
                                    type="submit" 
                                    name="accion" 
                                    value="eliminar" 
                                    class="btn btn-danger btn-sm" > 
                                    Eliminar 
                                    </button> 
                                <?php } ?> 
                            </form> 
                            </div> 
                        </td>
                    </tr> 
                    <?php } ?> 
                </tbody> 
            </table>
        </div>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>