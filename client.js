if (!document.cookie) {
    //если не авторизован - отправляем на auth-server
    window.location = "/auth-server";
}
window.onload = function () {
    //ajax-address websocket
    var sock_address = getResponse('/ws-server/websocket.php');
    var ws = new WebSocket(sock_address + "/?" + document.cookie);

    //обработка ответов вебсокета
    ws.onmessage = function (e) {
        var chatbox = document.getElementById("chatbox");
        var content = JSON.parse(e.data);
        var responseMap = {
            onlineEvent: function (data) {
                //онлайн/оффлайн событие
                document.getElementById('online').innerHTML = 'Онлайн: ' + data.count;
                chatbox.innerHTML += '<b>' + data.message + '</b><br>';
                chatbox.scrollTop = 9999;
            },
            usernameEvent: function (data) {
                //имя пользователя
                document.getElementById("username-label").innerHTML = data;
            },
            newMessageEvent: function (data) {
                //новое сообщение
                chatbox.innerHTML += data + "<br>";
                chatbox.scrollTop = 9999;
            },
            lastMessagesEvent: function (data) {
                for (var l in data) {
                    chatbox.innerHTML += "<b>" + data[l].author + "</b> : " + data[l].text + "<br>";
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
        window.location = "/auth-server";
    }

    //отправка сообщений на вебсокет
    var form = document.querySelector('form');
    form.onsubmit = function () {
        console.log(form);
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

    //вспомогательные функции
    function createRequest() {
        var Request = false;

        if (window.XMLHttpRequest) {
            //Gecko-совместимые браузеры, Safari, Konqueror
            Request = new XMLHttpRequest();
        }
        else if (window.ActiveXObject) {
            //Internet explorer
            try {
                Request = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (CatchException) {
                Request = new ActiveXObject("Msxml2.XMLHTTP");
            }
        }

        if (!Request) {
            alert("Невозможно создать XMLHttpRequest");
        }

        return Request;
    }

    function getResponse(url) {
        var sock_address = '';
        var xhr = createRequest();
        xhr.open("GET", url, false);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                sock_address = xhr.responseText;
            }
        };
        xhr.send();
        return sock_address;
    }

    function delete_cookie(name) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }


}