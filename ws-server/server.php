<?php
require_once '../vendor/autoload.php';
require_once '../config/config.php';

use Firebase\JWT\JWT;
use Workerman\Worker;
use db\Db;

$ws_worker = new Worker('websocket://' . WEB_SOCKET);
$db = new Db(DB_DSN, DB_USERNAME, DB_PASSWORD);

// storage of user-connection link
$users = [];

$ws_worker->onConnect = function ($connection) use (&$users, $db) {
    $connection->onWebSocketConnect = function ($connection) use (&$users, $db) {
        //Получаем и проверяем $jwt
        $jwt = $_GET['jwt'];
        $decode = JWT::decode($jwt, SECRET_KEY, array('HS256'));

        //отправляем пользователю его имя
        $username = [
            'key' => 'username',
            'username' => $decode->sub->username
        ];
        $connection->send(json_encode($username));

        //отправляем пользователю сообщения которые он пропустил
        $connection->send($db->getMessages());

        //добавляем соединение в массив с пользовательскими соединениями
        $users[$decode->sub->username] = $connection;

        //извещаем о том что вошел новый пользователь
        foreach ($users as $connection) {
            $message = [
                'key' => 'onlineEvent',
                'onlineCount' => count($users),
                'message' => 'Вошел пользователь ' . $decode->sub->username
            ];
            $connection->send(json_encode($message));
        }

    };
};
$ws_worker->onClose = function ($connection) use (&$users) {
    //удаляем из хранилища закрытые соединения
    $user = array_search($connection, $users);
    unset($users[$user]);

    //извещаем о событии
    foreach ($users as $connection) {
        $message = ['key' => 'onlineEvent', 'onlineCount' => count($users), 'message' => 'Пользователь ' . $user . ' вышел'];
        $connection->send(json_encode($message));
    }
};

$ws_worker->onMessage = function ($connection, $data) use (&$users, $db) {
    //имя пользователя
    $user = array_search($connection, $users);

    //записываем сообщение в бд
    $db->writeMessage($user, $data);

    //рассылаем всем участникам сообщения
    foreach ($users as $connection) {
        $connection->send('<b>' . $user . '</b> : ' . $data);
    }
};
// Run worker
Worker::runAll();