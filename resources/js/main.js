let url = window.location.href.split("/");
let lastOfUrl = url[url.length - 1];
let currentPage = lastOfUrl.split("?")[0].toLowerCase();
let mainPage = false;
let ajaxPath = "BusinessLogic/";
if (currentPage == "" || currentPage == "index.html") mainPage = true;
if (!mainPage) ajaxPath = "../BusinessLogic/"

window.onload = function(){
    readAjax("getLinks", generateNavbar);

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

            errors += reTestText(reName, nameField);
            errors += reTestText(reName, lastNameField);
            errors += reTestText(reEmail, emailField);
            errors += reTestText(rePass1, passwordField);
            errors += reTestText(rePass2, passwordField);
            errors += reTestText(rePass3, passwordField);
            errors += reTestText(rePass4, passwordField);
            errors += reTestText(rePass5, passwordField);

            let data = {"createNewUser" : true, "email" : email, "name" : name, "lastName" : lastName, "pass" : password};
            console.log(data);


            if(errors == 0){
                submitAjax("createNewUser", redirect, data, ["login.html", false]);
            }
        })
    }
    if(currentPage == "login.html"){
        let loginForm = document.querySelector("#loginForm");
        loginForm.addEventListener("submit", function(e){
            e.preventDefault();
            let email = document.querySelector("#emailInput").value;
            let password = document.querySelector("#loginPasswordInput").value;

            //Check if data is valid

            let data = {"attemptLogin" : true, "email" : email, "pass" : password};
            
            submitAjax("attemptLogin", redirect, data, ["index.html", true]);
        })
    }
}

function generateNavbar(response){
    let url;

    data = response.links;
    accessLevel = response.accessLevel;

    let headerHolder = document.querySelector("#headerHolder");
    let navbarHolder = document.querySelector("#navbarHolder");

    let headerElement = data.filter(el => el.location == "head")[0];
    let navbarElements = data.filter(el => el.location == "navbar");

    url = generateUrl(headerElement, "pages/");

    headerHolder.href = url;
    headerHolder.text = headerElement.link_title;

    for(let navbarElement of navbarElements){
    url = generateUrl(navbarElement, "pages/");
    navbarHolder.innerHTML += 
    `
      <li class="nav-item">
        <a class="nav-link ${currentPage == navbarElement.href ? "active" : ""}" aria-current="page" href="${url}">${navbarElement.link_title}</a>
      </li>
    `
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
        return 0;
    }
    else{
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
                resultFunction(args);
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
                resultFunction(args);
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