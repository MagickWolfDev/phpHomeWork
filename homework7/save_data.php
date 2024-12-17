<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $name = $data['name'];
    $email = $data['email'];
    $age = $data['age'];
    $specialty = $data['specialty'];
    $experience = $data['experience'];

    $line = "Имя: $name, Email: $email, Возраст: $age, Специальность: $specialty, Стаж: $experience\n";

    file_put_contents('data.txt', $line, FILE_APPEND | LOCK_EX);

    echo "Данные успешно сохранены!";
} else {
    echo "Неверный запрос.";
}
?>
