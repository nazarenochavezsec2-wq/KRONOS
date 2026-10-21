<?php 

require_once(__DIR__ . "/../../includes/autenticacion.php"); 

include("../../bd.php"); 

if ( isset($_POST["accion"]) && $_POST["accion"] === "eliminar" ) { 
    $txtID = isset($_POST["txtID"]) ? $_POST["txtID"] : ""; 
    
    $sentencia = $conexion->prepare( "SELECT imagen FROM tbl_portafolio WHERE ID = :ID" ); 
    $sentencia->bindParam(":ID", $txtID); 
    $sentencia->execute(); 
    $registro = $sentencia->fetch(PDO::FETCH_ASSOC); 

    if ($registro) { 
        $rutaImagen = "../../../assets/img/portfolio/" . $registro["imagen"]; 
        if (is_file($rutaImagen)) { 
            unlink($rutaImagen); 
            }
    }

    $sentencia = $conexion->prepare( "DELETE FROM tbl_portafolio WHERE ID = :ID" ); 

    $sentencia->bindParam(":ID", $txtID); 

    $sentencia->execute();

    header("Location: index.php?mensaje=eliminado"); 
    exit; 
}

    $sentencia = $conexion->prepare( "SELECT * FROM tbl_portafolio" ); 

    $sentencia->execute(); 

    $lista_portafolio = $sentencia->fetchAll(PDO::FETCH_ASSOC); 

    include("../../templates/header.php"); 
?>

<section class="admin-page-head">
    <span class="admin-eyebrow">/// SECCIÓN</span>
    <h1>Portafolio</h1>
</section>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Listado de proyectos</span>
        <a class="btn btn-primary" href="crear.php">+ Agregar registro</a> 
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
                        <th scope="col">Proyecto</th> 
                        <th scope="col">Imagen</th> 
                        <th scope="col">Descripción</th> 
                        <th scope="col">Cliente y categoría</th> 
                        <th scope="col">Acciones</th> 
                    </tr> 
                    
                </thead>
                 <tbody> 
                    <?php foreach($lista_portafolio as $registros){?> 
                    <tr class=""> 
                        <td class="admin-cell-id"><?php echo $registros["ID"]; ?></td> 
 
                        <td> 
                            <strong><?php echo $registros["titulo"]; ?></strong> 
                            <br> 
 
                            <?php echo $registros["subtitulo"]; ?> 
                            <br> 
 
                            <a class="admin-cell-link" href="<?php echo $registros["url"]; ?>" target="_blank"><?php echo $registros["url"]; ?> 
                            </a> 
                        </td> 
 
 
                        <td> 
                            <img src="/website/assets/img/portfolio/<?php echo $registros['imagen']; ?>" 
                            alt="<?php echo $registros['titulo']; ?>" 
                            class="img-thumbnail" 
                            width="100" > 
                        </td>
 
                        <td><?php echo $registros["descripcion"]; ?></td> 
 
                        <td> 
                            <strong>Cliente:</strong> <?php echo $registros["cliente"]; ?><br> 
 
                            <strong>Categoría:</strong> <?php echo $registros["categoria"]; ?> 
                        </td> 
 
                       
                        <td> 
                            <div class="d-flex gap-2"> 
                                <a class="btn btn-info btn-sm" 
                                href="editar.php?txtID=<?php echo $registros['ID']; ?>" > 
                                Editar 
                                </a>  
                                <form 
                                action="index.php" 
                                method="post" 
                                onsubmit="return confirm('¿Desea eliminar este proyecto?');" > 
                                <input 
                                type="hidden" 
                                name="txtID" 
                                value="<?php echo $registros['ID']; ?>" > 
                                
                                <button 
                                type="submit" 
                                name="accion" 
                                value="eliminar" 
                                class="btn btn-danger btn-sm" > 
                                Eliminar 
                                </button> 
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