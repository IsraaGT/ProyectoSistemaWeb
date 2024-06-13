<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </nav>

    <ul class="nav nav-tabs justify-content-center">
     
        <li class="nav-item">
            <a class="nav-link" href="ventas.php">Crear Ventas</a>
        </li>
       
        <li class="nav-item">
            <a class="nav-link" href="compras.php">Crear Compras</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="detalles_venta.php">Ver detalles de venta</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="detalles_compra.php">Ver detalles de compra</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="inventario.php">Inventario</a>
        </li>
            <a class="nav-link" href="informe_ventas.php">Reportes de Ventas</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="informe_compras.php">Reportes de Compras</a>
        </li>
        
    </ul>

    <div class="container mt-4">
        <h1 class="text-center">Bienvenido al Panel de Control</h1>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
