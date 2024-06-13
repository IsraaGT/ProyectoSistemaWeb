<?php
$serverName = "DESKTOP-8FR8DOU"; 
$database = "ProyectooDB"; 
$username = "sa"; 
$password = "Artrexxz12"; 

try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
    exit();
}
?>
