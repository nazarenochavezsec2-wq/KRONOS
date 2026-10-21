<?php 
require_once(__DIR__ . "/../../includes/autenticacion.php"); 

include("../../bd.php"); 

// Escapa texto antes de imprimirlo en pantalla
if (!function_exists("escapar")) {
    function escapar($valor) {
        return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
    }
}

if ($_POST) { 
// Recuperamos el ID del producto 
$txtID = isset($_POST["txtID"]) ? $_POST["txtID"] : ""; 
// Capturamos los datos modificados 
$nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : ""; 
$desc = isset($_POST["desc"]) ? $_POST["desc"] : ""; 
$precio_base = isset($_POST["precio_base"]) ? (int)$_POST["precio_base"] : 0; 
// El precio con descuento es opcional: si viene vacío se guarda NULL 
$precio_descuento = (isset($_POST["precio_descuento"]) && $_POST["precio_descuento"] !== "") 
    ? (int)$_POST["precio_descuento"] 
    : null; 
$categoria = isset($_POST["categoria"]) ? $_POST["categoria"] : ""; 
$temporada = isset($_POST["temporada"]) ? $_POST["temporada"] : ""; 
$peso = isset($_POST["peso"]) ? (float)$_POST["peso"] : 0; 
$dimensiones = isset($_POST["dimensiones"]) ? (float)$_POST["dimensiones"] : 0; 
$estado = isset($_POST["estado"]) ? (int)$_POST["estado"] : 1; 
$stock = isset($_POST["stock"]) ? (int)$_POST["stock"] : 0; 
// Recuperamos el nombre de la imagen actual 
$imagen = isset($_POST["imagen_actual"]) 
? $_POST["imagen_actual"] 
: ""; 
// Aquí procesaremos una nueva imagen y actualizaremos el registro 

if ( isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK ) { 
    $imagen = $_FILES["imagen"]["name"]; 
    $imagenTemporal = $_FILES["imagen"]["tmp_name"]; 
    $rutaDestino = "../../../assets/img/productos/" . $imagen; 
    move_uploaded_file($imagenTemporal, $rutaDestino); 
}

$sentencia = $conexion->prepare( 
    "UPDATE tbl_productos 
     SET 
        nombre = :nombre, 
        `desc` = :desc, 
        imagen = :imagen, 
        precio_base = :precio_base, 
        precio_descuento = :precio_descuento, 
        categoria = :categoria,
        temporada = :temporada, 
        peso = :peso, 
        dimensiones = :dimensiones, 
        estado = :estado, 
        stock = :stock 
     WHERE id = :ID" 
);

$sentencia->bindParam(":nombre", $nombre); 
$sentencia->bindParam(":desc", $desc); 
$sentencia->bindParam(":imagen", $imagen); 
$sentencia->bindParam(":precio_base", $precio_base); 
$sentencia->bindValue(":precio_descuento", $precio_descuento, $precio_descuento === null ? PDO::PARAM_NULL : PDO::PARAM_INT); 
$sentencia->bindParam(":categoria", $categoria); 
$sentencia->bindParam(":temporada", $temporada); 
$sentencia->bindParam(":peso", $peso); 
$sentencia->bindParam(":dimensiones", $dimensiones); 
$sentencia->bindParam(":estado", $estado); 
$sentencia->bindParam(":stock", $stock); 
$sentencia->bindParam(":ID", $txtID); 

$sentencia->execute(); 
header("Location: index.php?mensaje=actualizado"); 
exit; 
} 

$txtID = isset($_GET["txtID"]) ? $_GET["txtID"] : ""; 

$sentencia = $conexion->prepare( 
"SELECT * FROM tbl_productos WHERE id = :ID" 
); 

$sentencia->bindParam(":ID", $txtID); 
$sentencia->execute(); 
$registro = $sentencia->fetch(PDO::FETCH_ASSOC);

// Si el producto no existe, volvemos al listado 
if (!$registro) { 
    header("Location: index.php"); 
    exit; 
} 

include("../../templates/header.php"); 
?> 

<section class="admin-page-head">
    <span class="admin-eyebrow">/// PRODUCTOS</span>
    <h1>Editar producto</h1>
