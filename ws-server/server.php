<?php
require_once '../vendor/autoload.php';
require_once 'config.php';
require_once 'functions.php';

use Workerman\Worker;

$ws_worker = new Worker('websocket://' . WEB_SOCKET);

// storage of user-connection link
$users = [];

$ws_worker->onConnect = function ($connection) use (&$users) {
    $connection->onWebSocketConnect = function ($connection) use (&$users) {

        //отправляем пользователю сообщения которые он пропустил
        $connection->send(getMessages());

        //добавляем соединение в массив с пользовательскими соединениями
        $users[$_GET['user']] = $connection;
        foreach ($users as $connection) {
            $message = ['key' => 'onlineEvent', 'onlineCount' => count($users), 'message' => 'Вошел пользователь ' . $_GET['user']];
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

$ws_worker->onMessage = function ($connection, $data) use (&$users) {
    //имя пользователя
    $user = array_search($connection, $users);

    //записываем сообщение в бд
    writeMessage($user,$data);

    //рассылаем всем участникам сообщения
    foreach ($users as $connection) {
        $connection->send('<b>' . $user . '</b> : ' . $data);
    }
};
// Run worker
Worker::runAll();