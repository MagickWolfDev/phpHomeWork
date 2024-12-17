<?php
$directory = __DIR__;
$allowedExtensions = ['jpg'];
$maxFileSize = 500 * 1024; // Максимальный размер файла 500 Кб

// Обработка загрузки файла
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload'])) {
    $file = $_FILES['upload'];
    $fileName = basename($file['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if ($file['size'] > $maxFileSize) {
        echo "Ошибка: файл слишком большой. Максимальный размер 500 Кб.";
    } elseif (!in_array($fileExt, $allowedExtensions)) {
        echo "Ошибка: разрешены только файлы с расширением .jpg.";
    } else {
        $targetFilePath = $directory . '/' . $fileName;
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            echo "Файл загружен успешно.";
        } else {
            echo "Ошибка при загрузке файла.";
        }
    }
}

// Обработка переименования файла
if (isset($_POST['rename'])) {
    $oldName = $_POST['oldName'];
    $newName = $_POST['newName'];

    if (!empty($newName) && pathinfo($oldName, PATHINFO_EXTENSION) === 'jpg') {
        rename($directory . '/' . $oldName, $directory . '/' . $newName . '.jpg');
    }
}

// Обработка удаления файла
if (isset($_POST['delete'])) {
    $fileToDelete = $_POST['fileToDelete'];
    unlink($directory . '/' . $fileToDelete);
}

// Получение списка файлов в директории
$files = array_diff(scandir($directory), ['.', '..']);
$images = array_filter($files, function($file) use ($allowedExtensions) {
    return is_file($file) && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $allowedExtensions);
});
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление изображениями</title>
</head>
<body>
    <h1>Структура содержимого папки</h1>

    <h2>Загрузить новое изображение</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="upload" required>
        <button type="submit">Загрузить</button>
    </form>

    <h2>Существующие изображения</h2>
    <table border="1">
        <tr>
            <th>Имя файла</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($images as $image): ?>
            <tr>
                <td><?php echo htmlspecialchars($image); ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="oldName" value="<?php echo htmlspecialchars($image); ?>">
                        <input type="text" name="newName" placeholder="Новое имя" required>
                        <button type="submit" name="rename">Переименовать</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="fileToDelete" value="<?php echo htmlspecialchars($image); ?>">
                        <button type="submit" name="delete" onclick="return confirm('Вы уверены, что хотите удалить этот файл?');">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
