function redirectToAuth() {
    window.location = `/auth`;
}

const jwt = getCookie("jwt");
if (!jwt) {
    redirectToAuth();
}

// возвращает куки с указанным name,
// или undefined, если ничего не найдено
function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

window.onload = function () {
    let settings = getSettings();
    if(!settings){
        alert("can not load settings");
        return;
    }

    let ws = new WebSocket(`${settings.ws}?jwt=${jwt}`);

    ws.onerror = function () {
        alert("WEBSOCKET SERVER DOESN'T WORK!");
    };

    ws.onmessage = function (e) {
        let chat_box = document.getElementById("chat_box");
        console.log(e.data);
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
                for (let m of data) {
                    chat_box.innerHTML += "<b>" + m.author + "</b> [" + m.time + "] : " + m.text + "<br>";
                    chat_box.scrollTop = 9999;
                }
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

    function getSettings() {
        let xhr = createRequest();
        if (!xhr) {
            alert("Невозможно создать XMLHttpRequest");
            return;
        }

        let settings;
        xhr.open("GET", `/settings/api.php`, false);
        xhr.send();
        if (xhr.status === 200) {
            return JSON.parse(xhr.responseText);
        }
        return false;
    }

    //ajax запрос на чистом js
    function createRequest() {
        if (window.XMLHttpRequest) {
            //Gecko-совместимые браузеры, Safari, Konqueror
            return new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            //Internet explorer
            try {
                return new ActiveXObject("Microsoft.XMLHTTP");
            } catch (CatchException) {
                return new ActiveXObject("Msxml2.XMLHTTP");
            }
        }
    }


};