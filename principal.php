<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Biblioteca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-400 to-green-500 h-screen flex items-center justify-center">
    <div class="text-center text-white">
        <h1 class="text-6xl font-bold mb-6 italic">📚 Mi Biblioteca</h1>


        <?php 
        if (isset($_GET['registro']) && $_GET['registro'] == 'exitoso') {
            echo '<div class="mb-4 px-4 py-2 rounded bg-green-600">';
            echo '<p class="text-xl font-bold">Usuario creado con éxito. Puede iniciar sesión.</p>';
            echo '</div>';
        }
        ?>

        <?php 
        if (isset($_GET['sesion_cerrada']) && $_GET['sesion_cerrada'] == 'exitosa') {
            echo '<div class="mb-4 px-4 py-2 rounded bg-green-600">';
            echo '<p class="text-xl font-bold">Sesión cerrada correctamente. Hasta la próxima.</p>';
            echo '</div>';
        }
    ?>



        <div class="space-x-4">
            <a href="login.php" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded transition duration-300 ease-in-out">Iniciar Sesión</a>
            <a href="registro.php" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded transition duration-300 ease-in-out">Registrarse</a>
        </div>
    </div>
</body>
</html>
