let signinTab = document.getElementById("signinTab");
let signupTab = document.getElementById("signupTab");
let signinForm = document.getElementById("signin-form");
let signupForm = document.getElementById("signup-form");

signinTab.onclick = function () {
    signinForm.style.display = '';
    signupForm.style.display = 'none';
    signinTab.classList.add('active');
    signupTab.classList.remove('active');
};

signupTab.onclick = function () {
    signinForm.style.display = 'none';
    signupForm.style.display = '';
    signupTab.classList.add('active');
    signinTab.classList.remove('active');
};

signinForm.onsubmit = function () {
    let xhr = createRequest();
    if (!xhr) {
        alert("Невозможно создать XMLHttpRequest");
        return;
    }

    let data = new FormData(signinForm);
    xhr.open("POST", `/auth/login.php`);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            let answer = JSON.parse(xhr.responseText);
            if (answer.result) {
                document.cookie = 'jwt=' + answer.jwt + '; path=/';
                window.location = '/chat';
            } else {
                alert("Fail!");
            }
        }
    };
    xhr.send(data);
    return false;
};

signupForm.onsubmit = function (e) {
    e.preventDefault();
    let xhr = createRequest();
    if (!xhr) {
        alert("Невозможно создать XMLHttpRequest");
        return;
    }

    let data = new FormData(signupForm);
    xhr.open("POST", `/auth/signup.php`);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            let answer = JSON.parse(xhr.responseText);
            if (answer.result) {
                alert("SUCCESS");
            } else {
                alert("FAIL");
            }
        }
    };
    xhr.send(data);
};

const jwt = getCookie("jwt");
if (jwt) {
    window.location = `/chat`;
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

// возвращает куки с указанным name,
// или undefined, если ничего не найдено
function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}



