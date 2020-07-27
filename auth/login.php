<?php
require_once '../vendor/autoload.php';

use db\DB;
use Firebase\JWT\JWT;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// read environments
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

// create logger
$log = new Logger('auth-login');
$log->pushHandler(new StreamHandler(__DIR__ . '/logs/auth-login.log', Logger::DEBUG));

header("Access-Control-Allow-Origin: *");

if (!$_POST) {
    http_response_code(400);
    return;
}

$post = filter_input_array(INPUT_POST, $_POST);
try {
    $db = new DB($_ENV["DB_DSN"], $_ENV["DB_USERNAME"], $_ENV["DB_PASSWORD"]);
    $user = $db->getUserByUsername($post['username']);
} catch (Throwable $e) {
    $log->error($e->getMessage());
    echo json_encode(['result' => false, 'errors' => 'Internal error']);
    return;
}

// validate exist username and  correct password
if (!$user) {
    echo json_encode(['result' => false, 'errors' => 'Incorrect login or password']);
    return;
}
if (!password_verify($post['password'], $user['password_hash'])) {
    echo json_encode(['result' => false, 'errors' => 'Incorrect login or password']);
    return;
}

// create jwt
$token = [
    "iss" => $_ENV["HOST"], // issuer
    "iat" => time(),    // issued at
    "exp" => time() + intval($_ENV["TOKEN_LIVE"]),// expire time
    "sub" => [
        'id' => $user['id'],
        'username' => $user['username']
    ],
];
$jwt = JWT::encode($token, $_ENV["SECRET_KEY"]);
echo json_encode(['result' => true, 'jwt' => $jwt]);
