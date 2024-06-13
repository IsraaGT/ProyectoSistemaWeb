<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Obtener lista de empresas
$stmtEmpresas = $conn->prepare("EXEC ObtenerEmpresas");
$stmtEmpresas->execute();
$empresas = $stmtEmpresas->fetchAll(PDO::FETCH_ASSOC);

$inventarios = [];
$empresaSeleccionada = null;

// Al seleccionar una empresa
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['empresa_id'])) {
    $empresaSeleccionada = $_POST['empresa_id'];

    // Obtener inventario por empresa
    $stmtInventario = $conn->prepare("EXEC ObtenerInventarioPorEmpresa :EmpresaID");
    $stmtInventario->bindParam(':EmpresaID', $empresaSeleccionada);
    $stmtInventario->execute();
    $inventarios = $stmtInventario->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Gestión de Inventario</h1>
        
        <form method="post">
            <div class="form-group">
                <label for="empresa">Empresa</label>
                <select class="form-control" id="empresa" name="empresa_id" onchange="this.form.submit()">
                    <option value="">Seleccione una empresa</option>
                    <?php foreach ($empresas as $empresa): ?>
                        <option value="<?php echo $empresa['EmpresaID']; ?>" <?php if ($empresaSeleccionada == $empresa['EmpresaID']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($empresa['Nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
        <div class="mt-4">
            <a href="panel.php" class="btn btn-info">Volver</a>
        </div>

        <?php if (!empty($inventarios)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto ID</th>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventarios as $inventario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inventario['ProductoID']); ?></td>
                            <td><?php echo htmlspecialchars($inventario['Nombre']); ?></td>
                            <td><?php echo htmlspecialchars($inventario['Cantidad']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
</body>
</html>
