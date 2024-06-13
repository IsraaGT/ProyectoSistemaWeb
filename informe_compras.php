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

$compras = [];
$empresaSeleccionada = null;
$anioSeleccionado = date('Y'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $empresaSeleccionada = $_POST['empresa_id'] ?? null;  
    $anioSeleccionado = $_POST['anio'] ?? date('Y');  
    if ($empresaSeleccionada) {
        // Obtener reporte de compras por empresa y año
        $stmtCompras = $conn->prepare("EXEC ReporteCompras :EmpresaID, :Anio");
        $stmtCompras->bindParam(':EmpresaID', $empresaSeleccionada, PDO::PARAM_INT);
        $stmtCompras->bindParam(':Anio', $anioSeleccionado, PDO::PARAM_INT);
        $stmtCompras->execute();
        $compras = $stmtCompras->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Compras</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Informe de Compras</h1>
        
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
            <div class="form-group">
                <label for="anio">Año</label>
                <select class="form-control" id="anio" name="anio" onchange="this.form.submit()">
                    <?php for ($year = date('Y'); $year >= 2000; $year--): ?>
                        <option value="<?php echo $year; ?>" <?php if ($anioSeleccionado == $year) echo 'selected'; ?>>
                            <?php echo $year; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </form>
        <div class="mt-4">
        <a href="panel.php" class="btn btn-info">Volver</a>
    </div>
        <?php if (!empty($compras)): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Proveedor</th>
                <th>Enero</th>
                <th>Febrero</th>
                <th>Marzo</th>
                <th>Abril</th>
                <th>Mayo</th>
                <th>Junio</th>
                <th>Julio</th>
                <th>Agosto</th>
                <th>Septiembre</th>
                <th>Octubre</th>
                <th>Noviembre</th>
                <th>Diciembre</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($compras as $compra): ?>
                <tr>
                    <td><?php echo htmlspecialchars($compra['ProveedorNombre']); ?></td>
                    <td><?php echo htmlspecialchars($compra['Enero'] ?? 0); ?></td>
                    <td><?php echo htmlspecialchars($compra['Febrero'] ?? 0); ?></td>
                    <td><?php echo htmlspecialchars($compra['Marzo'] ?? 0); ?></td>
                    <td><?php echo htmlspecialchars($compra['Abril'] ?? 0); ?></td>
                    <td><?php echo htmlspecialchars($compra['Mayo'] ?? 0); ?></td>
                    <td><?php echo htmlspecialchars($compra['Junio'] ?? 0); ?></td>
                    <td><?php echo htmlspecialchars($compra['Julio'] ?? 0); ?></td>
                    <td><?php echo htmlspecialchars($compra['Agosto'] ?? 0); ?></td>
                    <td><?php echo htmlspecialchars($compra['Septiembre'] ?? 0); ?></td>
                    <td><?php echo htmlspecialchars($compra['Octubre'] ?? 0); ?></td>
                    <td><?php echo htmlspecialchars($compra['Noviembre'] ?? 0); ?></td>
                    <td><?php echo htmlspecialchars($compra['Diciembre'] ?? 0); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
   
<?php endif; ?>

    </div>
</body>
</html>
