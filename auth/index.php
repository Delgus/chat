<?php require_once '../config/config-local.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auth Server</title>
    <link rel="stylesheet" href="css/style.css">


</head>
<body>
<div class="auth-container">
    <div class="tabs-container">
        <div class="tab active" id="signinTab">
            <a> Sign In</a>
        </div>
        <div class="tab" id="signupTab">
            <a> Sign Up </a>
        </div>
    </div>
    <form id="signin-form" method="post">
        <div class="auth-form-group">
            <label for="sign-in-username"> Username</label>
            <input id="sign-in-username" type="text" name="username">
        </div>
        <div class="auth-form-group">
            <label for="sign-in-password"> Password </label>
            <input id="sign-in-password" type="password" name="password">
        </div>
        <div class="auth-form-group">
            <button id="sign-in" type="submit"> Sign In</button>
        </div>
    </form>
    <form class="auth-form" id="signup-form" style="display:none">
        <div class="auth-form-group">
            <label for="signup-username"> Username</label>
            <input id="signup-username" type="text" name="username">
        </div>
        <div class="auth-form-group">
            <label for="signup-email"> E-mail </label>
            <input id="signup-email" type="email" name="email">
        </div>
        <div class="auth-form-group">
            <label for="signup-password"> Password </label>
            <input id="signup-password" type="password" name="password">
        </div>
        <div class="auth-form-group">
            <button id="submit" type="submit"> Sign Up</button>
        </div>
    </form>
</div>
<script>
    var auth_url = "<?=AUTH_URL?>";
    var ref = "<?=filter_input(INPUT_GET, 'ref')?>";
</script>
<script src="js/auth.js"></script>
</body>
</html>
