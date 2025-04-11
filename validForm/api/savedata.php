<?php

    function sanitizeInput($input) {

        $disallow = ['~', '\'', '"', '<', '>', '.', '%'];
        $input = str_replace($disallow, '', $input);

        // Экранируем специальные HTML символы
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

        return $input;
    }

    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $json = file_get_contents("php://input");
        
        $data = json_decode($json, true);

        $name = sanitizeInput($data["Name"]);
        $email = $data["Email"];
        $message = htmlspecialchars($data["Message"], ENT_QUOTES, 'UTF-8');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            
            echo json_encode([
                "status" => "Ok",
                "message" => "Неверный email"
            ], JSON_UNESCAPED_UNICODE);
            http_response_code(400);
            return;
        }

        $outStr = json_encode([
            "Name" => $name,
            "Email" => $email,
            "Message" => $message
        ], JSON_UNESCAPED_UNICODE) . "\n";

        $filename = __DIR__ . '/file.txt';
 
        $fh = fopen($filename, 'a+');
        fwrite($fh, $outStr);
        fclose($fh);

        echo json_encode([
            "status" => "Ok"
        ], JSON_UNESCAPED_UNICODE);
    }
?>
