let url = window.location.href.split("/");
let lastOfUrl = url[url.length - 1];
let currentPage = lastOfUrl.split("?")[0].toLowerCase();
let mainPage = false;
let ajaxPath = "BusinessLogic/";
let data;
let globalData = {};
let success;
if (currentPage == "" || currentPage == "index.html") mainPage = true;
if (!mainPage) ajaxPath = "../BusinessLogic/"

window.onload = function(){
    data = {currentPage}

    //Send current page to check if allwwed
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

            let data = {"createNewUser" : true, "email" : email, "name" : name, "lastName" : lastName, "password" : password};

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
                errorHandler("Incorrect email/password");
            }
        })
    }
    if(currentPage === "admin.html"){
        let currData;
        globalData.modalBackground = document.querySelector(".mk-modal");
        window.addEventListener("click", function(e){
            if(e.target === globalData.modalBackground){
                closeCurrentModal();
            }
        })
        let tables = [
                      {title : "Users", headers : ["Name", "Last name","Email", "Date of creation", "Role"], target : "getUsers", edit : showUserModal},
                      {title : "Listings", headers : ["Name", "Price","Description", "Address", "Size"], target : "getUsers", createNew : showListingModal},
                      {title : "Links", headers : ["Title", "Access level","Link", "File location", "Location", "Parent", "Icon"], target : "getAllLinks", createNew : showLinkModal, edit : showLinkModal}
                    ];
        let table = document.querySelector("#element-table");
        let activeTable = 0;
        let html = "";
        let tabHolder = document.querySelector("#admin-tabs-holder");
        let active;
        //For each table, generate a button
        for(let table in tables){
            active = false;
            let id = table;
            let currTable = tables[id];
            if(table == activeTable) active = true;
            html += `<a href="#" data-id="${table}" class="btn ${active ? "btn-primary" : "btn-info"} admin-tab">${currTable["title"]}</a>`;
        }
        tabHolder.innerHTML = html;
        //To every button, add an event listener
        let adminTabs = document.querySelectorAll(".admin-tab");
        for(let tab of adminTabs){
            tab.addEventListener("click", function(){
                activeTable = this.dataset.id;
                generateTable(this.dataset.id);
                applyCurrentTab(this.dataset.id);
            })
        }
        generateTable();
        //Set up all modals
        setUpModals();
        //Generates the structure of a table
        function generateTable(){
            let tableId = activeTable;
            generateHeaderTableRow(table, tableId)
            let target = tables[tableId].target;
            //Makas an AJAX request and fills table with resulting information
            readAjax(target, fillTable);
        }
        function applyCurrentTab(tableId){
            activeTable = tableId;
            for(let tab of adminTabs){
                if(tab.dataset.id != tableId){
                    tab.classList.remove("btn-primary");
                    tab.classList.add("btn-info");
                }
                else{
                    tab.classList.add("btn-primary");
                    tab.classList.remove("btn-info");
                }
            }
        }
        function fillTable(data){
            let html = "";
            let counter = 1;
            let tableResultHolder = document.querySelector("#table-result-holder");
            currData = data;
            for(let row of data){
                html += 
                `
                <tr>
                <td>
                ${counter++}
                </td>
                `
                for(let i = 1; i < Object.keys(row).length / 2; i++){
                    html += 
                    `
                    <td>
                    ${row[i]}
                    </td>
                    `
                }
                html += 
                `
                <td>
                <button type="button" data-id="${row["id"]}" class="btn btn-light edit-button">Edit</button>
                <button type="button" data-table="${tables[activeTable].title}" data-id="${row["id"]}" class="btn btn-danger delete-button">Delete</button>
                </td>
                `
                
                html += `</tr>`;
                console.log(row);
            }

            //Code for generating the "Insert new" button;
            //if the table should show a create new buttno
            let createNew = tables[activeTable].createNew;
            let edit = tables[activeTable].edit;
            if(createNew)
            {
                html += 
                `
                <tr>
                <button type="button" class="btn btn-success new-button">Insert new</button>
                </tr>
                `
            }
            console.log(html);
            tableResultHolder.innerHTML = "";
            tableResultHolder.innerHTML += html;
            if(createNew){
                let newButton = document.querySelector(".new-button");
                newButton.addEventListener("click", function(){
                    createNew();
                })
            }
            if(edit){
                let editButtons = document.querySelectorAll(".edit-button");
                for(let button of editButtons){
                    button.addEventListener("click", function(){
                        let elemId = this.dataset.id;
                        edit(elemId);
                    })
                }
            }
            let deleteButtons = document.querySelectorAll(".delete-button");
            for(let button of deleteButtons){
                button.addEventListener("click", function(){
                    let tab = this.dataset.table;
                    let elemId = this.dataset.id;
                    adminDeleteRequest(tab, elemId);
                })
            }
        }
        function generateHeaderTableRow(table, tableId){
            let headerTableRow = document.querySelector("#header-table-row");
            let headers = tables[tableId].headers;
            let html = "";
            html += 
            `
            <th>
            #
            </th>`
            for(let header of headers){
                html += `
              <th>
                ${header}
              </th>`
            }
            html += 
            `
            <th>
            Options
            </th>
            `
            headerTableRow.innerHTML = html;
        }
        function adminDeleteRequest(table, elementId){
            let data = {table, id : elementId};
            console.log(data);
            submitAjax("deleteFromTable", showResult, data);
        }
        function showResult(data){
            generateTable();   
            closeCurrentModal();
        }
        function showUserModal(existingId = 0){
            let modal = document.querySelector("#user-modal");
            globalData.currModal = modal;
            let type = existingId ? "edit" : "create";

            //Select all fields
            let userNameField = document.querySelector("#userName");
            let userLastNameField = document.querySelector("#userLastName");
            let userEmailField = document.querySelector("#userEmail");
            let userRoleField = document.querySelector("#userRole");
            let userPasswordField = document.querySelector("#userPassword");
            let userIdField = document.querySelector("#userId");

            let elems = new Array(userEmailField, userNameField, userLastNameField, userRoleField, userPasswordField);

            //Remove success and error
            for(let elem of elems){
                removeError(elem);
                removeSuccess(elem);
            }

            if(type == "edit"){
                let data = {id : existingId};
                userIdField.value = existingId;
                submitAjax("getSpecificUser", function(data){
                    let firstRow = data[0];

                    userNameField.value = firstRow.name;
                    userLastNameField.value = firstRow.lastName;
                    userEmailField.value = firstRow.email;
                    userRoleField.value = firstRow.role_id;
                }, data);
            }
            else{
                //Not implemented nor intended
            }
            openModal(modal, globalData.modalBackground)
        }
        function showLinkModal(existingId = 0){
            let modal = document.querySelector("#link-modal");
            globalData.currModal = modal;
            let type = existingId ? "edit" : "create";
            //Select all fields
            let linkTitleField = document.querySelector("#LinkTitle");
            let LinkHrefField = document.querySelector("#LinkHref");
            let LinkIconField = document.querySelector("#LinkIcon");
            let accessLevelSelect = document.querySelector("#LinkReqLevel");
            let LinkLocation = document.querySelector("#LinkLocation");
            let LinkRoot = document.querySelector("#LinkRoot");
            let linkIdField = document.querySelector("#linkId");

            let linkModalTitle = document.querySelector("#link-modal-title");
            let modalSubmitButton = document.querySelector("#link-submit");

            
            let elems = new Array(linkTitleField, LinkHrefField, LinkIconField, accessLevelSelect, LinkLocation)

            //Remove success and error
            for(let elem of elems){
                removeError(elem);
                removeSuccess(elem);
            }
            if(type == "edit"){
                let data = {id : existingId};
                submitAjax("getSpecificLink", function(data){
                    let firstRow = data[0];

                    linkTitleField.value = firstRow.title;
                    LinkHrefField.value = firstRow.href;
                    LinkIconField.value = firstRow.icon ? firstRow.icon : "";
                    accessLevelSelect.value = firstRow.access_level_id;
                    LinkLocation.value = firstRow.location;
                    LinkRoot.checked = firstRow.landing;
                }, data);

                linkIdField.value = existingId;
                modalSubmitButton.innerText = "Edit link";
                linkModalTitle.innerText = `Edit existing link`;
            }
            else{
                linkTitleField.value = "";
                LinkHrefField.value = "";
                LinkIconField.value = "";
                accessLevelSelect.value = 0;
                LinkLocation.value = 0;
                LinkRoot.checked = false;

                modalSubmitButton.innerText = "Create new link";
                linkModalTitle.innerText = "Create a new link";

                linkIdField.value = existingId;
            }
            openModal(modal, globalData.modalBackground)
        }
        function showListingModal(existingId = 0){
            let modal = document.querySelector("#listing-modal");

            let type = existingId ? "edit" : "create";
            let listingIdField = document.querySelector("#listingId");

            listingIdField.value = existingId;

            if(type == "edit"){
            }

            globalData.currModal = modal;
            openModal(modal, globalData.modalBackground)
        }
        //Setup functions
        function setUpModals(){
            let cancelButtons = document.querySelectorAll(".close-button");
            for(let button of cancelButtons){
                button.addEventListener("click", closeCurrentModal);
            }
            setupLinkModal();
            setupUserModal();
            setupListingModal();
        }
        function setupUserModal(){
            let userRoleSelect = document.querySelector("#userRole"); 
            readAjax("getAllRoles", fillDropdown, [userRoleSelect]);

            let modalSubmitButton = document.querySelector("#user-submit");
            modalSubmitButton.addEventListener("click", function(e){
                e.preventDefault();
                submitUserForm();
            })
        }
        function setupListingModal(){
            let boroughSelect = document.querySelector("#listingBorough");
            let listingBuildingType = document.querySelector("#listingBuildingType");

            readAjax("getAllBoroughs", fillDropdown, [boroughSelect]);
            readAjax("getAllBuildingTypes", fillDropdown, [listingBuildingType]);

            let modalSubmitButton = document.querySelector("#listing-submit");
            modalSubmitButton.addEventListener("click", function(e){
                e.preventDefault();
                submitListingForm();
            })
        }
        function setupLinkModal(){
            let accessLevelSelect = document.querySelector("#LinkReqLevel");
            readAjax("getAllAccessLevels", fillDropdown, [accessLevelSelect]);

            let modalSubmitButton = document.querySelector("#link-submit");
            modalSubmitButton.addEventListener("click", function(e){
                e.preventDefault();
                submitLinkForm();
            })
        }
        function submitUserForm(){
            let userNameField = document.querySelector("#userName");
            let userLastNameField = document.querySelector("#userLastName");
            let userEmailField = document.querySelector("#userEmail");
            let userRoleField = document.querySelector("#userRole");
            let userPasswordField = document.querySelector("#userPassword");
            let userIdField = document.querySelector("#userId");

            let errors = 0;

            let reName = /^[A-Z][a-z]{1,14}(\s[A-Z][a-z]{1,14}){0,2}$/;

            let rePass1 = /[A-Z]/; 
            let rePass2 = /[a-z]/; 
            let rePass3 = /[0-9]/; 
            let rePass4 = /[!\?\.]/; 
            let rePass5 = /^[A-Za-z0-9!\?\.]{7,30}$/;

            let reEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

            let singleTests = [
                {re : reName, field: userNameField, message : `Name doesn't match format, eg "Nathan"`},
                {re : reName, field: userLastNameField, message : `Last name does not match format, eg "Smith"`},
                {re : reEmail, field: userEmailField, message : `Email not valid`},
            ]

            let passwordTests = [
                {re : rePass5, field: userPasswordField, message : `Password must be between 7 and 30 characters long`},
                {re : rePass1, field: userPasswordField, message : `Password must contain an uppercase letter`},
                {re : rePass2, field: userPasswordField, message : `Password must contain a lowercase letter`},
                {re : rePass3, field: userPasswordField, message : `Password must contain a digit`},
                {re : rePass4, field: userPasswordField, message : `Password must contain ! or ? or .`}
            ]

            for(let test of singleTests){
                errors += reTestText(test.re, test.field, test.message);
            }
            
            if(userPasswordField.value.length > 0){
                for(let test of passwordTests){
                    let result = reTestText(test.re, test.field, test.message);
                    errors += result;
    
                    if(result > 0) break;
                }
            }

            errors += testDropdown(userRoleField, 0, "You must select a role");

            if(userIdField.value < 1) errors++;

            if(errors != 0) return;

            data = {};

            target = "editUser";

            data.userId = userIdField.value;

            data.name = userNameField.value;
            data.lastName = userLastNameField.value;
            data.email = userEmailField.value;
            data.password = userPasswordField.value;
            data.roleId = userRoleField.value;

            submitAjax(target, showResult, data);
        }
        function submitListingForm(){
            let formData = new FormData();

            let listingTitleField = document.querySelector("#listingTitle");
            let listingDescriptionField = document.querySelector("#listingDescription");
            let listingAddressField = document.querySelector("#listingAddress");
            let listingSizeField = document.querySelector("#listingSize");
            let listingPriceField = document.querySelector("#listingPrice");
            let listingBoroughSelect = document.querySelector("#listingBorough");
            let listingBuildingTypeSelect = document.querySelector("#listingBuildingType");
            let listingPhotoField = document.querySelector("#listingPhoto");
            let listingIdField = document.querySelector("#listingId");

            let listingId = listingIdField.value;

            let type = listingId > 0 ? "edit" : "create";

            let target = "createNewListing";

            let errors;

            if(type == "edit"){
                formData.append("listingId", listingId);
                target = "editListing";
            }

            let reTitle = /^[A-Z][a-z]{2,15}(\s[A-Za-z][a-z]{2,15}){0,2}$/;
            let reAddress = /^(([A-Z][a-z\d]+)|([0-9][1-9]*\.?))(\s[A-Za-z\d]+){0,7}\s(([1-9][0-9]{0,5}[\/-]?[A-Z])|([1-9][0-9]{0,5})|(NN))\.?$/;
            let reDescription = /^[A-Z][a-z]{0,50}(\s[A-Za-z][a-z]{2,50})*$/;

            //tests

            if(reTestText(reTitle, listingTitleField, "Title does not match format")) errors++;

            if(reTestText(reDescription, listingDescriptionField, "Description does not match format")) errors++;

            if(reTestText(reAddress, listingAddressField, "Address does not match format")) errors++;

            if(testGeneric(listingSizeField, listingSizeField.value < 30, "Size cannot be below 30 feet")) errors++;

            if(testGeneric(listingPriceField, listingPriceField.value < 1000, "Price cannot be below 1000$")) errors++;

            if(testDropdown(listingBoroughSelect, 0, "You must select a borough")) errors++;

            if(testDropdown(listingBuildingTypeSelect, 0, "You must select a building type")) errors++;

            if(testImage(listingPhotoField)) errors++;

            

            //On success
            if(errors !== 0) return;
            formData.append("listingPhoto", listingPhotoField.files[0]);
            formData.append("listingTitle", listingTitleField.value);
            formData.append("listingDescription", listingDescriptionField.value);
            formData.append("listingAddress", listingSizeField.value);
            formData.append("listingSize", listingTitleField.value);
            formData.append("listingPrice", listingPriceField.value);
            formData.append("listingBorough", listingPriceField.value);
            formData.append("listingBuildingType", listingBuildingTypeSelect.value);

            // submitFormDataAjax(target, showResult, formData);
        }
        function submitLinkForm(){
            let LinkIdField = document.querySelector("#linkId");
            let LinkTitleField = document.querySelector("#LinkTitle");
            let LinkHrefField = document.querySelector("#LinkHref");
            let LinkIconField = document.querySelector("#LinkIcon");
            let accessLevelSelect = document.querySelector("#LinkReqLevel");
            let LinkLocationSelect = document.querySelector("#LinkLocation");
            let LinkRootCheck = document.querySelector("#LinkRoot");

            let linkId = LinkIdField.value;
            let linkTitle = LinkTitleField.value;
            let LinkHref = LinkHrefField.value;
            let LinkIcon = LinkIconField.value;
            let accessLevel = accessLevelSelect.value;
            let LinkLocation = LinkLocationSelect.value;
            let LinkRoot = LinkRootCheck.checked;

            let reTitle = /^[A-Z][a-z]{2,15}(\s[A-Za-z][a-z]{2,15}){0,2}$/;
            let reHref = /(^[a-z]{3,40}\.[a-z]{2,5}$)/;
            let reIcon = /(^$)|(^[a-z:-]{5,30}$)/;

            let submitType = linkId > 0 ? "edit" : "create";

            let data = {};

            let target = "createNewLink";

            if(submitType === "edit"){
                data.linkId = linkId;
                target = "editLink";
            }
            
            let errors = 0;

            if(reTestText(reTitle, LinkTitleField, "Title does not match format")) errors++;

            if(reTestText(reHref, LinkHrefField, "Link href does not match format")) errors++;

            if(reTestText(reIcon, LinkIconField, "Link icon does not match format")) errors++;

            if(testDropdown(accessLevelSelect, 0, "You must select an access level")) errors++;

            if(testDropdown(LinkLocationSelect, 0, "You must select a location")) errors++;

            console.log(errors);

            if(errors != 0){
                return;
            }
            
            data.title = linkTitle;
            data.href = LinkHref;
            data.icon = LinkIcon;
            data.aLevel = accessLevel;
            data.location = LinkLocation;
            data.main = LinkRoot;

            console.log("submitted");
            submitAjax(target, showResult, data);
        }
    }
}

