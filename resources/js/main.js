let url = window.location.href.split("/");
let lastOfUrl = url[url.length - 1];
let currentPage = lastOfUrl.split("?")[0].toLowerCase();
let mainPage = false;
let ajaxPath = "BusinessLogic/";
let data;
if (currentPage == "" || currentPage == "index.html") mainPage = true;
if (!mainPage) ajaxPath = "../BusinessLogic/"

window.onload = function(){
    data = {currentPage}

    submitAjax("getLinks", generateNavbar, data, ["index.html", true]);

    if(currentPage == "register.html"){
        let registrationForm = document.querySelector("#registrationForm");
        registrationForm.addEventListener("submit", function(e){
            e.preventDefault();

            let emailField = document.querySelector("#emailInput");
            let nameField = document.querySelector("#nameInput");
            let lastNameField = document.querySelector("#lastNameInput");
            let passwordField = document.querySelector("#registerPasswordInput");

            let email = emailField.value;
            let name = nameField.value;
            let lastName = lastNameField.value;
            let password = passwordField.value;

            //Check if data is valid
            let errors = 0;

            let reName = /^[A-Z][a-z]{1,14}(\s[A-Z][a-z]{1,14}){0,2}$/;

            let rePass1 = /[A-Z]/; 
            let rePass2 = /[a-z]/; 
            let rePass3 = /[0-9]/; 
            let rePass4 = /[!\?\.]/; 
            let rePass5 = /^[A-Za-z0-9!\?\.]{7,30}$/;

            let reEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

            let singleTests = [
                {re : reName, field: nameField, message : `Name doesn't match format, eg "Nathan"`},
                {re : reName, field: lastNameField, message : `Last name does not match format, eg "Smith"`},
                {re : reEmail, field: emailField, message : `Email not valid`},
            ]

            let passwordTests = [
            {re : rePass5, field: passwordField, message : `Password must be between 7 and 30 characters long`},
            {re : rePass1, field: passwordField, message : `Password must contain an uppercase letter`},
            {re : rePass2, field: passwordField, message : `Password must contain a lowercase letter`},
            {re : rePass3, field: passwordField, message : `Password must contain a digit`},
            {re : rePass4, field: passwordField, message : `Password must contain ! or ? or .`}
            ]

            for(let test of singleTests){
                errors += reTestText(test.re, test.field, test.message);
            }

            for(let test of passwordTests){
                let result = reTestText(test.re, test.field, test.message);
                errors += result;

                if(result > 0) break;
            }

            let data = {"createNewUser" : true, "email" : email, "name" : name, "lastName" : lastName, "pass" : password};

            if(errors == 0){
                submitAjax("createNewUser", redirect, data, ["login.html", false]);
            }
        })
    }
    if(currentPage == "login.html"){
        let loginForm = document.querySelector("#loginForm");
        loginForm.addEventListener("submit", function(e){
            e.preventDefault();

            let emailField = document.querySelector("#emailInput");
            let passwordField = document.querySelector("#loginPasswordInput");

            let email = emailField.value;
            let password = passwordField.value;

            //Check if data is valid
            let errors = 0;

            let rePass1 = /[A-Z]/; 
            let rePass2 = /[a-z]/; 
            let rePass3 = /[0-9]/; 
            let rePass4 = /[!\?\.]/; 
            let rePass5 = /^[A-Za-z0-9!\?\.]{7,30}$/;

            let reEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

            errors += reTestText(reEmail, emailField);

            errors += reTestText(rePass1, passwordField);
            errors += reTestText(rePass2, passwordField);
            errors += reTestText(rePass3, passwordField);
            errors += reTestText(rePass4, passwordField);
            errors += reTestText(rePass5, passwordField);

            let data = {"attemptLogin" : true, "email" : email, "pass" : password};

            if(errors == 0){
                submitAjax("attemptLogin", redirect, data, ["index.html", true]);
            }
            else{
                //Login failed
            }
        })
    }
}

