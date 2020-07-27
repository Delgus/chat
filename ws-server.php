<?php
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Workerman\Worker;
use db\DB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// read environments
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// create logger
$log = new Logger('ws-logger');
$log->pushHandler(new StreamHandler(__DIR__ . '/logs/ws-server.log', Logger::DEBUG));

$db = new DB($_ENV["DB_DSN"], $_ENV["DB_USERNAME"], $_ENV["DB_PASSWORD"]);

$ws_worker = new Worker($_ENV["WS_SERVER_ADDR"]);

const ONLINE_EVENT = 'onlineEvent';
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

date_default_timezone_set($_ENV["TIME_ZONE"]);

$ws_worker->onConnect = function ($connection) use (&$users, $db) {
    $connection->onWebSocketConnect = function ($connection) use (&$users, $db) {
        //get and validate JWT
        $jwt = $_GET['jwt'];
        try {
            $decode = JWT::decode($jwt, $_ENV["SECRET_KEY"], ['HS256']);
        } catch (SignatureInvalidException $e) {
            return;
        } catch (BeforeValidException $e) {
            return;
        } catch (ExpiredException $e) {
            $connection->send(message(EXPIRED_EVENT, 'Token are expired!'));
            return;
        }

        $connection->send(message(USERNAME_EVENT, $decode->sub->username));

        $connection->send(message(LAST_MESSAGES_EVENT, $db->getMessages(intval($_ENV["MESSAGES_ON_PAGE"]), $_ENV["TIME_FORMAT"])));

        $users[$decode->sub->username] = $connection;

        foreach ($users as $connection) {
            $connection->send(message(ONLINE_EVENT, [
                'count' => count($users),
                'message' => 'Logged in user ' . $decode->sub->username
            ]));
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
        $date = date($_ENV["TIME_FORMAT"]);
        $message = sprintf("<b>%s</b> [%s]: %s", $user, $date, $data);
        $connection->send(message(NEW_MESSAGE_EVENT, $message));
        $db->writeMessage($user, $data);
    }
};

// Run worker
$log->debug("start websocket server");
Worker::runAll();
