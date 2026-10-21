<?php 
include("../../bd.php"); 
if ($_POST) { 
$titulo = isset($_POST["titulo"]) ? $_POST["titulo"] : ""; 
$subtitulo = isset($_POST["subtitulo"])? $_POST["subtitulo"]: ""; 
$imagen = isset($_FILES["imagen"]["name"])? $_FILES["imagen"]["name"]: ""; 
$descripcion = isset($_POST["descripcion"])? $_POST["descripcion"]: ""; 
$cliente = isset($_POST["cliente"])? $_POST["cliente"]: ""; 
$categoria = isset($_POST["categoria"])? $_POST["categoria"]: ""; 
$url = isset($_POST["url"])? $_POST["url"]: "";

$imagenTemporal = $_FILES["imagen"]["tmp_name"]; 
$rutaDestino = "../../../assets/img/portfolio/" . $imagen; 

move_uploaded_file($imagenTemporal, $rutaDestino);

$sentencia = $conexion->prepare( 
    "INSERT INTO tbl_portafolio 
        (ID, titulo, subtitulo, imagen, descripcion, cliente, categoria, url) 
    VALUES 
        (NULL, :titulo, :subtitulo, :imagen, :descripcion, :cliente, :categoria, :url)" 
); 

$sentencia->bindParam(":titulo", $titulo); 
$sentencia->bindParam(":subtitulo", $subtitulo); 
$sentencia->bindParam(":imagen", $imagen); 
$sentencia->bindParam(":descripcion", $descripcion); 
$sentencia->bindParam(":cliente", $cliente); 
$sentencia->bindParam(":categoria", $categoria); 
$sentencia->bindParam(":url", $url); 

$sentencia->execute();

header("Location: index.php?mensaje=creado"); 
exit; 
} 
include("../../templates/header.php"); 
?> 
<div class="alert alert-success"> 
    Conexión con la base de datos realizada correctamente. 
</div>
<div class="card">
    <div class="card-header">Nuevo proyecto del Portafolio</div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">

            <div class="mb-3">
                <label for="titulo" class="form-label">Titulo</label>
                <input
                    type="text"
                    class="form-control"
                    name="titulo"
                    id="titulo"
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
                    placeholder="Subtitulo"
                />
            </div>

            <div class="mb-3">
                <label for="imagen" class="form-label">Imágen</label>
                <input
                    type="file"
                    class="form-control"
                    name="imagen"
                    id="imagen"
                    placeholder="Imagen"
                />            
            </div>

            <div class="mb-3">
                <label for="" class="form-label"></label>
                <textarea class="form-control" name="descripcion" id="descripcion" rows="3" placeholder="Descripcion"></textarea>
            </div>
            
            <div class="mb-3">
                <label for="cliente" class="form-label">Cliente</label>
                <input
                    type="text"
                    class="form-control"
                    name="cliente"
                    id="cliente"
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
                    placeholder="https://ejemplo.com"
                />
            </div>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Agregar
            </button>

            <a class="btn btn-primary ms-2" href="index.php">Cancelar</a>
            

            
        </form>
    </div>
</div>

<?php include("../../templates/footer.php"); ?> 