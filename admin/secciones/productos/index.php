<?php 

require_once(__DIR__ . "/../../includes/autenticacion.php"); 

include("../../bd.php"); 

// Escapa texto antes de imprimirlo en pantalla
if (!function_exists("escapar")) {
    function escapar($valor) {
        return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
    }
}

// Formatea precios como en la tienda: 48900 -> $48.900
if (!function_exists("formatoPrecio")) {
    function formatoPrecio($valor) {
        return "$" . number_format((float)$valor, 0, ",", ".");
    }
}

if ( isset($_POST["accion"]) && $_POST["accion"] === "eliminar" ) { 
    $txtID = isset($_POST["txtID"]) ? $_POST["txtID"] : ""; 
    
    $sentencia = $conexion->prepare( "SELECT imagen FROM tbl_productos WHERE id = :ID" ); 
    $sentencia->bindParam(":ID", $txtID); 
    $sentencia->execute(); 
    $registro = $sentencia->fetch(PDO::FETCH_ASSOC); 

    if ($registro) { 
        $rutaImagen = "../../../assets/img/productos/" . $registro["imagen"]; 
        if (is_file($rutaImagen)) { 
            unlink($rutaImagen); 
            }
    }

    $sentencia = $conexion->prepare( "DELETE FROM tbl_productos WHERE id = :ID" ); 

    $sentencia->bindParam(":ID", $txtID); 

    $sentencia->execute();

    header("Location: index.php?mensaje=eliminado"); 
    exit; 
}

    $sentencia = $conexion->prepare( "SELECT * FROM tbl_productos" ); 

    $sentencia->execute(); 

    $lista_productos = $sentencia->fetchAll(PDO::FETCH_ASSOC); 

    include("../../templates/header.php"); 
?>

<section class="admin-page-head">
    <span class="admin-eyebrow">/// SECCIÓN</span>
    <h1>Productos</h1>
</section>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Listado de Productos</span>
        <a class="btn btn-primary" href="crear.php">+ Agregar Producto</a> 
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
                        <th scope="col">Imagen</th> 
                        <th scope="col">Precio base y descuento</th> 
                        <th scope="col">Categoría y medidas</th>
                        <th scope="col">Stock</th>  
                        <th scope="col">Estado</th> 
                        <th scope="col">Acciones</th> 
                    </tr> 
                    
                </thead>
                 <tbody> 
                    <?php foreach($lista_productos as $producto){?> 
                    <tr class=""> 
                        <td class="admin-cell-id"><?php echo escapar($producto["id"]); ?></td> 
 
                        <td> 
                            <strong><?php echo escapar($producto["nombre"]); ?></strong> 
                        </td> 
 
                        <td> 
                            <img src="/Kronos/assets/img/productos/<?php echo escapar($producto['imagen']); ?>" 
                            alt="<?php echo escapar($producto['nombre']); ?>" 
                            class="img-thumbnail" 
                            width="100" > 
                        </td>
 
                        <td> 
                            <strong>Base:</strong> <?php echo formatoPrecio($producto["precio_base"]); ?><br> 

                            <strong>Descuento:</strong> 
                            <?php if ($producto["precio_descuento"] !== null && $producto["precio_descuento"] !== "") { 
                                echo formatoPrecio($producto["precio_descuento"]); 
                            } else { 
                                echo "—"; 
                            } ?> 
                        </td> 
 
                        <td> 
                            <strong>Categoría:</strong> <?php echo escapar($producto["categoria"]); ?><br> 

                            <strong>Temporada:</strong> <?php echo escapar($producto["temporada"]); ?><br> 

                            <strong>Peso:</strong> <?php echo escapar($producto["peso"]); ?> kg<br> 

                            <strong>Dimensiones:</strong> <?php echo escapar($producto["dimensiones"]); ?> 
                        </td> 

                        <td class="admin-cell-id <?php echo ((int)$producto['stock'] <= 0) ? 'admin-stock-empty' : ''; ?>"> 
                            <?php echo escapar($producto["stock"]); ?> 
                        </td> 

                        <td> 
                            <?php if ((int)$producto["estado"] === 1) { ?> 
                                <span class="admin-badge">Activo</span> 
                            <?php } else { ?> 
                                <span class="admin-badge admin-badge-off">Inactivo</span> 
                            <?php } ?> 
                        </td> 
                       
                        <td> 
                            <div class="d-flex gap-2"> 
                                <a class="btn btn-info btn-sm" 
                                href="editar.php?txtID=<?php echo escapar($producto['id']); ?>" > 
                                Editar 
                                </a>  
                                <form 
                                action="index.php" 
                                method="post" 
                                onsubmit="return confirm('¿Desea eliminar este producto?');" > 
                                <input 
                                type="hidden" 
                                name="txtID" 
                                value="<?php echo escapar($producto['id']); ?>" > 
                                
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