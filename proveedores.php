<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre'], $_POST['direccion'], $_POST['telefono'])) {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];

    try {
        $stmt = $conn->prepare("EXEC AggProveedores @Nombre = :nombre, @Direccion = :direccion, @Telefono = :telefono");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->execute();

        echo '<div class="alert alert-success" role="alert">Proveedor agregado exitosamente!</div>';
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Error al agregar el proveedor: ' . $e->getMessage() . '</div>';
    }
}
?>