</section>

<div class="card">
    <div class="card-header">Datos del producto #<?php echo escapar($registro['id']); ?></div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">

        <input type="hidden" name="txtID" value="<?php echo escapar($registro['id']); ?>"> 

        <input type="hidden" name="imagen_actual" value="<?php echo escapar($registro['imagen']); ?>"> 


            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input
                    type="text"
                    class="form-control"
                    name="nombre"
                    id="nombre"
                    value="<?php echo escapar($registro['nombre']); ?>" 
                    placeholder="Nombre del producto"
                    required
                />
            </div>

            <div class="mb-3"> 
                <label class="form-label">Imagen actual</label> 
                <br> 
                <img 
                src="/Kronos/assets/img/productos/<?php echo escapar($registro['imagen']); ?>" 
                alt="<?php echo escapar($registro['nombre']); ?>" 
                class="img-thumbnail mb-2" 
                width="120" 
                > 
                <p class="admin-file-name"> 
                    <?php echo escapar($registro["imagen"]); ?> 
                </p> 
                <label for="imagen" class="form-label"> 
                    Seleccionar una nueva imagen 
                </label> 
                <input 
                type="file" 
                class="form-control" 
                name="imagen" 
                id="imagen" 
                accept="image/*" 
                > 
            </div>

            <div class="mb-3">
                <label for="desc" class="form-label">Descripción</label>
                <textarea 
                class="form-control" 
                name="desc" 
                id="desc" 
                rows="3" 
                placeholder="Descripción del producto"
                required
                ><?php echo escapar($registro["desc"]); ?></textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="precio_base" class="form-label">Precio base ($)</label>
                    <input
                        type="number"
                        class="form-control"
                        name="precio_base"
                        id="precio_base"
                        min="0"
                        step="1"
                        value="<?php echo escapar($registro['precio_base']); ?>"
                        required
                    />
                </div>

                <div class="col-md-6">
                    <label for="precio_descuento" class="form-label">Precio con descuento ($)</label>
                    <input
                        type="number"
                        class="form-control"
                        name="precio_descuento"
                        id="precio_descuento"
                        min="0"
                        step="1"
                        value="<?php echo escapar($registro['precio_descuento']); ?>"
                        placeholder="Opcional"
                    />
                    <div class="form-text">Dejalo vacío si el producto no tiene descuento.</div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="categoria" class="form-label">Categoría</label>
                    <input
                        type="text"
                        class="form-control"
                        name="categoria"
                        id="categoria"
                        value="<?php echo escapar($registro['categoria']); ?>" 
                        placeholder="Categoría"
                        required
                    />
                </div>

                <div class="col-md-6">
                    <label for="temporada" class="form-label">Temporada</label>
                    <input
                        type="text"
                        class="form-control"
                        name="temporada"
                        id="temporada"
                        value="<?php echo escapar($registro['temporada']); ?>" 
                        placeholder="Ej: Invierno 2026"
                        required
                    />
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="peso" class="form-label">Peso (kg)</label>
                    <input
                        type="number"
                        class="form-control"
                        name="peso"
                        id="peso"
                        min="0"
                        step="any"
                        value="<?php echo escapar($registro['peso']); ?>"
                        required
                    />
                </div>

                <div class="col-md-6">
                    <label for="dimensiones" class="form-label">Dimensiones</label>
                    <input
                        type="number"
                        class="form-control"
                        name="dimensiones"
                        id="dimensiones"
                        min="0"
                        step="any"
                        value="<?php echo escapar($registro['dimensiones']); ?>"
                        required
                    />
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="stock" class="form-label">Stock</label>
                    <input
                        type="number"
                        class="form-control"
                        name="stock"
                        id="stock"
                        min="0"
                        step="1"
                        value="<?php echo escapar($registro['stock']); ?>"
                        required
                    />
                </div>

                <div class="col-md-6">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" name="estado" id="estado">
                        <option value="1" <?php echo ((int)$registro['estado'] === 1) ? 'selected' : ''; ?>>Activo</option>
                        <option value="0" <?php echo ((int)$registro['estado'] !== 1) ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
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