<?php
session_start();
require_once 'config.php';

// Cargar lista de empresas para el selector
try {
    $stmtEmpresas = $conn->prepare("EXEC ObtenerEmpresas");
    $stmtEmpresas->execute();
    $empresas = $stmtEmpresas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorLoad = "Error al cargar empresas: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre'], $_POST['empresa'], $_POST['correo'])) {
    $nombre = $_POST['nombre'];
    $empresaID = $_POST['empresa'];
    $correo = $_POST['correo'];

    try {
        $stmt = $conn->prepare("EXEC AggEmpleado @Nombre = :nombre, @EmpresaID = :empresaID, @Correo = :correo");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':empresaID', $empresaID);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        echo '<div class="alert alert-success" role="alert">Empleado agregado exitosamente!</div>';
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Error al agregar el empleado: ' . $e->getMessage() . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Empleado</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Agregar Nuevo Empleado</h2>
    <form action="empleados.php" method="post">
        <div class="form-group">
            <label for="nombre">Nombre del Empleado:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="empresa">Empresa:</label>
            <select class="form-control" id="empresa" name="empresa" required>
                <?php foreach ($empresas as $empresa): ?>
                    <option value="<?php echo $empresa['EmpresaID']; ?>"><?php echo $empresa['Nombre']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="correo">Correo Electrónico:</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
        </div>
        <button type="submit" class="btn btn-primary">Agregar Empleado</button>
    </form>
    <div class="mt-4">
            <a href="admin.php" class="btn btn-info">Volver</a>
        </div>
</div>
</body>
</html>
