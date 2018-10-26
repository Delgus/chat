<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/config-local.php';

use Firebase\JWT\JWT;
use Workerman\Worker;
use db\Db;
use chat\ServerMessage;

$ws_worker = new Worker('websocket://' . WEB_SOCKET);
$db = new Db(DB_DSN, DB_USERNAME, DB_PASSWORD);

// storage of user-connection link
$users = [];

$ws_worker->onConnect = function ($connection) use (&$users, $db) {
    $connection->onWebSocketConnect = function ($connection) use (&$users, $db) {
        //get and validate JWT
        $jwt = $_GET['jwt'];
        try {
            $decode = JWT::decode($jwt, SECRET_KEY, array('HS256'));
        } catch (DomainException $e) {
            $connection->send((string)new ServerMessage(ServerMessage::ATTACK_EVENT, 'The server has been attacked!'));
            foreach ($users as $connection) {
                $connection->send((string)new ServerMessage(ServerMessage::ATTACK_EVENT, 'The server has been attacked!'));
            }
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $connection->send((string)new ServerMessage(ServerMessage::ATTACK_EVENT, 'The server has been attacked!'));
            foreach ($users as $connection) {
                $connection->send((string)new ServerMessage(ServerMessage::ATTACK_EVENT, 'The server has been attacked!'));
            }
        } catch (\Firebase\JWT\BeforeValidException $e) {
            $connection->send((string)new ServerMessage(ServerMessage::ATTACK_EVENT, 'The server has been attacked!'));
            foreach ($users as $connection) {
                $connection->send((string)new ServerMessage(ServerMessage::ATTACK_EVENT, 'The server has been attacked'));
            }
        } catch (\Firebase\JWT\ExpiredException $e) {
            $connection->send((string)new ServerMessage(ServerMessage::EXPIRED_EVENT, 'Token are expired!'));
        }

        if (isset($decode)) {
            $connection->send((string)new ServerMessage(ServerMessage::USERNAME_EVENT, $decode->sub->username));

            $connection->send((string)new ServerMessage(ServerMessage::LAST_MESSAGES_EVENT, $db->getMessages()));

            $users[$decode->sub->username] = $connection;

            foreach ($users as $connection) {
                $connection->send((string)new ServerMessage(ServerMessage::ONLINE_EVENT, [
                    'count' => count($users),
                    'message' => 'Logged in user ' . $decode->sub->username
                ]));
            }
        }


    };
};
$ws_worker->onClose = function ($connection) use (&$users) {

    $user = array_search($connection, $users);
    unset($users[$user]);
    foreach ($users as $connection) {
        $connection->send((string)new ServerMessage(ServerMessage::ONLINE_EVENT, [
            'count' => count($users),
            'message' => 'User ' . $user . ' left'
        ]));
    }
};

$ws_worker->onMessage = function ($connection, $data) use (&$users, $db) {

    $user = array_search($connection, $users);
    foreach ($users as $connection) {
        $connection->send((string)new ServerMessage(ServerMessage::NEW_MESSAGE_EVENT,
            '<b>' . $user . '</b> [' . date(TIME_FORMAT) . ']: ' . $data
        ));
    }
    $db->writeMessage($user, $data);
};
// Run worker
Worker::runAll();