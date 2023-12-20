<?php


// Configuración de la conexión a la base de datos
$host = 'localhost';
$dbname = 'biblioteca';
$user = 'root';
$password = '1234';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("Error de conexión: " . $e->getMessage());
}

?>