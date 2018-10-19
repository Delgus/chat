<?php
require_once '../vendor/autoload.php';
require_once '../config/config.php';

use Firebase\JWT\JWT;
use Workerman\Worker;
use db\Db;
use ws\ServerMessage;

$ws_worker = new Worker('websocket://' . WEB_SOCKET);
$db = new Db(DB_DSN, DB_USERNAME, DB_PASSWORD);

// storage of user-connection link
$users = [];

$ws_worker->onConnect = function ($connection) use (&$users, $db) {
    $connection->onWebSocketConnect = function ($connection) use (&$users, $db) {
        //Получаем и проверяем $jwt
        $jwt = $_GET['jwt'];
        try {
            $decode = JWT::decode($jwt, SECRET_KEY, array('HS256'));
        } catch (DomainException $e) {
            $connection->send((string)new ServerMessage(ServerMessage::ATTACK_EVENT, 'Сервер был атакован!'));
            foreach ($users as $connection) {
                $connection->send((string)new ServerMessage(ServerMessage::ATTACK_EVENT, 'Сервер был атакован!'));
            }
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $connection->send((string)new ServerMessage(ServerMessage::ATTACK_EVENT, 'Сервер был атакован!'));
            foreach ($users as $connection) {
                $connection->send((string)new ServerMessage(ServerMessage::ATTACK_EVENT, 'Сервер был атакован!'));
            }
        } catch (\Firebase\JWT\BeforeValidException $e) {
            $connection->send((string)new ServerMessage(ServerMessage::ATTACK_EVENT, 'Сервер был атакован!'));
            foreach ($users as $connection) {
                $connection->send((string)new ServerMessage(ServerMessage::ATTACK_EVENT, 'Сервер был атакован!'));
            }
        } catch (\Firebase\JWT\ExpiredException $e) {
            $connection->send((string)new ServerMessage(ServerMessage::ATTACK_EVENT, 'Токен просрочен!'));
        }

        if (isset($decode)) {
            //отправляем пользователю его имя
            $connection->send((string)new ServerMessage(ServerMessage::USERNAME_EVENT, $decode->sub->username));

            //отправляем пользователю сообщения которые он пропустил
            $connection->send((string)new ServerMessage(ServerMessage::LAST_MESSAGES_EVENT, $db->getMessages()));

            //добавляем соединение в массив с пользовательскими соединениями
            $users[$decode->sub->username] = $connection;

            //извещаем о том что вошел новый пользователь
            foreach ($users as $connection) {
                $connection->send((string)new ServerMessage(ServerMessage::ONLINE_EVENT, [
                    'count' => count($users),
                    'message' => 'Вошел пользователь ' . $decode->sub->username
                ]));
            }
        }


    };
};
$ws_worker->onClose = function ($connection) use (&$users) {
    //удаляем из хранилища закрытые соединения
    $user = array_search($connection, $users);
    unset($users[$user]);

    //извещаем о событии
    foreach ($users as $connection) {
        $connection->send((string)new ServerMessage(ServerMessage::ONLINE_EVENT, [
            'count' => count($users),
            'message' => 'Пользователь ' . $user . ' вышел'
        ]));
    }
};

$ws_worker->onMessage = function ($connection, $data) use (&$users, $db) {
    //имя пользователя
    $user = array_search($connection, $users);

    //записываем сообщение в бд
    $db->writeMessage($user, $data);

    //рассылаем всем участникам сообщения
    foreach ($users as $connection) {
        $connection->send((string)new ServerMessage(ServerMessage::NEW_MESSAGE_EVENT, '<b>' . $user . '</b> : ' . $data));
    }
};
// Run worker
Worker::runAll();