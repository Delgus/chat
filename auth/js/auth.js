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
    let data = new FormData(signinForm);
    xhr.open("POST", `https://${document.location.host}/auth/login.php`);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            let answer = JSON.parse(xhr.responseText);
            if (answer.result) {
                document.cookie = 'jwt=' + answer.jwt + '; path=/';
                window.location = ref;
            } else {
                alert("Fail!");
            }
        }
    };
    xhr.send(data);
    return false;
}

signupForm.onsubmit = function (e) {
    e.preventDefault();
    let xhr = createRequest();
    let data = new FormData(signupForm);
    xhr.open("POST", `https://${document.location.host}/auth/signup.php`);
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

if (document.cookie) {
    window.location = `https://${document.location.host}/chat`;
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



