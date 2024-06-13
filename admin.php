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
    <!-- Barra de navegación principal -->
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

    <!-- Navegación superior -->
    <ul class="nav nav-tabs justify-content-center">
     
        <li class="nav-item">
            <a class="nav-link" href="empresas.html">Empresas</a>
        </li>
       
        <li class="nav-item">
            <a class="nav-link" href="proveedores.html">Proveedores</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="clientes.html">Clientes</a>
        </li>
            <a class="nav-link" href="usuarios.php">Usuarios</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="empleados.php">Empleados</a>
        </li>
    </ul>

    <div class="container mt-4">
        <h1 class="text-center">Bienvenido Admin</h1>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
