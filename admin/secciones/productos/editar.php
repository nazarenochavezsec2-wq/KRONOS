<?php 
include("../../bd.php"); 

if ($_POST) { 
// Recuperamos el ID del proyecto 
$txtID = isset($_POST["txtID"]) ? $_POST["txtID"] : ""; 
// Capturamos los datos modificados 
$titulo = isset($_POST["titulo"]) ? $_POST["titulo"] : ""; 
$subtitulo = isset($_POST["subtitulo"]) ? $_POST["subtitulo"] : ""; 
$descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : ""; 
$cliente = isset($_POST["cliente"]) ? $_POST["cliente"] : ""; 
$categoria = isset($_POST["categoria"]) ? $_POST["categoria"] : ""; 
$url = isset($_POST["url"]) ? $_POST["url"] : ""; 
// Recuperamos el nombre de la imagen actual 
$imagen = isset($_POST["imagen_actual"]) 
? $_POST["imagen_actual"] 
: ""; 
// Aquí procesaremos una nueva imagen y actualizaremos el registro 

if ( isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK ) { 
    $imagen = $_FILES["imagen"]["name"]; 
    $imagenTemporal = $_FILES["imagen"]["tmp_name"]; 
    $rutaDestino = "../../../assets/img/portfolio/" . $imagen; 
    move_uploaded_file($imagenTemporal, $rutaDestino); 
}

$sentencia = $conexion->prepare( 
    "UPDATE tbl_portafolio 
     SET 
        titulo = :titulo, 
        subtitulo = :subtitulo, 
        imagen = :imagen, 
        descripcion = :descripcion, 
        cliente = :cliente, 
        categoria = :categoria, 
        url = :url 
     WHERE ID = :ID" 
);

$sentencia->bindParam(":titulo", $titulo); 
$sentencia->bindParam(":subtitulo", $subtitulo); 
$sentencia->bindParam(":imagen", $imagen); 
$sentencia->bindParam(":descripcion", $descripcion); 
$sentencia->bindParam(":cliente", $cliente); 
$sentencia->bindParam(":categoria", $categoria); 
$sentencia->bindParam(":url", $url); 
$sentencia->bindParam(":ID", $txtID); 

$sentencia->execute(); 
header("Location: index.php?mensaje=actualizado"); 
exit; 
} 

$txtID = isset($_GET["txtID"]) ? $_GET["txtID"] : ""; 

$sentencia = $conexion->prepare( 
"SELECT * FROM tbl_portafolio WHERE ID = :ID" 
); 

$sentencia->bindParam(":ID", $txtID); 
$sentencia->execute(); 
$registro = $sentencia->fetch(PDO::FETCH_ASSOC);

include("../../templates/header.php"); 
?> 
<div class="card">
    <div class="card-header">Editar proyecto del Portafolio</div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">

        <input type="hidden" name="txtID" value="<?php echo $registro['ID']; ?>"> 

        <input type="hidden" name="imagen_actual" value="<?php echo $registro['imagen']; ?>"> 


            <div class="mb-3">
                <label for="titulo" class="form-label">Titulo</label>
                <input
                    type="text"
                    class="form-control"
                    name="titulo"
                    id="titulo"
                    value="<?php echo $registro['titulo']; ?>" 
                    placeholder="Titulo"
                />
            </div>

            <div class="mb-3">
                <label for="subtitulo" class="form-label">Subtitulo</label>
                <input
                    type="text"
                    class="form-control"
                    name="subtitulo"
                    id="subtitulo"
                    value="<?php echo $registro['titulo']; ?>" 
                    placeholder="Subtitulo"
                />
            </div>

            <div class="mb-3"> 
                <label class="form-label">Imagen actual</label> 
                <br> 
                <img 
                src="/website/assets/img/portfolio/<?php echo $registro['imagen']; ?>" 
                alt="<?php echo $registro['titulo']; ?>" 
                class="img-thumbnail mb-2" 
                width="120" 
                > 
                <p class="mb-2"> 
                    <?php echo $registro["imagen"]; ?> 
                </p> 
                <label for="imagen" class="form-label"> 
                    Seleccionar una nueva imagen 
                </label> 
                <input 
                type="file" 
                class="form-control" 
                name="imagen" 
                id="imagen" 
                > 
            </div>

            <div class="mb-3">
                <label for="" class="form-label"></label>
                <textarea 
                class="form-control" 
                name="descripcion" 
                id="descripcion" 
                rows="3" 
                placeholder="Descripcion"
                ><?php echo $registro["descripcion"]; ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="cliente" class="form-label">Cliente</label>
                <input
                    type="text"
                    class="form-control"
                    name="cliente"
                    id="cliente"
                    value="<?php echo $registro['cliente']; ?>"
                    placeholder="Cliente"
                />
            </div> 

            <div class="mb-3">
                <label for="categoria" class="form-label">Categoria</label>
                <input
                    type="text"
                    class="form-control"
                    name="categoria"
                    id="categoria"
                    value="<?php echo $registro['categoria']; ?>" 
                    placeholder="Categoria"
                />
            </div>

            <div class="mb-3">
                <label for="url" class="form-label">URL</label>
                <input
                    type="url"
                    class="form-control"
                    name="url"
                    id="url"
                    value="<?php echo $registro['url']; ?>" 
                    placeholder="https://ejemplo.com"
                />
            </div>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Actualizar 
            </button>

            <a class="btn btn-primary ms-2" href="index.php">Cancelar</a>
            

            
        </form>
    </div>
</div>
<?php include("../../templates/footer.php"); ?>