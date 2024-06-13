<?php
session_start();
require_once 'config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Cargar empleados y roles de la base de datos
try {
    $stmtEmpleados = $conn->prepare("EXEC ObtenerEmpleados");
    $stmtEmpleados->execute();
    $empleados = $stmtEmpleados->fetchAll(PDO::FETCH_ASSOC);

    $stmtRoles = $conn->prepare("EXEC ObtenerRoles");
    $stmtRoles->execute();
    $roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al cargar datos: " . $e->getMessage();
}

// Procesar el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['usuario'], $_POST['contrasena'], $_POST['empleadoID'], $_POST['roleID'])) {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $empleadoID = $_POST['empleadoID'];
    $roleID = $_POST['roleID'];

    // Verificar si ya existe un usuario con el mismo EmpleadoID
    try {
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM Usuarios WHERE EmpleadoID = :empleadoID");
        $stmtCheck->bindParam(':empleadoID', $empleadoID, PDO::PARAM_INT);
        $stmtCheck->execute();
        $count = $stmtCheck->fetchColumn();

        if ($count > 0) {
            echo '<div class="alert alert-danger" role="alert">Este empleado ya tiene un usuario.</div>';
        } else {
            $stmtAgregarUsuario = $conn->prepare("EXEC AggUsuario @EmpleadoID = :empleadoID, @Usuario = :usuario, @Contrasena = :contrasena, @RoleID = :roleID");
            $stmtAgregarUsuario->bindParam(':empleadoID', $empleadoID, PDO::PARAM_INT);
            $stmtAgregarUsuario->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $stmtAgregarUsuario->bindParam(':contrasena', $contrasena, PDO::PARAM_STR);
            $stmtAgregarUsuario->bindParam(':roleID', $roleID, PDO::PARAM_INT);
            $stmtAgregarUsuario->execute();

            echo '<div class="alert alert-success" role="alert">Usuario agregado exitosamente!</div>';
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Error al agregar el usuario: ' . $e->getMessage() . '</div>';
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Agregar Nuevo Usuario</h2>
    <form method="post">
        <div class="form-group">
            <label for="empleadoID">Empleado:</label>
            <select class="form-control" id="empleadoID" name="empleadoID" required>
                <option value="">Seleccione un empleado</option>
                <?php foreach ($empleados as $empleado): ?>
                    <option value="<?php echo $empleado['EmpleadoID']; ?>"><?php echo htmlspecialchars($empleado['Nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="roleID">Rol:</label>
            <select class="form-control" id="roleID" name="roleID" required>
                <option value="">Seleccione un rol</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['RoleID']; ?>"><?php echo htmlspecialchars($role['RoleName']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="usuario">Nombre de Usuario:</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>
        <div class="form-group">
            <label for="contrasena">Contraseña:</label>
            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
        </div>
        <button type="submit" class="btn btn-primary">Agregar Usuario</button>
        <div class="mt-4">
            <a href="admin.php" class="btn btn-info">Volver</a>
        </div>
    </form>
</div>
</body>
</html>
