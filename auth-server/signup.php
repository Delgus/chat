<?php
require_once '../vendor/autoload.php';
require_once '../config/config.php';

if ($_POST) {
    $post = filter_input_array(INPUT_POST, $_POST);


    $errors = [];


    //require field username
    if (empty($post['username'])) {
        $errors['username'] = 'Username is required';
    }

    //require email
    if (empty($post['email'])) {
        $errors['email'] = 'Email is required';
    }

    //require password
    if (empty($post['password'])) {
        $errors['password'] = 'Password is required';
    }

    if (empty($errors)) {
        $db = new \db\Db(DB_DSN, DB_USERNAME, DB_PASSWORD);
        //unique username
        if ($db->getUserByUsername($post['username'])) {
            $errors['username'] = 'Not unique user!';
        }
        //unique email
        if ($db->getUserByEmail($post['email'])) {
            $errors['email'] = 'Not unique email!';
        }
    }

    if (empty($errors)) {
        //save user
        $password_hash = password_hash($post['password'], PASSWORD_DEFAULT);
        $db->saveUser($post['username'], $post['email'], $password_hash);
        echo "true";
        exit;
    }
    echo json_encode($errors);
}
//silence is gold
die();