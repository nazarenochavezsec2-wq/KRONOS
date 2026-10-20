<?php 
$servidor = "localhost"; 
$baseDeDatos = "website"; 
$usuario = "root"; 
$contrasenia = ""; 
try { 
$conexion = new PDO( 
"mysql:host=$servidor;dbname=$baseDeDatos;charset=utf8mb4", 
$usuario, 
$contrasenia 
); 
$conexion->setAttribute( 
PDO::ATTR_ERRMODE, 
PDO::ERRMODE_EXCEPTION 
); 
} catch (PDOException $error) { 
die("Error de conexión: " . $error->getMessage()); 
}