<?php
session_start();
require 'conexion.php';

if (isset($_GET['cerrar_sesion']) && $_GET['cerrar_sesion'] == '1') {
    session_unset();
    session_destroy();
    header('Location: principal.php?sesion_cerrada=exitosa');
    exit();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuarioId = $_SESSION['usuario_id']; // Obtén el usuario_id desde la sesión
$usuarioEmail = $_SESSION['usuario_email'] ?? 'No identificado';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
    $autor = $_POST['autor'];
    $genero = $_POST['genero'];
    $disponible = $_POST['disponible'];

    $stmt = $pdo->prepare("INSERT INTO libros (nombre, autor, genero, disponible, usuario_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $autor, $genero, $disponible, $usuarioId]);

    header("Location: biblioteca.php");
    exit();
}

if (isset($_GET['eliminar_libro'])) {
    $id_libro = $_GET['eliminar_libro'];
    $stmt = $pdo->prepare("DELETE FROM libros WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id_libro, $usuarioId]);

    header("Location: biblioteca.php");
    exit();
}

// Cargar datos del libro para editar
$libroParaEditar = null;
if (isset($_GET['editar_libro'])) {
    $id_libro = $_GET['editar_libro'];
    $stmt = $pdo->prepare("SELECT * FROM libros WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id_libro, $usuarioId]);
    $libroParaEditar = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_libro_editar'])) {
    $id = $_POST['id_libro_editar'];
    $nombre = $_POST['nombre_editar'];
    $autor = $_POST['autor_editar'];
    $genero = $_POST['genero_editar'];
    $disponible = $_POST['disponible_editar'];

    $stmt = $pdo->prepare("UPDATE libros SET nombre = ?, autor = ?, genero = ?, disponible = ? WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$nombre, $autor, $genero, $disponible, $id, $usuarioId]);

    header("Location: biblioteca.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Biblioteca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-400 to-green-500 h-screen">
    <div class="absolute top-0 right-0 p-4">
        <p class="text-black font-bold italic text-sm">
            <?php echo htmlspecialchars($usuarioEmail); ?>
            <span class="text-green-500">Conectado</span>
        </p>
        <a href="biblioteca.php?cerrar_sesion=1" class="text-red-600 hover:text-red-800 ml-4">Cerrar sesión</a>
    </div>

    <div class="flex flex-col items-center justify-center h-full">
        <div class="text-center mb-8">
            <h1 class="text-6xl font-bold mb-6 italic text-white">📚 Tu Biblioteca Personal</h1>
            
            <form action="biblioteca.php" method="post" class="max-w-md w-full mx-auto bg-white p-6 rounded shadow">
                <div class="mb-4">
                    <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre del libro:</label>
                    <input type="text" id="nombre" name="nombre" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-4">
                    <label for="autor" class="block text-gray-700 text-sm font-bold mb-2">Autor:</label>
                    <input type="text" id="autor" name="autor" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-4">
                    <label for="genero" class="block text-gray-700 text-sm font-bold mb-2">Género:</label>
                    <input type="text" id="genero" name="genero" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-4">
                    <label for="disponible" class="block text-gray-700 text-sm font-bold mb-2">Disponible:</label>
                    <select id="disponible" name="disponible" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="flex justify-between">
                    <input type="submit" value="Añadir Libro" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                </div>
            </form>
        </div>

        <!-- Mostrar libros -->
        <div class="w-full px-8">
            <?php
            $consulta = $pdo->prepare("SELECT * FROM libros WHERE usuario_id = ? ORDER BY autor, nombre");
            $consulta->execute([$usuarioId]);

            while ($libro = $consulta->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='max-w-md w-full mx-auto bg-white p-4 rounded shadow mb-4'>";
                echo "<p class='text-gray-700 text-lg'>{$libro['autor']} - {$libro['nombre']} - {$libro['genero']}</p>";
                echo "<p class='text-gray-600'>Disponible: " . ($libro['disponible'] ? 'Sí' : 'No') . "</p>";
                echo "<div class='flex justify-between mt-4'>";
                echo "<a href='biblioteca.php?editar_libro={$libro['id']}' class='text-blue-600 hover:text-blue-800'>Editar</a>";
                echo "<a href='biblioteca.php?eliminar_libro={$libro['id']}' class='text-red-600 hover:text-red-800'>Eliminar</a>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>

        <?php if ($libroParaEditar) { ?>
            <div class="max-w-md w-full mx-auto bg-white p-6 rounded shadow mt-4">
                <h2 class="text-2xl font-semibold mb-4">Editar Libro</h2>
                <form action="biblioteca.php" method="post">
                    <input type="hidden" name="id_libro_editar" value="<?php echo $libroParaEditar['id']; ?>">
                    <div class="mb-4">
                        <label for="nombre_editar" class="block text-gray-700 text-sm font-bold mb-2">Nombre del libro:</label>
                        <input type="text" id="nombre_editar" name="nombre_editar" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" value="<?php echo $libroParaEditar['nombre']; ?>">
                    </div>
                    <div class="mb-4">
                        <label for="autor_editar" class="block text-gray-700 text-sm font-bold mb-2">Autor:</label>
                        <input type="text" id="autor_editar" name="autor_editar" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" value="<?php echo $libroParaEditar['autor']; ?>">
                    </div>
                    <div class="mb-4">
                        <label for="genero_editar" class="block text-gray-700 text-sm font-bold mb-2">Género:</label>
                        <input type="text" id="genero_editar" name="genero_editar" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" value="<?php echo $libroParaEditar['genero']; ?>">
                    </div>
                    <div class="mb-4">
                        <label for="disponible_editar" class="block text-gray-700 text-sm font-bold mb-2">Disponible:</label>
                        <select id="disponible_editar" name="disponible_editar" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            <option value="1" <?php if ($libroParaEditar['disponible'] == 1) echo "selected"; ?>>Sí</option>
                            <option value="0" <?php if ($libroParaEditar['disponible'] == 0) echo "selected"; ?>>No</option>
                        </select>
                    </div>
                    <div class="flex justify-between">
                        <input type="submit" value="Guardar Cambios" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    </div>
                </form>
            </div>
        <?php } ?>

    </div>
</body>
</html>
