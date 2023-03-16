let url = window.location.href.split("/");
let currentPage = url[url.length - 1];
let mainPage = false;
let ajaxPath = "BusinessLogic/";
if (currentPage == "" || currentPage.toLowerCase() == "index.html") mainPage = true;
if (!mainPage) ajaxPath = "../BusinessLogic/"

window.onload = function(){
    readAjax("getLinks.php", generateNavbar);
}

function generateNavbar(data){
    let headerHolder = document.querySelector("#headerHolder");
    let navbarHolder = document.querySelector("#navbarHolder");

    let headerElement = data.filter(el => el.location == "head")[0];
    let navbarElements = data.filter(el => el.location == "navbar");

    headerHolder.href = headerElement.href;
    headerHolder.text = headerElement.link_title;

    for(let navbarElement of navbarElements){
    navbarHolder.innerHTML += 
    `
      <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="${navbarElement.href}">${navbarElement.link_title}</a>
      </li>
    `
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
            else{
                console.log(request.responseText);
                let data = JSON.parse(request.responseText);
                console.log(data["error"]);
            }
        }
    }
    request.open("GET", ajaxPath+url);
    request.send();
}