function generateNavbar(response){
    let url;

    data = response.links;
    accessLevel = response.accessLevel;

    let headerHolder = document.querySelector("#headerHolder");
    let footerHeaderHolder = document.querySelector("#footerHeaderHolder");
    let navbarHolder = document.querySelector("#navbarHolder");
    let footerHolder = document.querySelector("#footerHolder");

    let headerElement = data.filter(el => el.location == "head")[0];
    let navbarElements = data.filter(el => el.location == "navbar");
    let footerElements = data.filter(el => el.location == "footer");

    url = generateUrl(headerElement, "pages/");

    headerHolder.href = url;
    headerHolder.text = headerElement.link_title;

    footerHeaderHolder.href = url;
    footerHeaderHolder.text = headerElement.link_title;

    for(let navbarElement of navbarElements){
        navbarHolder.innerHTML += generateLinkElement(navbarElement, "pages/");
    }

    if(accessLevel > 1){
        navbarHolder.innerHTML += 
        `
          <li class="nav-item">
            <a class="nav-link" id="logoutButton" aria-current="page" href="#">Log out</a>
          </li>
        `;
        let logoutButton = document.querySelector("#logoutButton");
        logoutButton.addEventListener("click", function(){
            readAjax("logout", redirect, ["login.html", false]);
        })
    }

    for(let footerElement of footerElements){
        footerHolder.innerHTML += generateLinkElement(footerElement);
    }
}

function createRequest(){
    let request = false;
    try{
        request = new XMLHttpRequest();
    }
    catch{
        try{
            request = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch{
            try{
                request = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch{
                console.log("Ajax is not supported");
            }
        }
    }
    return request;
}

function reTestText(regex, field, errorMessage = ""){
    let textValue = field.value;
    let passes = regex.test(textValue);
    if(passes){
        if(errorMessage != ""){
            let errorBox = field.nextElementSibling;
            errorBox.classList.add("hidden");
            field.classList.remove("error-outline");
            field.classList.add("success-outline");
        }
        return 0;
    }
    else{
        if(errorMessage != ""){
            let errorBox = field.nextElementSibling;
            errorBox.innerText = errorMessage;
            errorBox.classList.remove("hidden");
            field.classList.remove("success-outline");
            field.classList.add("error-outline");
        }
        return 1;
    }
}

function redirect(args){
    let newLocation = args[0];
    let landing = args[1];
    let newLink = window.location.hostname + "/nycestatee" + (landing ? `/${newLocation}` : `/pages/${newLocation}`); 
    window.location.assign("https://" + newLink);
}

function readAjax(url, resultFunction, args = []){
    let request = createRequest();
    request.onreadystatechange = function(){
        if(request.readyState == 4){
            if(request.status >= 200 && request.status < 300){
                console.log(request.responseText);
                let data = JSON.parse(request.responseText);
                if(args != []){
                    resultFunction(data.general, args);
                }
                else{
                    resultFunction(data.general);
                }
            }
            else if(request.status >= 300 && request.status < 400){
                redirect(args);
            }
            else{
                console.log(request.responseText);
                let data = JSON.parse(request.responseText);
                console.log(data["error"]);
            }
        }
    }
    request.open("GET", ajaxPath+url+".php");
    request.send();
}

function submitAjax(url, resultFunction, data, args = []){
    let request = createRequest();
    request.onreadystatechange = function(){
        if(request.readyState == 4){
            if(request.status >= 200 && request.status < 300){
                console.log(request.responseText);
                let data = JSON.parse(request.responseText);
                if(args != []){
                    resultFunction(data.general, args);
                }
                else{
                    resultFunction(data.general);
                }
            }
            else if(request.status >= 300 && request.status < 400){
                redirect(args);
            }
            else{
                let data = JSON.parse(request.responseText);
                console.log(data["error"]);
            }
        }
    }

    request.open("POST", ajaxPath+url+".php");
    request.setRequestHeader("Content-type", "application/json");
    request.send(JSON.stringify(data));
}

function generateUrl(object, redirect = ""){
    let url = "";
    if(object.landing){
       url += mainPage? '' : '../';
    }
    else{
       url += mainPage? `${redirect}` : '';
    }
    url += object.href;
    return url;
 }

 function generateLinkElement(object, redirect = ""){
    let html;
    let url = generateUrl(object, redirect);
    let text = object.link_title;
    let icon = object.icon;
    if(icon == null){
        html = 
        `
        <li class="nav-item">
          <a class="nav-link ${currentPage == object.href ? "active" : ""}" id=${text} aria-current="page" href="${url}">${text}</a>
        </li>
      `
    }
    else{
        html = `
        <li class="col-md-3 col-6 icon-holder">
            <a class="" href="${url}">
                <span class="iconify" id="${text}-icon" data-icon="${icon}"></span>
            </a>
        </li>
        `
    }
    return html;
 }