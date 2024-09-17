<?php

    $messages = [];

    //Подгружаем данные из файла
    $json = file_get_contents(__DIR__ . '/data.json');

    if(empty($json))
    {
        $json = '[]';
    }

    $messages = json_decode($json, true);

    //При успешно переданных данных, пишем их в файл
    if($_POST["message"] & $_POST["email"])
    {
        $date = time();
        array_push($messages, [
            "email" => $_POST["email"],
            "message" => $_POST["message"],
            "date" => $date,
        ]);

        $json = json_encode($messages, JSON_UNESCAPED_UNICODE);
        file_put_contents(__DIR__ . '/data.json', $json);

        header('Location: ' . $_SERVER['PHP_SELF']); //Блокировка повторной отправки формы
    };

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="index.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <title>Гоcтевая книга</title>
</head>

<body class="color-pr">
    <div class="wrapper">
        <h1 class="header">Гостевая книга</h1>
        <form action method="post" class="form color-sc">
            <div class="form-input">
                <label for="email">Email:</label>
                <input class="color-pr" type="email" id="email" name="email" required>
            </div>
            <div class="form-input">
                <label for="message">Сообщение:</label>
                <textarea class="color-pr" id="message" name="message"></textarea>
            </div>
            <div class="form-input">
                <input type="submit" class="button color-btn-pr">
            </div>
        </form>
        <div class="filters">
            <select class="color-sc select" id="select-date">
                <option value="new">Сначала новые</option>
                <option value="old">Сначала старые</option>
            </select>
        </div>
        <div class="messages">
            <?php
                foreach ($messages as $key => $value) {

                    $email = $value["email"];
                    $data = date("d.m.y h:m:s",  intval($value["date"]));
                    $message = str_replace(array("\r\n", "\r", "\n"), '<br>', $value["message"]);
                    
                    echo "
                    <div class=\"message-contaner color-sc\">
                        <div class=\"message-heder\">
                            <label>$email</label>
                            <label>$data</label>
                        </div>
                        <div class=\"message-body\">
                            $message
                        </div>
                    </div>
                    ";
                }
            ?>
        </div>
    </div>
</body>

<script>
    let select = document.querySelector("#select-date");
    let messageContaner = document.querySelector(".messages");
    
    select.addEventListener('change', (e) => {
        let selected = e.target.value;
        if(selected == "old")
        {
            messageContaner.style.setProperty('flex-direction', 'column');
        }
        if(selected == "new")
        {
            messageContaner.style.setProperty('flex-direction', 'column-reverse');
        }
    })
</script>
</html>
