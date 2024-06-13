<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$stmtEmpresas = $conn->prepare("EXEC ObtenerEmpresas");
$stmtEmpresas->execute();
$empresas = $stmtEmpresas->fetchAll(PDO::FETCH_ASSOC);

$stmtProveedores = $conn->prepare("EXEC ObtenerProveedores");
$stmtProveedores->execute();
$proveedores = $stmtProveedores->fetchAll(PDO::FETCH_ASSOC);

$productos = [];
$proveedorID = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proveedor'])) {
    $proveedorID = $_POST['proveedor'];
    
    $stmtProductos = $conn->prepare("EXEC ObtenerProductosPorProveedor :ProveedorID");
    $stmtProductos->bindParam(':ProveedorID', $proveedorID);
    $stmtProductos->execute();
    $productos = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['empresa']) && isset($_POST['crear_compra'])) {
    $empresaID = $_POST['empresa'];
    $proveedorID = $_POST['proveedor'];
    $fecha = $_POST['fecha']; 
    
    $fecha = date('Y-m-d H:i:s', strtotime($fecha));
    
    $status = 'Pendiente';
    
    try {
        $stmtCrearCompra = $conn->prepare("DECLARE @OrdenCompraID INT; EXEC CrearCompra :EmpresaID, :ProveedorID, :Fecha, :Status, @OrdenCompraID OUTPUT; SELECT @OrdenCompraID AS OrdenCompraID;");
        $stmtCrearCompra->bindParam(':EmpresaID', $empresaID);
        $stmtCrearCompra->bindParam(':ProveedorID', $proveedorID);
        $stmtCrearCompra->bindParam(':Fecha', $fecha);
        $stmtCrearCompra->bindParam(':Status', $status);
        $stmtCrearCompra->execute();

        $stmtCrearCompra->nextRowset();
        $result = $stmtCrearCompra->fetch(PDO::FETCH_ASSOC);
        $ordenCompraID = $result['OrdenCompraID'];
        
        $productosSeleccionados = false;
        $detallesCompra = [];

        if (isset($_POST['productos']) && is_array($_POST['productos']) && count($_POST['productos']) > 0) {
            foreach ($_POST['productos'] as $productoID => $cantidad) {
                $cantidad = intval($cantidad); 
                if ($cantidad > 0) {
                    $stmtProducto = $conn->prepare("SELECT Precio, Nombre FROM Productos WHERE ProductoID = :ProductoID");
                    $stmtProducto->bindParam(':ProductoID', $productoID);
                    $stmtProducto->execute();
                    $producto = $stmtProducto->fetch(PDO::FETCH_ASSOC);
                    $precioUnitario = $producto['Precio'];
                    $nombreProducto = $producto['Nombre'];

                    $stmtAgregarDetalle = $conn->prepare("EXEC AgregarDetalleCompra :OrdenCompraID, :ProductoID, :EmpresaID, :Cantidad, :PrecioUnitario");
                    $stmtAgregarDetalle->bindParam(':OrdenCompraID', $ordenCompraID);
                    $stmtAgregarDetalle->bindParam(':ProductoID', $productoID);
                    $stmtAgregarDetalle->bindParam(':EmpresaID', $empresaID);
                    $stmtAgregarDetalle->bindParam(':Cantidad', $cantidad);
                    $stmtAgregarDetalle->bindParam(':PrecioUnitario', $precioUnitario);
                    $stmtAgregarDetalle->execute();
                    
                    $stmtActualizarInventario = $conn->prepare("EXEC ActualizarInventario :ProductoID, :EmpresaID, :CantidadModificada");
                    $stmtActualizarInventario->bindParam(':ProductoID', $productoID);
                    $stmtActualizarInventario->bindParam(':EmpresaID', $empresaID);
                    $stmtActualizarInventario->bindParam(':CantidadModificada', $cantidad);
                    $stmtActualizarInventario->execute();

                    $productosSeleccionados = true;
                    $detallesCompra[] = [
                        'ProductoID' => $productoID,
                        'Nombre' => $nombreProducto,
                        'Cantidad' => $cantidad,
                        'PrecioTotal' => $cantidad * $precioUnitario
                    ];
                }
            }
        }
        
        if ($productosSeleccionados) {
            $mensaje = "Compra creada exitosamente.";
        } else {
            throw new Exception("Debe seleccionar al menos un producto.");
        }
    } catch (Exception $e) {
        $error = "Error al crear la compra: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Compras</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Gestión de Compras</h1>
        
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="empresa">Empresa</label>
                <select class="form-control" id="empresa" name="empresa" required>
                    <option value="">Seleccione una empresa</option>
                    <?php foreach ($empresas as $empresa): ?>
                        <option value="<?php echo $empresa['EmpresaID']; ?>"><?php echo $empresa['Nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="proveedor">Proveedor</label>
                <select class="form-control" id="proveedor" name="proveedor" onchange="this.form.submit()" required>
                    <option value="">Seleccione un proveedor</option>
                    <?php foreach ($proveedores as $proveedor): ?>
                        <option value="<?php echo $proveedor['ProveedorID']; ?>" <?php if (isset($proveedorID) && $proveedorID == $proveedor['ProveedorID']) echo 'selected'; ?>><?php echo $proveedor['Nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="fecha">Fecha</label>
                <input type="datetime-local" class="form-control" id="fecha" name="fecha" required>
            </div>
            
            <?php if (isset($proveedorID)): ?>
                <div id="productos-container">
                    <h4>Productos</h4>
                    <?php foreach ($productos as $producto): ?>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"><?php echo $producto['Nombre']; ?></label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" name="productos[<?php echo $producto['ProductoID']; ?>]" placeholder="Cantidad" min="0">
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" value="<?php echo $producto['Precio']; ?>" readonly>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" name="crear_compra">Crear Compra</button>
            <?php endif; ?>
        </form>

        <?php if (isset($detallesCompra) && !empty($detallesCompra)): ?>
            <div class="mt-4">
                <h2>Detalles de Compra</h2>
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
                        <?php foreach ($detallesCompra as $detalle): ?>
                            <tr>
                                <td><?php echo $detalle['ProductoID']; ?></td>
                                <td><?php echo $detalle['Nombre']; ?></td>
                                <td><?php echo $detalle['Cantidad']; ?></td>
                                <td><?php echo $detalle['PrecioTotal']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="panel.php" class="btn btn-info">Volver al panel</a>
        </div>
    </div>
</body>
</html>
