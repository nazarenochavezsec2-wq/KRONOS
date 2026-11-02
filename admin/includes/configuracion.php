<?php 
/*
 * CONFIGURACIÓN EDITABLE DE LA PORTADA
 * Ubicación: admin/includes/configuracion.php
 *
 * Lo usan el panel (secciones/configuraciones) y la tienda (index.php).
 * Si la tabla no existe o está vacía, se usan los textos originales,
 * así la tienda nunca se rompe.
 */

// Textos originales de la tienda (se usan si no hay nada guardado en la BD)
function configuracionPorDefecto() {
    return [
        "ticker_frases"        => "BIENVENIDOS A HIVE DIGITAL WORLD\nCOLECCIÓN STREETWEAR Y2K\nENVÍOS A TODO EL MUNDO\nNUEVA GENERACIÓN / NUEVO ESTILO",
        "franja_frases"        => "UNIVERSO HIVE\nNUEVA GENERACIÓN\nCULTURA URBANA\nY2K PARA SIEMPRE",

        "hero_info_1_etiqueta" => "COLECCIÓN",
        "hero_info_1_valor"    => "Y2K / 001",
        "hero_info_2_etiqueta" => "ESTILO",
        "hero_info_2_valor"    => "STREET FUTURISTA",
        "hero_info_3_etiqueta" => "ESTADO",
        "hero_info_3_valor"    => "DISPONIBLE AHORA",

        "hero_imagen"          => "",   // vacío = se usa img/portada.png
        "hero_img_sup_izq"     => "HIVE® EDITORIAL",
        "hero_img_sup_der"     => "001 / 026",
        "hero_img_inf_izq"     => "GENERACIÓN FUTURO",
        "hero_img_inf_der"     => "DESDE MMXXVI ✦",

        "hero_card_etiqueta"   => "NUEVO DROP",
        "hero_card_titulo"     => "ENERGÍA\nY2K.",
    ];
}

// Largo máximo de cada campo de texto (coincide con la tabla de la BD)
function configuracionLimites() {
    return [
        "ticker_frases"        => 2000,
        "franja_frases"        => 2000,
        "hero_info_1_etiqueta" => 60,
        "hero_info_1_valor"    => 100,
        "hero_info_2_etiqueta" => 60,
        "hero_info_2_valor"    => 100,
        "hero_info_3_etiqueta" => 60,
        "hero_info_3_valor"    => 100,
        "hero_img_sup_izq"     => 80,
        "hero_img_sup_der"     => 80,
        "hero_img_inf_izq"     => 80,
        "hero_img_inf_der"     => 80,
        "hero_card_etiqueta"   => 60,
        "hero_card_titulo"     => 100,
    ];
}

// Devuelve la configuración guardada (o los valores por defecto)
function cargarConfiguracion($conexion) {
    $config = configuracionPorDefecto();

    try {
        $sentencia = $conexion->prepare("SELECT * FROM tbl_configuraciones WHERE id = 1");
        $sentencia->execute();
        $fila = $sentencia->fetch(PDO::FETCH_ASSOC);

        if ($fila) {
            foreach ($config as $clave => $valor) {
                if (array_key_exists($clave, $fila) && $fila[$clave] !== null) {
                    $config[$clave] = $fila[$clave];
                }
            }
        }
    } catch (PDOException $e) {
        // Si la tabla no existe, se quedan los valores por defecto
    }

    return $config;
}

// Convierte un texto con una frase por línea en una lista (sin líneas vacías)
function lineasConfig($texto) {
    $lineas = preg_split('/\R/u', (string)$texto);
    $lineas = array_map('trim', $lineas);

    return array_values(array_filter($lineas, function ($linea) {
        return $linea !== "";
    }));
}