function redirectToAuth() {
    window.location = `https://${document.location.host}/auth`;
}

if (!document.cookie) {
    redirectToAuth();
}

window.onload = function () {
    let ws = new WebSocket("wss://" + document.location.host + "/ws?" + document.cookie);

    ws.onerror = function () {
        alert("WEBSOCKET SERVER DOESN'T WORK!");
    };

    ws.onmessage = function (e) {
        let chat_box = document.getElementById("chat_box");
        let content = JSON.parse(e.data);
        let responseMap = {
            onlineEvent: function (data) {
                document.getElementById('online').innerHTML = 'Online: ' + data.count;
                chat_box.innerHTML += '<b>' + data.message + '</b><br>';
                chat_box.scrollTop = 9999;
            },
            usernameEvent: function (data) {
                document.getElementById("username-label").innerHTML = data;
            },
            newMessageEvent: function (data) {

                chat_box.innerHTML += data + "<br>";
                chat_box.scrollTop = 9999;
            },
            lastMessagesEvent: function (data) {
                for (var l in data) {
                    chat_box.innerHTML += "<b>" + data[l].author + "</b> [" + data[l].time + "] : " + data[l].text + "<br>";
                    chat_box.scrollTop = 9999;
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
        redirectToAuth();
    }

    //отправка сообщений на вебсокет
    let form = document.querySelector('form');
    form.onsubmit = function () {
        if (form[0].value !== '') {
            ws.send(form[0].value);
        }
        form[0].value = '';
        return false;
    };

    let un_login = document.getElementById("un-login");
    un_login.onclick = function (e) {
        e.preventDefault();
        unlogin();
    };

    function delete_cookie(name) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
    }


};