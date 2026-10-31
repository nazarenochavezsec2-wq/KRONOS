<?php
// Sube una foto a assets/img/equipo/.
// Devuelve: nombre del archivo guardado | null si no se subió nada | false si hubo error
function subirFoto($archivo) {
    if (!isset($archivo) || $archivo["error"] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($archivo["error"] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Máximo 2 MB
    if ($archivo["size"] > 2 * 1024 * 1024) {
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

    $carpeta = __DIR__ . "/../../../assets/img/equipo/";
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $nombre = uniqid("eq_") . "." . $permitidos[$mime];
    if (!move_uploaded_file($archivo["tmp_name"], $carpeta . $nombre)) {
        return false;
    }
    return $nombre;
}

// Borra una foto del disco (si existe)
function borrarFoto($nombre) {
    if ($nombre) {
        $ruta = __DIR__ . "/../../../assets/img/equipo/" . basename($nombre);
        if (is_file($ruta)) {
            unlink($ruta);
        }
    }
}