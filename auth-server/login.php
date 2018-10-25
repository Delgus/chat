<?php
require_once '../vendor/autoload.php';
require_once '../config/config.php';

if ($_POST) {
    $post = filter_input_array(INPUT_POST, $_POST);
    $db = new \db\Db(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $user = $db->getUserByUsername($post['username']);
    if ($user) {
        //validate password
        $valid = password_verify($post['password'], $user['password_hash']);

        //create jwt
        if ($valid) {
            $token = [
                "iss" => HOST_NAME, //issuer
                "aud" => WEB_SOCKET,//audience
                "iat" => time(),    //issued at
                "exp" => time() + TOKEN_LIVE,//expire time
                //subject
                "sub" => [
                    'id' => $user['id'],
                    'username' => $user['username']
                ],
            ];
            $jwt = \Firebase\JWT\JWT::encode($token, SECRET_KEY);
            echo $jwt;
            exit;
        }
    }
}