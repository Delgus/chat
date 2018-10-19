<?php
require_once '../vendor/autoload.php';
require_once '../config/config.php';

if ($_POST) {
    $post = filter_input_array(INPUT_POST, $_POST);
    $db = new \db\Db(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $user = $db->getUserByUsername($post['username']);
    if ($user) {
        //проверка пароля
        $valid = password_verify($post['password'], $user['password_hash']);
        //Если все хорошо создаем JWT токен
        if ($valid) {
            $token = [
                "iss" => HOST_NAME, //(issuer) издатель токена
                "aud" => WEB_SOCKET,//(audience) аудитория, получатели токена
                "iat" => time(),    //(issued at) время создания токена
                "exp" => time() + TOKEN_LIVE,//(expire time) срок действия токена
                //(subject) "тема", назначение токена
                "sub" => [
                    'id' => $user['id'],
                    'username' => $user['username']
                ],
            ];
            $jwt = \Firebase\JWT\JWT::encode($token, SECRET_KEY);
            echo $jwt;
        } else {
            echo "false";
        }
    } else {
        echo "false";
    }
}
//silence is gold
die();