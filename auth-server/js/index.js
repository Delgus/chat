//Табы на чистом js. Потому что тянуть мегатяжелую Jquery не хочу
var sign_tab = document.getElementById("sign-tab");
var login_tab = document.getElementById("login-tab");
var sign_up_container = document.getElementById("signup");
var login_container = document.getElementById("login");
sign_tab.addEventListener('click', function (e) {
    e.preventDefault();
    login_container.style.display = 'none';
    sign_up_container.style.display = '';
    sign_tab.classList.add("active");
    login_tab.classList.remove("active");
});
login_tab.addEventListener('click', function (e) {
    e.preventDefault();
    sign_up_container.style.display = 'none';
    login_container.style.display = '';
    login_tab.classList.add("active");
    sign_tab.classList.remove("active");
});

//очистка инпутов. пришлось подзаморочиться но jquery все равно не буду тащить
var su_username = document.getElementById("su-username");
var su_email = document.getElementById("su-email");
var su_password = document.getElementById("su-password");
var l_username = document.getElementById("l-username");
var l_password = document.getElementById("l-password");
var su_username_label = document.getElementById("su-username-label");
var su_email_label = document.getElementById("su-email-label");
var su_password_label = document.getElementById("su-password-label");
var l_username_label = document.getElementById("l-username-label");
var l_password_label = document.getElementById("l-password-label");


su_username.addEventListener('keyup', function () {
    if (this.value === '') {
        su_username_label.className = '';
    } else {
        su_username_label.className = 'active highlight';
    }
});
su_username.addEventListener('blur', function () {
    if (this.value === '') {
        su_username_label.className = '';
    } else {
        su_username_label.className = 'active';
    }
});
su_username.addEventListener('focus', function () {
    if (this.value === '') {
        su_username_label.className = 'active';
    }
});

su_email.addEventListener('keyup', function () {
    if (this.value === '') {
        su_email_label.className = '';
    } else {
        su_email_label.className = 'active highlight';
    }
});
su_email.addEventListener('blur', function () {
    if (this.value === '') {
        su_email_label.className = '';
    } else {
        su_email_label.className = 'active';
    }
});
su_email.addEventListener('focus', function () {
    if (this.value === '') {
        su_email_label.className = 'active';
    }
});

su_password.addEventListener('keyup', function () {
    if (this.value === '') {
        su_password_label.className = '';
    } else {
        su_password_label.className = 'active highlight';
    }
});
su_password.addEventListener('blur', function () {
    if (this.value === '') {
        su_password_label.className = '';
    } else {
        su_password_label.className = 'active';
    }
});
su_password.addEventListener('focus', function () {
    if (this.value === '') {
        su_password_label.className = 'active';
    }
});

l_username.addEventListener('keyup', function () {
    if (this.value === '') {
        l_username_label.className = '';
    } else {
        l_username_label.className = 'active highlight';
    }
});
l_username.addEventListener('blur', function () {
    if (this.value === '') {
        l_username_label.className = '';
    } else {
        l_username_label.className = 'active';
    }
});
l_username.addEventListener('focus', function () {
    if (this.value === '') {
        l_username_label.className = 'active';
    }
});

l_password.addEventListener('keyup', function () {
    if (this.value === '') {
        l_password_label.className = '';
    } else {
        l_password_label.className = 'active highlight';
    }
});
l_password.addEventListener('blur', function () {
    if (this.value === '') {
        l_password_label.className = '';
    } else {
        l_password_label.className = 'active';
    }
});
l_password.addEventListener('focus', function () {
    if (this.value === '') {
        l_password_label.className = 'active';
    }
});

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

var sign_up_form = document.getElementById("signup-form");
var login_form = document.getElementById("login-form");
sign_up_form.onsubmit = function (e) {
    e.preventDefault();
    var data = new FormData(sign_up_form);
    var answer = post('/auth-server/signup.php', "POST", data);
    if (answer === 'true') {
        alert('Вы успешно зарегистрировались!!!');
    }
}
login_form.onsubmit = function (e) {
    e.preventDefault();
    var data = new FormData(login_form);
    var token = post('/auth-server/login.php', "POST", data);
    if (token !== 'false') {
        document.cookie = 'jwt=' + token + '; path=/';
        window.location = "/";
    } else {
        alert("Неверные логин или пароль!!!");
    }
}
if (document.cookie) {
    window.location = "/";
}


