var signinTab = document.getElementById("signinTab");
var signupTab = document.getElementById("signupTab");
var signinForm = document.getElementById("signin-form");
var signupForm = document.getElementById("signup-form");

signinTab.onclick = function () {
    signinForm.style.display = '';
    signupForm.style.display = 'none';
    signinTab.classList.add('active');
    signupTab.classList.remove('active');
}
signupTab.onclick = function () {
    signinForm.style.display = 'none';
    signupForm.style.display = '';
    signupTab.classList.add('active');
    signinTab.classList.remove('active');
}
signinForm.onsubmit = function (e) {
    alert("signin");
    e.preventDefault();
    var data = new FormData(signinForm);
    var token = post('/auth-server/login.php', "POST", data);
    if (token) {
        document.cookie = 'jwt=' + token + '; path=/';
        window.location = "/";
    } else {
        alert("Неверные логин или пароль!!!");
    }
}
signupForm.onsubmit = function (e) {
    e.preventDefault();
    var data = new FormData(signupForm);
    var answer = post('/auth-server/signup.php', "POST", data);
    if (answer === 'true') {
        alert('Вы успешно зарегистрировались!!!');
    } else {
        alert(answer);
    }
}

//ajax запрос на чистом js
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

function post(url, method, data) {
    var token = '';
    var xhr = createRequest();
    xhr.open(method, url, false);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            token = xhr.responseText;
        }
    };
    xhr.send(data);
    return token;
}

