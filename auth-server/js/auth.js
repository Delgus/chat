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
    e.preventDefault();
    var xhr = createRequest();
    var data = new FormData(signinForm);
    xhr.open("POST", '/auth-server/login.php');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            var answer = xhr.responseText;
            if (answer.result) {
                alert("success");
            } else {
                alert("fail");
            }
        }
    };
    xhr.send(data);
}

signupForm.onsubmit = function (e) {
    e.preventDefault();
    var xhr = createRequest();
    var data = new FormData(signupForm);
    xhr.open("POST", '/auth-server/signup.php');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            var answer = xhr.responseText;
            if (answer.result) {
                alert("success");
            } else {
                alert("fail");
            }
        }
    };
    xhr.send(data);
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



