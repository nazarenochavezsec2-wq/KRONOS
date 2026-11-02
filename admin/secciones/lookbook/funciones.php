<?php
// Guarda UNA imagen en assets/img/lookbook/.
// Devuelve: nombre del archivo | null si no se subió nada | false si hubo un error
function subirImagenLookbook($archivo) {
    if (!isset($archivo) || $archivo["error"] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($archivo["error"] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Máximo 5 MB por foto
    if ($archivo["size"] > 5 * 1024 * 1024) {
        return false;
    }

    $permitidos = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp",
    ];
    $mime = mime_content_type($archivo["tmp_name"]);
    if (!isset($permitidos[$mime])) {
        return false;
    }

    $carpeta = __DIR__ . "/../../../assets/img/lookbook/";
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $nombre = "look_" . bin2hex(random_bytes(8)) . "." . $permitidos[$mime];
    if (!move_uploaded_file($archivo["tmp_name"], $carpeta . $nombre)) {
        return false;
    }
    return $nombre;
}

// Borra una imagen del disco (si existe)
function borrarImagenLookbook($nombre) {
    if ($nombre) {
        $ruta = __DIR__ . "/../../../assets/img/lookbook/" . basename($nombre);
        if (is_file($ruta)) {
            unlink($ruta);
        }
    }
}

// Convierte $_FILES["fotos"] (subida múltiple) en una lista de archivos individuales
function normalizarArchivos($campo) {
    $lista = [];

    if (!isset($campo["name"]) || !is_array($campo["name"])) {
        return $lista;
    }

    foreach ($campo["name"] as $i => $nombre) {
        $lista[] = [
            "name"     => $nombre,
            "type"     => $campo["type"][$i],
            "tmp_name" => $campo["tmp_name"][$i],
            "error"    => $campo["error"][$i],
            "size"     => $campo["size"][$i],
        ];
    }
    return $lista;
}