<?php require_once '../config/config-local.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Chat </title>
    <link rel="stylesheet" href="/chat/style.css">
</head>
<body>
<div class="chat-container">
    <div class="top-menu-container">
        <p class="top-menu-item">Hi, <b id="username-label"></b></p>
        <p class="top-menu-item" id="online"></p>
        <p class="top-menu-item top-menu-link"><a href="/chat/history.php"> History </a></p>
        <p class="top-menu-item top-menu-link"><a id="un-login" href=""> Exit </a></p>
    </div>
    <div id="chatbox"></div>
    <form>
        <input name="message" type="text" id="usermsg">
        <button class="send-button" type="submit" id="submitmsg"> Send </button>
    </form>
</div>
</body>
<script>
    var socket_url = "<?='ws://' . WEB_SOCKET;?>";
    var chat_url = "<?=CHAT_URL?>";
    var auth_url = "<?=AUTH_URL?>";
</script>
<script src="/chat/client.js"></script>
</html>