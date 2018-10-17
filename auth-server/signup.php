<?php
require_once '../vendor/autoload.php';
require_once '../config/config.php';

if ($_POST) {
    $post = filter_input_array(INPUT_POST,$_POST);
    //TODO:unique username and email
    $password_hash = password_hash($post['password'],PASSWORD_DEFAULT);
    $db = new \db\Db(DB_DSN,DB_USERNAME,DB_PASSWORD);
    $db->saveUser($post['username'],$post['email'],$password_hash);
}
//silence is gold
die();