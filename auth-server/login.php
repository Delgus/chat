<?php
require_once '../vendor/autoload.php';
require_once '../config/config.php';

if ( $_POST ) {
	$post = filter_input_array( INPUT_POST, $_POST );
	try {
		$db   = new \db\Db( DB_DSN, DB_USERNAME, DB_PASSWORD );
		$user = $db->getUserByUsername( $post['username'] );
	} catch ( Throwable $e ) {
		echo json_encode( [ 'result' => false, 'errors' => 'Internal error' ] );
		exit;
	}
	//validate exist username and  correct password
	if ( ! $user && ! password_verify( $post['password'], $user['password_hash'] ) ) {
		echo json_encode( [ 'result' => false, 'errors' => 'Incorrect login or password' ] );
		exit;
	}
	//create jwt
	$token = [
		"iss" => HOST_NAME, //issuer
		"aud" => WEB_SOCKET,//audience
		"iat" => time(),    //issued at
		"exp" => time() + TOKEN_LIVE,//expire time
		//subject
		"sub" => [
			'id'       => $user['id'],
			'username' => $user['username']
		],
	];
	$jwt   = \Firebase\JWT\JWT::encode( $token, SECRET_KEY );
	echo json_encode( [ 'result' => true, 'jwt' => $jwt ] );
	exit;
}