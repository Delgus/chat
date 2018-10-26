if (!document.cookie) {
    //if not authorized - send on auth-server
    window.location = auth_url + '/?ref=' + chat_url;
}
window.onload = function () {
    var ws = new WebSocket(socket_url + "/?" + document.cookie);

    ws.onerror = function () {
        alert("WEBSOCKET SERVER DOESN'T WORK!");
    }

    ws.onmessage = function (e) {
        var chatbox = document.getElementById("chatbox");
        var content = JSON.parse(e.data);
        var responseMap = {
            onlineEvent: function (data) {
                document.getElementById('online').innerHTML = 'Online: ' + data.count;
                chatbox.innerHTML += '<b>' + data.message + '</b><br>';
                chatbox.scrollTop = 9999;
            },
            usernameEvent: function (data) {
                document.getElementById("username-label").innerHTML = data;
            },
            newMessageEvent: function (data) {

                chatbox.innerHTML += data + "<br>";
                chatbox.scrollTop = 9999;
            },
            lastMessagesEvent: function (data) {
                for (var l in data) {
                    chatbox.innerHTML += "<b>" + data[l].author + "</b> [" + data[l].time + "] : " + data[l].text + "<br>";
                    chatbox.scrollTop = 9999;
                }
            },
            attackEvent: function () {
                unlogin();
            },
            expiredEvent: function () {
                unlogin();
            }
        };
        if (content.type) {
            responseMap[content.type](content.data);
        }
    };

    function unlogin() {
        delete_cookie('jwt');
        window.location = auth_url + '/?ref=' + chat_url;
    }

    //отправка сообщений на вебсокет
    var form = document.querySelector('form');
    form.onsubmit = function () {
        if (form[0].value !== '') {
            ws.send(form[0].value);
        }
        form[0].value = '';
        return false;
    }

    var un_login = document.getElementById("un-login");
    un_login.onclick = function (e) {
        e.preventDefault();
        unlogin();
    }

    function delete_cookie(name) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
    }
}