function openModal(modal, modalBackground) {
    showElement(modalBackground, "hidden");
    showElement(modal, "hidden")
}
function closeModal(modal, modalBackground) {
    hideElement(modalBackground, "hidden");
    hideElement(modal, "hidden");
}

function closeCurrentModal(){
    hideElement(globalData.modalBackground, "hidden");
    hideElement(globalData.currModal, "hidden");
}

function fillDropdown(data, args){
    let selectElement = args[0];
    html = "";
    for(let row of data){
        html += `<option value="${row.id}">${row.title}</option>`;
    }
    selectElement.innerHTML += html;
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

function toggleShowElement(element){
    let hidden = element.classList.contains("hidden");
    if(hidden){
        showElement(element);
    }
    else{
        hideElement(element);
    }
}

function showElement(element, type="hide"){
    element.classList.remove(type);
}

function hideElement(element, type="hide"){
    element.classList.add(type);
}

function errorHandler(error){
    let errorHolder = document.querySelector("#error-holder");
    let errorMessage = document.createElement("span");
    errorMessage.classList.add("error-message");
    errorMessage.classList.add("alert");
    errorMessage.classList.add("alert-danger");
    errorMessage.innerText = error;
    errorHolder.append(errorMessage);
    // showElement(errorHolder);
    setTimeout(function(){
        hideElement(errorMessage);
    }, 2000);
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

function testGeneric(field, statement, errorMessage = ""){
    if(statement){
        removeSuccess(field);
        addError(field, errorMessage);
        return 1;
    }
    removeError(field);
    addSuccess(field);
    return 0;
}

function testNumericBounds(field, minimalValue, errorMessage = ""){
    let value = field.value;
    if(value < minimalValue){
        removeSuccess(field);
        addError(field, errorMessage);
        return 1;
    }
    removeError(field);
    addSuccess(field);
    return 0;
}

function testDropdown(field, negativeValue, errorMessage = ""){
    let value = field.value;
    //On success
    if(value != negativeValue){
        removeError(field);
        addSuccess(field);
        return 0;
    }
    //On fail
    else{
        removeSuccess(field);
        addError(field, errorMessage);
        return 1;
    }
}

function testImage(field, errorMessage = ""){
    let value = field.value;

    if(value == ""){
        removeSuccess(field);
        addError(field, errorMessage);
        return 1;
    }
    removeSuccess(field);
    addError(field, errorMessage);
}

function reTestText(regex, field, errorMessage = ""){
    let textValue = field.value;
    let passes = regex.test(textValue);
    if(passes){
        if(errorMessage != ""){
            removeError(field);
            addSuccess(field);
        }
        return 0;
    }
    else{
        if(errorMessage != ""){
            removeSuccess(field);
            addError(field, errorMessage);
        }
        return 1;
    }
}

function addSuccess(field){
    field.classList.add("success-outline");
}

function removeError(field){
    let errorBox = field.nextElementSibling;
    errorBox.innerText = "";
    errorBox.classList.add("hidden");
    field.classList.remove("error-outline");
}

function removeSuccess(field){
    field.classList.remove("success-outline");
}

function addError(field, msg = ""){
    let errorBox = field.nextElementSibling;
    errorBox.innerText = msg;
    errorBox.classList.remove("hidden");
    field.classList.add("error-outline");
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
                    console.log(data.general);
                    resultFunction(data.general);
                }
            }
            else if(request.status >= 300 && request.status < 400){
                redirect(args);
            }
            else{
                console.log(request.responseText);
                let data = JSON.parse(request.responseText);
                errorHandler(data["error"]);
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
                errorHandler(data["error"]);
                console.log(data["error"]);
            }
        }
    }

    request.open("POST", ajaxPath+url+".php");
    request.setRequestHeader("Content-type", "application/json");
    console.log(data);
    request.send(JSON.stringify(data));
}

function submitFormDataAjax(url, resultFunction, data, args = []){
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
                errorHandler(data["error"]);
                console.log(data["error"]);
            }
        }
    }

    request.open("POST", ajaxPath+url+".php");
    console.log(data);
    request.send(data);
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