<?php
require_once '../vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
if ( $_POST ) {
	$post = filter_input_array( INPUT_POST, $_POST );
	try {
		$db   = new \db\Db();
		$user = $db->getUserByUsername( $post['username'] );
	} catch ( Throwable $e ) {
		echo json_encode( [ 'result' => false, 'errors' => 'Internal error' ] );
		return;
	}
	//validate exist username and  correct password
	if ( ! $user || ! password_verify( $post['password'], $user['password_hash'] ) ) {
		echo json_encode( [ 'result' => false, 'errors' => 'Incorrect login or password' ] );
		return;
	}
	//create jwt
	$token = [
		"iss" => getenv("HOST"), //issuer
		// "aud" => WEB_SOCKET,//audience
		"iat" => time(),    //issued at
		"exp" => time() + getenv("TOKEN_LIVE"),//expire time
		//subject
		"sub" => [
			'id'       => $user['id'],
			'username' => $user['username']
		],
	];
	$jwt   = \Firebase\JWT\JWT::encode( $token, getenv("SECRET_KEY"));
	echo json_encode( [ 'result' => true, 'jwt' => $jwt ] );
}
