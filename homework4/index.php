<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>База данных</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
            color: #333;
        }
        h2 {
            color: #004080;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            margin: 5px 0;
        }
        ul li a {
            text-decoration: none;
            color: #004080;
            font-weight: bold;
        }
        ul li a:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #004080;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        button {
            background-color: #004080;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0066cc;
        }
        form {
            margin: 10px 0;
        }
        input[type="text"] {
            padding: 5px;
            width: 100%;
            box-sizing: border-box;
        }
        label {
            font-weight: bold;
        }
    </style>
</head>
<body><?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
$host = 'localhost';
$user = 'root';
$password = 'root';
$dbname = 'homework4';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

if (!isset($_GET['table'])) {
    $tables = $conn->query("SHOW TABLES");
    echo "<ul>";
    while ($row = $tables->fetch_array()) {
        $table = $row[0];
        $columns = $conn->query("SHOW COLUMNS FROM $table");
        $column_count = $columns->num_rows;
        echo "<li><a href='?table=$table'>$table ($column_count)</a></li>";
    }
    echo "</ul>";
} else {
    $table = $_GET['table'];
    $columns = $conn->query("SHOW COLUMNS FROM $table");
    echo "<h2>Структура таблицы $table</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Поле</th><th>Тип</th></tr>";
    while ($column = $columns->fetch_assoc()) {
        echo "<tr><td>{$column['Field']}</td><td>{$column['Type']}</td></tr>";
    }
    echo "</table>";

    $data = $conn->query("SELECT * FROM $table");
    echo "<h2>Данные таблицы $table</h2>";
    echo "<table border='1'>";
    echo "<tr>";
    $fields = $conn->query("SHOW COLUMNS FROM $table");
    $columns = [];
    while ($field = $fields->fetch_assoc()) {
        $columns[] = $field['Field'];
        echo "<th>{$field['Field']}</th>";
    }
    echo "<th>Действия</th></tr>";
    while ($row = $data->fetch_assoc()) {
        echo "<tr>";
        foreach ($columns as $column) {
            echo "<td>{$row[$column]}</td>";
        }
        echo "<td>
                <form method='post' action=''>
                    <input type='hidden' name='table' value='$table'>
                    <input type='hidden' name='id' value='{$row['id']}'>
                    <button type='submit' name='edit'>Редактировать</button>
                </form>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit'])) {
        $table = $_POST['table'];
        $id = $_POST['id'];
        $data = $conn->query("SELECT * FROM $table WHERE id = $id");
        $row = $data->fetch_assoc();
        echo "<form method='post' action=''>";
        foreach ($row as $column => $value) {
            if ($column !== 'id') {
                echo "<label>$column: <input type='text' name='$column' value='$value'></label><br>";
            }
        }
        echo "<input type='hidden' name='table' value='$table'>";
        echo "<input type='hidden' name='id' value='$id'>";
        echo "<button type='submit' name='save'>Сохранить</button>";
        echo "</form>";
    } elseif (isset($_POST['save'])) {
        $table = $_POST['table'];
        $id = $_POST['id'];
        $updates = [];
        foreach ($_POST as $column => $value) {
            if ($column !== 'table' && $column !== 'id' && $column !== 'save') {
                $updates[] = "$column = '" . $conn->real_escape_string($value) . "'";
            }
        }
        $query = "UPDATE $table SET " . implode(', ', $updates) . " WHERE id = $id";
        $conn->query($query);
        header("Location: ?table=$table");
    }
}

$conn->close();
?>
</body>
</html>
