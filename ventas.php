<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Obtener empresas
$stmtEmpresas = $conn->prepare("EXEC ObtenerEmpresas");
$stmtEmpresas->execute();
$empresas = $stmtEmpresas->fetchAll(PDO::FETCH_ASSOC);

// Obtener clientes
$stmtClientes = $conn->prepare("EXEC ObtenerClientes");
$stmtClientes->execute();
$clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

$productos = [];
$empresaID = null;
$clienteID = null;
$detallesVenta = [];
$ordenVentaID = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['empresa'])) {
    $empresaID = $_POST['empresa'];
    
    // Obtener inventario por empresa
    $stmtProductos = $conn->prepare("EXEC ObtenerInventarioPorEmpresa :EmpresaID");
    $stmtProductos->bindParam(':EmpresaID', $empresaID);
    $stmtProductos->execute();
    $productos = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cliente']) && isset($_POST['crear_venta'])) {
    $empresaID = $_POST['empresa'];
    $clienteID = $_POST['cliente'];
    $fecha = $_POST['fecha']; // Obtener la fecha del formulario
    
    $fecha = date('Y-m-d H:i:s', strtotime($fecha));
    
    $status = 'Pendiente';
    
    try {
        // Crear la venta
        $stmtCrearVenta = $conn->prepare("DECLARE @OrdenVentaID INT; EXEC CrearVenta :EmpresaID, :EmpleadoID, :ClienteID, :Fecha, :Status, @OrdenVentaID OUTPUT; SELECT @OrdenVentaID AS OrdenVentaID;");
        $stmtCrearVenta->bindParam(':EmpresaID', $empresaID);
        $stmtCrearVenta->bindParam(':EmpleadoID', $empleadoID);
        $stmtCrearVenta->bindParam(':ClienteID', $clienteID);
        $stmtCrearVenta->bindParam(':Fecha', $fecha); 
        $stmtCrearVenta->bindParam(':Status', $status);
        $stmtCrearVenta->execute();
        
        // Avanzar al siguiente conjunto de resultados para obtener el valor de salida
        $stmtCrearVenta->nextRowset();
        $result = $stmtCrearVenta->fetch(PDO::FETCH_ASSOC);
        $ordenVentaID = $result['OrdenVentaID'];
        
        // Verificar si se seleccionaron productos
        $productosSeleccionados = false;
        if (isset($_POST['productos']) && is_array($_POST['productos']) && count($_POST['productos']) > 0) {
            foreach ($_POST['productos'] as $productoID => $detalles) {
                $cantidad = intval($detalles['cantidad']); 
                $precio = floatval($detalles['precio']);
                if ($cantidad > 0 && $precio > 0) {
                    // Agregar detalle de venta
                    $stmtAgregarDetalle = $conn->prepare("EXEC AgregarDetalleVenta :OrdenVentaID, :ProductoID, :EmpresaID, :Cantidad, :Precio");
                    $stmtAgregarDetalle->bindParam(':OrdenVentaID', $ordenVentaID);
                    $stmtAgregarDetalle->bindParam(':ProductoID', $productoID);
                    $stmtAgregarDetalle->bindParam(':EmpresaID', $empresaID);
                    $stmtAgregarDetalle->bindParam(':Cantidad', $cantidad);
                    $stmtAgregarDetalle->bindParam(':Precio', $precio);
                    $stmtAgregarDetalle->execute();
                    
                    $productosSeleccionados = true;
                }
            }
        }
        
        if ($productosSeleccionados) {
            // Obtener detalles de la orden de venta recién creada
            $stmtDetallesVenta = $conn->prepare("EXEC ObtenerDetallesVentaPorOrden :OrdenVentaID");
            $stmtDetallesVenta->bindParam(':OrdenVentaID', $ordenVentaID, PDO::PARAM_INT);
            $stmtDetallesVenta->execute();
            $detallesVenta = $stmtDetallesVenta->fetchAll(PDO::FETCH_ASSOC);

            $mensaje = "Venta creada exitosamente.";
        } else {
            throw new Exception("Debe seleccionar al menos un producto con una cantidad y precio válidos.");
        }
    } catch (Exception $e) {
        $error = "Error al crear la venta: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ventas</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Gestión de Ventas</h1>
        
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="empresa">Empresa</label>
                <select class="form-control" id="empresa" name="empresa" onchange="this.form.submit()" required>
                    <option value="">Seleccione una empresa</option>
                    <?php foreach ($empresas as $empresa): ?>
                        <option value="<?php echo $empresa['EmpresaID']; ?>" <?php if (isset($empresaID) && $empresaID == $empresa['EmpresaID']) echo 'selected'; ?>><?php echo $empresa['Nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="cliente">Cliente</label>
                <select class="form-control" id="cliente" name="cliente" required>
                    <option value="">Seleccione un cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?php echo $cliente['ClienteID']; ?>"><?php echo $cliente['Nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha">Fecha</label>
                <input type="datetime-local" class="form-control" id="fecha" name="fecha" required>
            </div>
            
            <?php if (isset($empresaID)): ?>
                <div id="productos-container">
                    <h4>Productos</h4>
                    <?php foreach ($productos as $producto): ?>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"><?php echo $producto['Nombre']; ?></label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="productos[<?php echo $producto['ProductoID']; ?>][cantidad]" placeholder="Cantidad" min="0">
                            </div>
                            <div class="col-sm-3">
                                <input type="number" step="0.01" class="form-control" name="productos[<?php echo $producto['ProductoID']; ?>][precio]" placeholder="Precio" min="0">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" name="crear_venta">Crear Venta</button>
            <?php endif; ?>
        </form>

        <?php if (!empty($detallesVenta)): ?>
            <div class="mt-4">
                <h2>Detalle de Venta #<?php echo htmlspecialchars($ordenVentaID); ?></h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Producto ID</th>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Precio Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detallesVenta as $detalle): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($detalle['ProductoID']); ?></td>
                                <td><?php echo htmlspecialchars($detalle['Nombre']); ?></td>
                                <td><?php echo htmlspecialchars($detalle['Cantidad']); ?></td>
                                <td><?php echo htmlspecialchars($detalle['PrecioTotal']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="panel.php" class="btn btn-info">Volver al panel.</a>
        </div>
    </div>
</body>
</html>
