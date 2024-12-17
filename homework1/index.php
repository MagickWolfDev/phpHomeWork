<?php
$date1 = '';
$date2 = '';
$daysDifference = '';
$minutesDifference = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date1 = $_POST['date1'];
    $date2 = $_POST['date2'];

    $startDate = new DateTime($date1);
    $endDate = new DateTime($date2);

    // Вычисление разницы между датами
    $interval = $startDate->diff($endDate);
    $daysDifference = $interval->days;
    $minutesDifference = $daysDifference * 24 * 60; // Переводим дни в минуты

    $line = "Дата 1: $date1, Дата 2: $date2\n";
    file_put_contents('dates.txt', $line, FILE_APPEND | LOCK_EX);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Разница между датами</title>
</head>
<body>
    <h1>Введите две даты</h1>
    <form method="post">
        <label for="date1">Дата 1:</label>
        <input type="date" id="date1" name="date1" value="<?php echo htmlspecialchars($date1); ?>" required>
        <br><br>

        <label for="date2">Дата 2:</label>
        <input type="date" id="date2" name="date2" value="<?php echo htmlspecialchars($date2); ?>" required>
        <br><br>

        <button type="submit">Подсчитать разницу</button>
    </form>

    <?php if ($daysDifference !== ''): ?>
        <h2>Результаты:</h2>
        <p>Количество дней между датами: <?php echo $daysDifference; ?></p>
        <p>Количество минут между датами: <?php echo $minutesDifference; ?></p>
    <?php endif; ?>

</body>
</html>
