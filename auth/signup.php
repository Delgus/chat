<?php
require_once '../vendor/autoload.php';

use db\DB;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// read environments
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

// create logger
$log = new Logger('auth-signup');
$log->pushHandler(new StreamHandler(__DIR__ . '/logs/auth-signup.log', Logger::DEBUG));

header("Access-Control-Allow-Origin: *");

if (!$_POST) {
    http_response_code(400);
    return;
}

$post = filter_input_array(INPUT_POST, $_POST);

$errors = [];

// require field username
if (empty($post['username'])) {
    $errors['username'] = 'Username is required';
}

// require email
if (empty($post['email'])) {
    $errors['email'] = 'Email is required';
}

// require password
if (empty($post['password'])) {
    $errors['password'] = 'Password is required';
}

if (!empty($errors)) {
    echo json_encode(['result' => false, 'errors' => $errors]);
    return;
}

try {
    $db = new DB($_ENV["DB_DSN"], $_ENV["DB_USERNAME"], $_ENV["DB_PASSWORD"]);
    // unique username
    if ($db->getUserByUsername($post['username'])) {
        $errors['username'] = 'Not unique user!';
    }
    // unique email
    if ($db->getUserByEmail($post['email'])) {
        $errors['email'] = 'Not unique email!';
    }

    if (!empty($errors)) {
        echo json_encode(['result' => false, 'errors' => $errors]);
        return;
    }

    $password_hash = password_hash($post['password'], PASSWORD_DEFAULT);
    if (!$db->saveUser($post['username'], $post['email'], $password_hash)) {
        throw new DomainException("can not save user!");
    }

    echo json_encode(['result' => true]);
} catch (Throwable $e) {
    $log->error($e->getMessage());
    $errors['server'] = 'Internal error';
    echo json_encode(['result' => false, 'errors' => $errors]);
}
