<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre'], $_POST['direccion'])) {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];

    try {
        $stmt = $conn->prepare("EXEC AggEmpresa @Nombre = :nombre, @Direccion = :direccion");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->execute();

        echo '<div class="alert alert-success" role="alert">Empresa agregada exitosamente!</div>';
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Error al agregar la empresa: ' . $e->getMessage() . '</div>';
    }
}
?>
