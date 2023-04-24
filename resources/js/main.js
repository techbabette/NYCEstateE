let url = window.location.href.split("/");
let lastOfUrl = url[url.length - 1];
let currentPage = lastOfUrl.split("?")[0].toLowerCase().replace(/[^A-Za-z0-9\.]/gi, "");
let mainPage = false;
let ajaxPath = "BusinessLogic/";
let data;
let globalData = {};
let success;
if (currentPage == "" || currentPage == "index.html") mainPage = true;
if (!mainPage) ajaxPath = "../BusinessLogic/";

window.onload = function(){
    data = {currentPage};

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
                      {title : "Listings", headers : ["Name", "Price","Description", "Address", "Size"], target : "getAllListings", createNew : showListingModal, edit: showListingModal},
                      {title : "Links", headers : ["Title", "Access level","Link", "File location", "Location",  "Priority", "Parent", "Icon"], target : "getAllLinks", createNew : showLinkModal, edit : showLinkModal},
                      {title : "Boroughs", headers : ["Title", "Number of listings (Both active and deleted)"], target : "getAllBoroughsCount", createNew: showBoroughModal, edit: showBoroughModal},
                      {title : "Building types", headers : ["Title", "Number of listings (Both active and deleted)"], target : "getAllBuildingTypesCount", createNew: showBuildingTypeModal, edit: showBuildingTypeModal},
                      {title : "Survey questions", headers : ["Title", "Number of answers"], target : "getAllQuestions", createNew : showQuestionModal, edit: showQuestionModal},
                      {title : "Room types", headers : ["Title"], target : "getAllRoomTypes", createNew: showRoomTypeModal, edit: showRoomTypeModal},
                      {title : "Deleted listings", headers : ["Name", "Price","Description", "Address", "Size"], target : "getAllDeletedListings", edit: showListingModal, restore : "restoreListing"},
                    ];
        let table = document.querySelector("#element-table");
        let activeTable = 0;
        let html = "";
        let tabHolder = document.querySelector("#admin-tabs-holder");
        let active;
        globalData.rooms = new Array();
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
                `
                if(tables[activeTable].restore){
                    html += `<button type="button" data-table="${tables[activeTable].title}" data-id="${row["id"]}" class="btn btn-success restore-button">Restore</button>`
                }
                else{
                    html += `<button type="button" data-table="${tables[activeTable].title}" data-id="${row["id"]}" class="btn btn-danger delete-button">Delete</button>`
                }
                html += `</td></tr>`;
                console.log(row);
            }

            //Code for generating the "Insert new" button;
            //if the table should show a create new buttno
            let createNew = tables[activeTable].createNew;
            let edit = tables[activeTable].edit;
            let restore = tables[activeTable].restore;
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
                    button.addEventListener("click", function(e){
                        e.preventDefault();
                        let elemId = this.dataset.id;
                        edit(elemId);
                    })
                }
            }
            if(restore)
            {
                let restoreButtons = document.querySelectorAll(".restore-button");
                for(let button of restoreButtons){
                    addEventListenerOnce("click", button, function(e){
                        e.preventDefault();
                        let elemId = this.dataset.id;
                        let data = {id : elemId};
                        submitAjax(restore, showResult, data, {closeModal : false});
                    });
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
            submitAjax("deleteFromTable", showResult, data, {closeModal : false});
        }
        function showResult(data, args){
            generateTable();   
            let messageHolder = document.querySelector("#error-holder");
            let displayMessage = true;
            if(displayMessage){
                let message = document.createElement("span");
                message.classList.add("error-message");
                message.classList.add("alert");
                message.classList.add("alert-success");
                message.innerText = data;
                messageHolder.append(message);
                setTimeout(function(){
                    hideElement(message);
                }, 2000);
            }
            if(args.closeModal){
                closeCurrentModal();
            };
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
            let LinkPriorityField = document.querySelector("#linkPriority");

            let linkModalTitle = document.querySelector("#link-modal-title");
            let modalSubmitButton = document.querySelector("#link-submit");

            
            let elems = new Array(linkTitleField, LinkHrefField, LinkIconField, accessLevelSelect, LinkLocation, LinkPriorityField);

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
                    LinkPriorityField.value = firstRow.priority;
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
        function showBoroughModal(existingId = 0){
            let modal = document.querySelector("#borough-modal");

            let type = existingId ? "edit" : "create";

            let boroughNameField = document.querySelector("#boroughName");

            let boroughIdField = document.querySelector("#boroughId");

            boroughNameField.value = "";
            boroughIdField.value = existingId;

            let boroughTitle = document.querySelector("#borough-modal-title");
            let boroughSubmitButton = document.querySelector("#borough-submit");

            if(type == "edit"){
                let data = {id : existingId};
                boroughTitle.innerText = "Edit borough";
                boroughSubmitButton.innerText = "Edit borough";
                submitAjax("getSpecificBorough", function(data){
                    console.log(data.title);
                    boroughNameField.value = data.title;
                }, data);
            }
            else{
                boroughTitle.innerText = "Create new borough";
                boroughSubmitButton.innerText = "Create borough";
            }
            


            let elems = new Array(boroughNameField);

            for(let elem of elems){
                removeError(elem);
                removeSuccess(elem);
            }

            globalData.currModal = modal;
            openModal(modal, globalData.modalBackground);
        }
        function showBuildingTypeModal(existingId = 0){
            let modal = document.querySelector("#buildingType-modal");

            let type = existingId ? "edit" : "create";

            let buildingTypeNameField = document.querySelector("#buildingTypeName");

            let buildingTypeId = document.querySelector("#buildingTypeId");

            buildingTypeNameField.value = "";
            buildingTypeId.value = existingId;

            let buildingTypeTitle = document.querySelector("#buildingType-modal-title");
            let buildingTypeSubmitButton = document.querySelector("#buildingType-submit");

            if(type == "edit"){
                let data = {id : existingId};
                buildingTypeTitle.innerText = "Edit building type";
                buildingTypeSubmitButton.innerText = "Edit building type";
                submitAjax("getSpecificBuildingType", function(data){
                    buildingTypeNameField.value = data.title;
                }, data);
            }
            else{
                buildingTypeTitle.innerText = "Create new building type";
                buildingTypeSubmitButton.innerText = "Create building type";
            }
            


            let elems = new Array(buildingTypeNameField);

            for(let elem of elems){
                removeError(elem);
                removeSuccess(elem);
            }

            globalData.currModal = modal;
            openModal(modal, globalData.modalBackground);
        }
        function showRoomTypeModal(existingId = 0){
            let modal = document.querySelector("#roomType-modal");

            let type = existingId ? "edit" : "create";

            let roomTypeNameField = document.querySelector("#roomTypeName");

            let roomTypeId = document.querySelector("#roomTypeId");

            roomTypeNameField.value = "";
            roomTypeId.value = existingId;

            let roomTypeTitle = document.querySelector("#roomType-modal-title");
            let roomTypeSubmitButton = document.querySelector("#roomType-submit");

            if(type == "edit"){
                let data = {id : existingId};
                roomTypeTitle.innerText = "Edit room type";
                roomTypeSubmitButton.innerText = "Edit room type";
                submitAjax("getSpecificRoomType", function(data){
                    roomTypeNameField.value = data.title;
                }, data);
            }
            else{
                roomTypeTitle.innerText = "Create new room type";
                roomTypeSubmitButton.innerText = "Create room type";
            }
            


            let elems = new Array(roomTypeNameField);

            for(let elem of elems){
                removeError(elem);
                removeSuccess(elem);
            }

            globalData.currModal = modal;
            openModal(modal, globalData.modalBackground);
        }
        function showListingModal(existingId = 0){
            let modal = document.querySelector("#listing-modal");

            let type = existingId ? "edit" : "create";

            let listingTitleField = document.querySelector("#listingTitle");
            let listingDescriptionField = document.querySelector("#listingDescription");
            let listingAddressField = document.querySelector("#listingAddress");
            let listingSizeField = document.querySelector("#listingSize");
            let listingPriceField = document.querySelector("#listingPrice");
            let listingBoroughSelect = document.querySelector("#listingBorough");
            let listingBuildingTypeSelect = document.querySelector("#listingBuildingType");
            let listingPhotoField = document.querySelector("#listingPhoto");
            let listingIdField = document.querySelector("#listingId");
            let listingImagePreview = document.querySelector("#main-photo-preview");

            let listingModalTitle = document.querySelector("#listing-modal-title");
            let modalSubmitButton = document.querySelector("#listing-submit");

            let elems = new Array(listingTitleField, listingDescriptionField, listingAddressField, listingSizeField, listingPriceField, listingBoroughSelect, listingBuildingTypeSelect, listingPhotoField);

            for(let elem of elems){
                removeError(elem);
                removeSuccess(elem);
            }

            listingIdField.value = existingId;
            removeAllRooms();
            listingImagePreview.src = "";

            if(type == "edit"){
                let data = {id : existingId};
                submitAjax("getSpecificListing", function(data){
                    console.log(data);
                    let core = data.main;
                    let photo = data.photo;
                    let rooms = data.rooms;
                    listingTitleField.value = core.listing_name;
                    listingAddressField.value = core.address;
                    listingDescriptionField.value = core.description;
                    listingSizeField.value = core.size; 
                    listingPriceField.value = core.price;
                    listingBoroughSelect.value = core.borough_id;
                    listingBuildingTypeSelect.value = core.building_type_id; 
                    listingImagePreview.src = `../resources/imgs/${photo.path}`;
                    
                    globalData.startingRooms = new Array();
                    for(let room of rooms){
                        addRoom(room.room_type_id, room.room_name, room.numberOf);
                        globalData.startingRooms.push({roomId : room.room_type_id, count : room.numberOf});
                    }
                }, data);
                listingModalTitle.innerText = "Edit listing";
                modalSubmitButton.innerText = "Edit listing";
            }
            else{
                listingTitleField.value = "";
                listingAddressField.value = "";
                listingDescriptionField.value = "";
                listingSizeField.value = 30; 
                listingPriceField.value = 1000;
                listingBoroughSelect.value = 0;
                listingBuildingTypeSelect.value = 0; 
                listingPhotoField.value = "";
                listingModalTitle.innerText = "Create new listing";
                modalSubmitButton.innerText = "Create listing";
            }

            globalData.currModal = modal;
            openModal(modal, globalData.modalBackground);
        }
        function showQuestionModal(existingId = 0){
            let modal = document.querySelector("#question-modal");

            let answerHolder = document.querySelector("#question-answer-holder");

            let type = existingId ? "edit" : "create";

            globalData.numberOfAnswers = 1;

            answerHolder.innerHTML = "";

            globalData.currModal = modal;
            openModal(modal, globalData.modalBackground);
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
            setupBoroughModal();
            setupBuildingTypeModal();
            setupRoomTypeModal();
            setupQuestionModal();
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
            let listingRoomsList = document.querySelector("#listingRoomsList");

            readAjax("getAllBoroughs", fillDropdown, [boroughSelect]);
            readAjax("getAllBuildingTypes", fillDropdown, [listingBuildingType]);
            readAjax("getAllRoomTypes", fillDropdown, [listingRoomsList]);
            

            let fileReader = new FileReader();
            let previewHolder = document.querySelector("#main-photo-preview");
            fileReader.onload = function (e) {previewHolder.src = this.result;}
            let listingPhotoField = document.querySelector("#listingPhoto");
            listingPhotoField.addEventListener("change", function(){
                fileReader.readAsDataURL(listingPhotoField.files[0]);
            })

            let addListingRoomButton = document.querySelector("#addListingRoomButton");
            addListingRoomButton.addEventListener("click", function(e){
                e.preventDefault();
                let roomId = listingRoomsList.options[listingRoomsList.selectedIndex].value;
                let roomText = listingRoomsList.options[listingRoomsList.selectedIndex].text;
                if(roomId==0){
                    addError(listingRoomsList, "Must select a room before adding");
                    removeSuccess(listingRoomsList);
                    return;
                }
                removeError(listingRoomsList);
                addRoom(roomId, roomText);
            });

            let modalSubmitButton = document.querySelector("#listing-submit");
            addEventListenerOnce("click", modalSubmitButton, function(e){
                e.preventDefault();
                submitListingForm();
            });
        }
        function setupLinkModal(){
            let accessLevelSelect = document.querySelector("#LinkReqLevel");
            readAjax("getAllAccessLevels", fillDropdown, [accessLevelSelect]);

            let locationSelect = document.querySelector("#LinkLocation");
            readAjax("getAllNavigationLocations", fillDropdown, [locationSelect, true]);

            let modalSubmitButton = document.querySelector("#link-submit");
            addEventListenerOnce("click", modalSubmitButton, function(e){
                e.preventDefault();
                submitLinkForm();
            });
        }
        function setupBoroughModal(){
            let modalSubmitButton = document.querySelector("#borough-submit");
            addEventListenerOnce("click", modalSubmitButton, function(e){
                e.preventDefault();
                submitBoroughForm();
            });
        }
        function setupBuildingTypeModal(){
            let modalSubmitButton = document.querySelector("#buildingType-submit");
            addEventListenerOnce("click", modalSubmitButton, function(e){
                e.preventDefault();
                submitBuildingTypeForm();
            })
        }
        function setupRoomTypeModal(){
            let modalSubmitButton = document.querySelector("#roomType-submit");
            addEventListenerOnce("click", modalSubmitButton, function(e){
                e.preventDefault();
                submitRoomTypeForm();
            })
        }
        function setupQuestionModal(){
            let questionAddAnswerButton = document.querySelector("#questionAddAnswer");
            addEventListenerOnce("click", questionAddAnswerButton, function(e){
                e.preventDefault();
                addAnswer(globalData.numberOfAnswers++, "");
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

            submitAjax(target, showResult, data, {closeModal : true});
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

            let errors = 0;

            if(type == "edit"){
                formData.append("listingId", listingId);
                target = "editListing";
            }

            let reTitle = /^[A-Z][a-z]{2,15}(\s[A-Za-z][a-z]{2,15}){0,2}$/;
            let reAddress = /^(([A-Z][a-z\d']+)|([0-9][1-9]*\.?))(\s[A-Za-z\d][a-z\d']+){0,7}\s(([1-9][0-9]{0,5}[\/-]?[A-Z])|([1-9][0-9]{0,5})|(NN))\.?$/;
            let reDescription = /^[A-Z][a-z']{0,50}(\s[A-Za-z][a-z']{0,50})*$/;

            //tests

            if(reTestText(reTitle, listingTitleField, "Title does not match format")) errors++;

            if(reTestText(reDescription, listingDescriptionField, "Description does not match format")) errors++;

            if(reTestText(reAddress, listingAddressField, "Address does not match format")) errors++;

            if(testGeneric(listingSizeField, listingSizeField.value < 30, "Size cannot be below 30 feet")) errors++;

            if(testGeneric(listingSizeField, listingSizeField.value > 100000, "Size cannot be above 100000 feet")) errors++;

            if(testGeneric(listingPriceField, listingPriceField.value < 1000, "Price cannot be below 1000$")) errors++;

            if(testGeneric(listingPriceField, listingPriceField.value > 1000000000, "Price cannot be above 1000000000$")) errors++;

            if(testDropdown(listingBoroughSelect, 0, "You must select a borough")) errors++;

            if(testDropdown(listingBuildingTypeSelect, 0, "You must select a building type")) errors++;

            if(type === "create"){
                if(testImage(listingPhotoField)) errors++;
            }
            let roomSelects = document.querySelectorAll(".listingRoom");
            let arrayOfRooms = new Array();
            for(let roomElement of roomSelects){
                if(testGeneric(roomElement, roomElement.value < 1)) {
                    errors++;
                    continue;
                }
                arrayOfRooms.push({roomId : parseInt(roomElement.dataset.id), count : parseInt(roomElement.value)});
            }

            let listingRooms = JSON.stringify(arrayOfRooms);

            console.log(listingRooms);

            console.log(`Step one, errors = ${errors}`);

            //On success
            if(errors !== 0) return;

            if(listingPhotoField.value != ""){
                formData.append("listingPhoto", listingPhotoField.files[0]);
            }
            formData.append("listingTitle", listingTitleField.value);
            formData.append("listingDescription", listingDescriptionField.value);
            formData.append("listingAddress", listingAddressField.value);
            formData.append("listingSize", listingSizeField.value);
            formData.append("listingPrice", listingPriceField.value);
            formData.append("listingBorough", listingBoroughSelect.value);
            formData.append("listingBuildingType", listingBuildingTypeSelect.value);

            if(arrayOfRooms.length > 0){
                formData.append("listingRooms", listingRooms);
            }
            console.log("Step two");

            submitFormDataAjax(target, showResult, formData, {closeModal : true});
        }
        function submitLinkForm(){
            let LinkIdField = document.querySelector("#linkId");
            let LinkTitleField = document.querySelector("#LinkTitle");
            let LinkHrefField = document.querySelector("#LinkHref");
            let LinkIconField = document.querySelector("#LinkIcon");
            let accessLevelSelect = document.querySelector("#LinkReqLevel");
            let LinkLocationSelect = document.querySelector("#LinkLocation");
            let LinkPriorityField = document.querySelector("#linkPriority");
            let LinkRootCheck = document.querySelector("#LinkRoot");
            

            let linkId = LinkIdField.value;
            let linkTitle = LinkTitleField.value;
            let LinkHref = LinkHrefField.value;
            let LinkIcon = LinkIconField.value;
            let accessLevel = accessLevelSelect.value;
            let LinkLocation = LinkLocationSelect.value;
            let LinkRoot = LinkRootCheck.checked;
            let linkPriority = LinkPriorityField.value;

            let reTitle = /^[A-Z][a-z]{2,15}(\s[A-Za-z][a-z]{2,15}){0,2}$/;
            let reHref = /(https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[-a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&\/=]*))||(^[a-z]{3,40}\.[a-z]{2,5}$)/;
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

            if(testGeneric(LinkPriorityField, linkPriority < 1, "Link priority cannot be lower than 1")) errors++;

            if(testGeneric(LinkPriorityField, linkPriority > 99, "Link priority cannot be higher than 99")) errors++;

            if(errors != 0){
                return;
            }
            
            data.title = linkTitle;
            data.href = LinkHref;
            data.icon = LinkIcon;
            data.aLevel = accessLevel;
            data.location = LinkLocation;
            data.main = LinkRoot;
            data.priority = linkPriority;

            submitAjax(target, showResult, data, {closeModal : true});
        }
        function submitBoroughForm(){
            let boroughNameField = document.querySelector("#boroughName");
            let boroughIdField = document.querySelector("#boroughId");

            let boroughId = boroughIdField.value;

            let reBoroughName = /^[A-Z][a-z']{1,50}(\s[A-Za-z][a-z']{1,50}){0,3}$/;

            let errors = 0;

            let type = boroughId > 0 ? "edit" : "create";

            let target = "createNewBorough";

            let data = {};

            if(type == "edit"){
                target = "editBorough";
                data.id = boroughId;
            }

            if(reTestText(reBoroughName, boroughNameField, `Borough name does not match format, eg "The Queens"`)) errors ++;

            if(errors != 0){
                return;
            }

            data.boroughName = boroughNameField.value;

            submitAjax(target, showResult, data, {closeModal : true});
            // submitAjax(target, callMultipleFunctions, data, [showResult, setupListingModal]);
        }
        function submitBuildingTypeForm(){
            let buildingTypeNameField = document.querySelector("#buildingTypeName");
            let buildingTypeIdField = document.querySelector("#buildingTypeId");

            let buildingTypeId = buildingTypeIdField.value;

            let reBuildingTypeName = /^[A-Z][a-z']{1,50}(\s[A-Za-z][a-z']{1,50}){0,3}$/;

            let errors = 0;

            let type = buildingTypeId > 0 ? "edit" : "create";

            let target = "createNewBuildingType";

            let data = {};

            if(type == "edit"){
                target = "editBuildingType";
                data.id = buildingTypeId;
            }

            if(reTestText(reBuildingTypeName, buildingTypeNameField, `Building type name does not match format, eg "The Queens"`)) errors ++;

            if(errors != 0){
                return;
            }

            data.buildingTypeName = buildingTypeNameField.value;

            submitAjax(target, showResult, data, {closeModal : true});
        }
        function submitRoomTypeForm(){
            let roomTypeNameField = document.querySelector("#roomTypeName");
            let roomTypeIdField = document.querySelector("#roomTypeId");

            let roomTypeId = roomTypeIdField.value;

            let reRoomTypeName = /^[A-Z][a-z']{1,50}(\s[A-Za-z][a-z']{1,50}){0,3}$/;

            let errors = 0;

            let type = roomTypeId > 0 ? "edit" : "create";

            let target = "createNewRoomType";

            let data = {};

            if(type == "edit"){
                target = "editRoomType";
                data.id = roomTypeId;
            }

            if(reTestText(reRoomTypeName, roomTypeNameField, `Room type name does not match format, eg "The Queens"`)) errors ++;

            if(errors != 0){
                return;
            }

            data.roomTypeName = roomTypeNameField.value;

            submitAjax(target, showResult, data, {closeModal : true});
        }
        function addAnswer(num, answerText, answerId = 0){
            let answerHolder = document.querySelector("#question-answer-holder");
            let newAnswerHolder = document.createElement("div");

            let html = 
            `
            <label for="answer${num}" class="d-block">Answer ${num}</label>
            <input type="text" value="${answerText}" class="form-control d-inline questionAnswer w-50" data-id="${answerId}" name="answer${num}" id="answer${num}">
            <button class="btn btn-danger d-inline removeAnswer">Remove</button>
            `
            newAnswerHolder.innerHTML += html;
            answerHolder.appendChild(newAnswerHolder);
            let removeButtons = document.querySelectorAll("removeAnswer");

            for(let elem of removeButtons){
                addEventListenerOnce("click", elem, function(e){
                    e.preventDefault();
                    let parentElement = this.parentElement;
                    parentElement.remove;
                })
            }
        }
        function addRoom(roomId, roomText, count = 1){
            let html = "";
            let roomHolder = document.querySelector("#room-holder");
            let existingInputField = document.querySelector(`#room${roomId}`);
            if(existingInputField){
                let currValue = existingInputField.value;
                existingInputField.value = parseInt(currValue) + 1;
                return;
            }
            let newRoomHolder = document.createElement("div");
            html += 
            `<label for="room${roomId}" class="d-block">${roomText}</label>
            <input type="number" value="${count}" min="1" class="form-control d-inline listingRoom w-50" data-id="${roomId}" name="listingRoom${roomId}" id="room${roomId}">
            <button class="btn btn-danger d-inline removeRoom" id="removeButton${roomId}">Remove</button>`
            newRoomHolder.innerHTML += html;
            roomHolder.appendChild(newRoomHolder);
            let removeButtons = document.querySelectorAll(`.removeRoom`);
            for(let elem of removeButtons){
                addRemoveParentOnClickListener(elem);
            }
        }
        function removeAllRooms(){
            let roomHolder = document.querySelector("#room-holder");
            roomHolder.innerHTML = "";
        }
        function addEventListenerOnce(event, element, onEvent, listenerMark = ""){
            let listenerMarker = event + listenerMark;

            if(element.classList.contains(listenerMarker)){
                return;
            }

            element.classList.add(listenerMarker);
            element.addEventListener(event, onEvent);
        }
        function addRemoveParentOnClickListener(element){
            element.addEventListener("click", function(e){
            e.preventDefault();
            let parentElement = this.parentElement;
            parentElement.remove();
        });
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
    let simple = args[1];
    let value;
    let title;
    html = "";
    for(let row of data){
        if(simple){
            value = row;
            title = row;
        }
        else{
            value = row.id;
            title = row.title;
        }
        let firstLetter = title.charAt(0);

        let firstLetterCap = firstLetter.toUpperCase();

        let remainingLetters = title.slice(1);

        title = firstLetterCap + remainingLetters;

        html += `<option value="${value}">${title}</option>`;
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

function callMultipleFunctions(data, functions){
    let firstFunction = functions[0];
    firstFunction(data);
    for(let i = 1; i < functions.length; i++){
        let currFunc = functions[i];
        currFunc();
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
    removeError(field);
    addSuccess(field);
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
    if(msg != ""){
        let errorBox = field.nextElementSibling;
        errorBox.innerText = msg;
        errorBox.classList.remove("hidden");
    }
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