<?php
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Workerman\Worker;
use db\Db;

$ws_worker = new Worker('websocket://' . getenv("HOST") .":".getenv("PORT"). "/ws");
$db = new Db();

const ONLINE_EVENT = 'onlineEvent';
const ATTACK_EVENT = 'attackEvent';
const EXPIRED_EVENT = 'expiredEvent';
const USERNAME_EVENT = 'usernameEvent';
const LAST_MESSAGES_EVENT = 'lastMessagesEvent';
const NEW_MESSAGE_EVENT = 'newMessageEvent';

function message($event, $data)
{
    return json_encode(["type" => $event, "data" => $data]);
}

// storage of user-connection link
$users = [];

date_default_timezone_set(getenv("TIME_ZONE"));

function attackWrite($connection, $users)
{
    $connection->send(message(ATTACK_EVENT, 'The server has been attacked!'));
    foreach ($users as $connection) {
        $connection->send(message(ATTACK_EVENT, 'The server has been attacked!'));
    }
}

$ws_worker->onConnect = function ($connection) use (&$users, $db) {
    $connection->onWebSocketConnect = function ($connection) use (&$users, $db) {
        //get and validate JWT
        $jwt = $_GET['jwt'];
        try {
            $decode = JWT::decode($jwt, getenv("SECRET_KEY"), array('HS256'));
        } catch (DomainException $e) {

        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            attackWrite($connection, $users);
        } catch (\Firebase\JWT\BeforeValidException $e) {
            attackWrite($connection, $users);
        } catch (\Firebase\JWT\ExpiredException $e) {
            $connection->send(message(EXPIRED_EVENT, 'Token are expired!'));
        }

        if (isset($decode)) {
            $connection->send(message(USERNAME_EVENT, $decode->sub->username));

            $connection->send(message(LAST_MESSAGES_EVENT, $db->getMessages()));

            $users[$decode->sub->username] = $connection;

            foreach ($users as $connection) {
                $connection->send(message(ONLINE_EVENT, [
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
        $connection->send(message(ONLINE_EVENT, [
            'count' => count($users),
            'message' => 'User ' . $user . ' left'
        ]));
    }
};

$ws_worker->onMessage = function ($connection, $data) use (&$users, $db) {

    $user = array_search($connection, $users);
    foreach ($users as $connection) {
        $date = date(getenv("TIME_FORMAT"));
        $connection->send(message(NEW_MESSAGE_EVENT, "<b>{$user}</b> [ {$date}]: {$data}"));
        $db->writeMessage($user, $data);
    }
};
// Run worker
Worker::runAll();