<?php
//Inicializamós las variables
$email = $password = '';
$errores = [];

//Realizo un intento de conexión a la base de datos.

try {
    $pdo = new PDO('mysql:host=localhost;dbname=biblioteca', 'root', '1234');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    exit("Error de conexión: " . $error->getMessage());
} //Si la conexion no se puede realizar termina la ejecucion

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
}

    // Verificar si el email ya está registrado
    $consulta = $pdo->prepare("SELECT email FROM usuarios WHERE email = :email");
    $consulta->bindParam(':email', $email);
    $consulta->execute();
    if ($consulta->rowCount() > 0) {
        $errores[] = "El email ya está registrado.";
    }

    //Si no existen errores procedemos al registro
    if (empty($errores)) {
        //Encriptamos la contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT); // Encriptar la contraseña
       //Preparamos la consulta para insertar al nuevo usuario. 
        $consulta = $pdo->prepare("INSERT INTO usuarios (email, password) VALUES (:email, :password)");
        $consulta->bindParam(':email', $email);
        $consulta->bindParam(':password', $passwordHash);
        //Ejecutamos la consulta.
        
    if ($consulta->execute()) {
        // Redireccionar a la página principal con mensaje de éxito
        header('Location: principal.php?registro=exitoso');
        exit();
    } else {
        $errores[] = "Error al guardar en la base de datos.";
    }
    }

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-400 to-green-500 h-screen flex items-center justify-center">
    <div class="flex flex-col items-center justify-center h-full">
        <form action="registro.php" method="post" class="max-w-md w-full mx-auto bg-white p-6 rounded shadow">
        <!-- Formulario de registro -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" required value="<?php echo $email; ?>"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Contraseña:</label>
                <input type="password" id="password" name="password" required value="<?php echo $password; ?>"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex items-center justify-between mt-4">
            <input type="submit" value="Enviar" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            <a href="registro.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Borrar</a>
        </div>
    </form>

    <?php if (!empty($errores)): ?>
        <div class="mt-4 max-w-md w-full mx-auto bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            <strong class='font-bold'>Errores encontrados:</strong>
            <ul class='list-disc list-inside'>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    </div>
</body>
</html>