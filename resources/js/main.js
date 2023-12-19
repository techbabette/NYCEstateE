// let url = window.location.href.split("/");
// let lastOfUrl = url[url.length - 1];
// let currentPage = lastOfUrl.split("?")[0].toLowerCase().replace(/[^A-Za-z0-9\.]/gi, "");
let mainPage = false;
let ajaxPath = "models/";
let data;
let globalData = {};
let success;
let queryString = window.location.search;
let urlParams = new URLSearchParams(queryString);
let currentPage = urlParams.get("page") ? urlParams.get("page") : "index.html";
let firstSearch = window.location.search.replace("?page=", "");
firstSearch = firstSearch ? firstSearch : "index.html";
let canUseRouter = window.history ? true : false;
let prefix = "index.php?page="

mainPage = true;
if (!mainPage) ajaxPath = "../models/";

window.onload = function(){
    getLinks(firstSearch);
    prepareJavascript();
}

if(canUseRouter);
window.onpopstate = function(e){
    if(e.state){
        document.querySelector("#router-view").innerHTML = e.state.html;
        document.title = e.state.title;
        removeActiveFromAllOtherLinks();
        addActiveToLinkThatContains(e.state.page);
        prepareJavascript();
    }
};

function getLinks(urlPage){
    requestedPage = urlPage.split("&")[0];

    data = {currentPage : requestedPage};
    submitAjax("links/getLinks", function(response){
                generateNavbar(response);
    }, data, {redirectOnNotAllowed : true, newLocation : "index.html", landing : true});
}

function changePage(urlPage){
    requestedPage = urlPage.split("&")[0];

    let requestPath = "./views/pages/" + requestedPage;

    data = {currentPage : requestedPage};

    submitAjax("links/getLinks", function(response){
        var request = createRequest();
        request.onreadystatechange = function() {
        if (request.readyState == 4)
            if(request.status >= 200 && request.status < 300){
                loadPage(request.responseText, urlPage, requestedPage);
                generateNavbar(response);
            }
            else{
                changeUrl("index.html");
            }
        };
        request.open("GET", requestPath);
        request.send();

    }, data, { newLocation : "index.html", landing : true, redirectOnNotAllowed : true});

}

function loadPage(data, newUrl, page){
    let routerView = document.querySelector("#router-view");

    routerView.innerHTML = data;

    let title = newUrl.split(".")[0]

    title = title.charAt(0).toUpperCase() + title.slice(1);

    newUrl = `index.php?page=${newUrl}`;

    document.title = title;
    
    if(canUseRouter);
    window.history.pushState({html : data, title : title, page : page}, ``, newUrl);

    prepareJavascript();
}

function changeUrl(newPage){
    let requestedPageParts = newPage.split("/");

    newPage = requestedPageParts[requestedPageParts.length - 1];


    newPage =  newPage.split(/=(.*)/s)[1];
    
    //Do not change page if already on page
    if(newPage == currentPage) return;
    changePage(newPage);
}

function prepareJavascript(){
    let queryString = window.location.search;

    let urlParams = new URLSearchParams(queryString);

    currentPage = urlParams.get("page") ? urlParams.get("page") : "index.html";

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
            {re : rePass5, field: passwordField, message : `Password must be between 7 and 30 characters long, contain an uppercase letter, a lowercase letter, a digit, and ! or ? or .`},
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
                submitAjax("users/createNewUser", redirectSuccess, data, {newLocation : "login.html", "landing" : false});
            }
        })
    }
    if(currentPage == "login.html"){
        let loginForm = document.querySelector("#loginForm");

        let queryString = window.location.search;

        let urlParams = new URLSearchParams(queryString);

        let activationLink = urlParams.get("activation");

        if(activationLink){
            submitAjax("users/activateUser", redirectSuccess, {activationLink : activationLink}, { newLocation : "index.html", landing : true});
        }

        loginForm.addEventListener("submit", function(e){
            e.preventDefault();

            let emailField = document.querySelector("#emailInput");
            let passwordField = document.querySelector("#loginPasswordInput");

            let email = emailField.value;
            let password = passwordField.value;

            //Check if data is valid
            let errors = 0;

            // let rePass1 = /[A-Z]/; 
            // let rePass2 = /[a-z]/; 
            // let rePass3 = /[0-9]/; 
            // let rePass4 = /[!\?\.]/; 
            // let rePass5 = /^[A-Za-z0-9!\?\.]{7,30}$/;

            let reEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

            errors += reTestText(reEmail, emailField, "");


            //Disable checks for password format
            // errors += reTestText(rePass1, passwordField, "");
            // errors += reTestText(rePass2, passwordField, "");
            // errors += reTestText(rePass3, passwordField, "");
            // errors += reTestText(rePass4, passwordField, "");
            // errors += reTestText(rePass5, passwordField, "");

            let data = {"attemptLogin" : true, "email" : email, "pass" : password};

            if(errors == 0){
                submitAjax("users/attemptLogin", redirectSuccess, data, { newLocation : "index.html", landing : true});
            }
            else{
                errorHandler("Email does not match format");
            }
        })
    }
    if(currentPage === "admin.html"){
        let sortType = -1;
        globalData.modalBackground = document.querySelector(".mk-modal");
        window.addEventListener("click", function(e){
            if(e.target === globalData.modalBackground){
                closeCurrentModal();
            }
        })
        let tables = [
                    {title : "All page visits", headers : 
                    [
                    {Name : "Page name", Key : "page"},
                    {Name : "Time of visit", Key : "timeOfVisit", Sort : {Desc : 0, Asc : 1}},
                    {Name : "Email", Key : "email"},
                    {Name : "Role", Key : "role"}
                    ], target : "activities/getIndividualPageVisits", defaultSort : {Header: 1, Position : "Desc"}, delete : false, paginate : true},
                    {title : "Page visits %", headers :
                    [
                    {Name : "Page name", Key : "name", Sort : {Desc : 2, Asc : 3}},
                    {Name : "Percentage of visits", Key : "number", Sort : {Desc : 0, Asc : 1}, Suffix : "%"}
                    ], target : "activities/getPageVisitsPercent", defaultSort : {Header: 1, Position : "Desc"}, delete : false, paginate : true},
                    {title : "Page visits last 24h", headers :
                    [
                    {Name : "Page name", Key : "name", Sort : {Desc : 2, Asc : 3}},
                    {Name : "Number of visits", Key : "number", Sort : {Desc : 0, Asc : 1}}
                    ], target : "activities/getPageVisitsLastDay", defaultSort : {Header: 1, Position : "Desc"}, delete : false, paginate : true},
                    {title : "Users", headers : 
                    [
                    {Name : "Name", Key : "name", Sort : {Desc : 0, Asc : 1}},
                    {Name : "Last name", Key : "lastName", Sort : {Desc : 2, Asc : 3}},
                    {Name : "Email", Key : "email", Sort : {Desc : 8, Asc : 9}}, 
                    {Name : "Date of creation", Key : "dateCreated", Sort : {Desc : 4, Asc : 5}}, 
                    {Name : "Role", Key : "role_name", Sort : {Desc : 6, Asc : 7}}
                    ], target : "users/getAllUsers", edit : showUserModal, delete : true, defaultSort : {Header : "Date of creation", Position : "Desc", delete : true }, paginate : true},
                    {title : "Messages", headers : 
                    [
                    {Name : "Sender", Key : "email", Sort : {Desc : 0, Asc : 1}},
                    {Name : "Type", Key : "message_type_name", Sort : {Desc : 2, Asc : 3}},
                    {Name : "Title", Key : "title", Sort : {Desc : 4, Asc : 5}}, 
                    {Name : "Body", Key : "message", Sort : {Desc : 6, Asc : 7}},
                    {Name :"Date sent", Key : "dateCreated", Sort : {Desc : 8, Asc : 9}}
                    ], target : "messages/getAllMessages", defaultSort : {Header : 4, Position : "Desc"}, delete : true, paginate : true},
                    {title : "Message types", headers : 
                    [
                    {Name : "Title", Key : "title", Sort : {Desc : 0, Asc : 1}},
                    {Name : "Number of messages", Key : "Count", Sort : {Desc : 2, Asc : 3}}
                    ], target : "messagetypes/getAllMessageTypesCount", edit : showMessageTypeModal, createNew : showMessageTypeModal, delete : true, defaultSort : {Header : 1, Position : "Desc"}, paginate : true},
                    {title : "Listings", headers : 
                    [
                    {Name : "Name", Key : "listing_name", Sort : {Desc : 0, Asc : 1} }, 
                    {Name : "Price", Key : "price", Suffix : "$", Sort : {Desc : 2, Asc : 3}},
                    {Name :"Description", Key : "description", Sort : {Desc : 4, Asc : 5}}, 
                    {Name : "Borough", Key : "borough_name", Sort : {Desc : 6, Asc : 7}}, 
                    {Name :"Building type", Key : "type_name", Sort : {Desc : 8, Asc : 9}},
                    {Name : "Address", Key : "address", Sort : {Desc : 10, Asc : 11}}, 
                    {Name :"Size", Key : "size", Suffix : " feet", Sort : {Desc : 12, Asc : 13}}
                    ], target : "listings/getAllListings", createNew : showListingModal, edit: showListingModal, delete : true, defaultSort : {Header : 0, Position : "Desc"}, paginate : true},
                    {title : "Links", headers : 
                    [
                    {Name : "Title", Key : "link_title", Sort : {Desc : 0, Asc : 1}}, 
                    {Name : "Access level", Key : "level_title", Sort : {Desc : 2, Asc : 3}},
                    {Name : "Link", Key : "href", Sort : {Desc : 4, Asc : 5}}, 
                    {Name : "File location", Key : "flocation", Sort : {Desc : 6, Asc : 7}},
                    {Name :"Location", Key : "location", Sort : {Desc : 8, Asc : 9}},
                    {Name : "Priority", Key : "priority", Sort : {Desc : 10, Asc : 11}},
                    {Name : "Icon", Key : "icon", Sort : {Desc : 14, Asc : 15}}
                    ], target : "links/getAllLinks", createNew : showLinkModal, edit : showLinkModal, delete : true, defaultSort : {Header : 5, Position : "Desc"}, paginate : true},
                    {title : "Boroughs", headers : 
                    [
                    {Name : "Title", Key : "title", Sort : {Desc : 0, Asc : 1}}, 
                    {Name : "Number of listings (Both active and deleted)", Key : "Count", Sort : {Desc : 2, Asc : 3}}
                    ], target : "boroughs/getAllBoroughsCount", createNew: showBoroughModal, edit: showBoroughModal, delete : true, defaultSort : {Header : 1, Position : "Desc"}, paginate : true},
                    {title : "Building types", headers : 
                    [
                    {Name : "Title", Key : "title", Sort : {Desc : 0, Asc : 1}}, 
                    {Name : "Number of listings (Both active and deleted)", Key : "Count", Sort : {Desc : 2, Asc : 3}}
                    ], target : "buildingtypes/getAllBuildingTypesCount", createNew: showBuildingTypeModal, edit: showBuildingTypeModal, delete : true, defaultSort : {Header : 1, Position : "Desc"}, paginate : true},
                    {title : "Survey questions", headers : 
                    [
                    {Name : "Title", Key : "question", Sort : {Desc : 0, Asc : 1}}, 
                    {Name : "Number of times answered", Key : "Count", Sort : {Desc : 2, Asc : 3}}
                    ], target : "questions/getAllQuestions", createNew : showQuestionModal, edit: showQuestionModal, delete : true, viewAnswers: "questions/getSpecificQuestionAnswers", defaultSort : {Header : 1, Position : "Desc"}, paginate : true},
                    {title : "Deleted survey questions", headers : 
                    [
                    {Name : "Title", Key : "question", Sort : {Desc : 0, Asc : 1}}, 
                    {Name : "Number of times answered", Key : "Count", Sort : {Desc : 2, Asc : 3}}
                    ], target : "questions/getAllDeletedQuestions", edit: showQuestionModal, restore : "questions/restoreQuestion", viewAnswers: "questions/getSpecificQuestionAnswers", defaultSort : {Header : 1, Position : "Desc"}, paginate : true},
                    {title : "Room types", headers : 
                    [
                    {Name : "Title", Key : "title", Sort : {Desc : 0, Asc : 1}},
                    {Name : "Number of listings with room (Both active and deleted)", Key : "Count", Sort : {Desc : 2, Asc : 3}}
                    ], target : "roomtypes/getAllRoomTypesCount", createNew: showRoomTypeModal, edit: showRoomTypeModal, delete : true, defaultSort : {Header : "Title", Position : "Desc"}, paginate : true},
                    {title : "Deleted listings", headers : 
                    [
                    {Name : "Name", Key : "listing_name", Sort : {Desc : 0, Asc : 1} }, 
                    {Name : "Price", Key : "price", Suffix : "$", Sort : {Desc : 2, Asc : 3}},
                    {Name :"Description", Key : "description", Sort : {Desc : 4, Asc : 5}}, 
                    {Name : "Borough", Key : "borough_name", Sort : {Desc : 6, Asc : 7}}, 
                    {Name :"Building type", Key : "type_name", Sort : {Desc : 8, Asc : 9}},
                    {Name : "Address", Key : "address", Sort : {Desc : 10, Asc : 11}}, 
                    {Name :"Size", Key : "size", Suffix : " feet", Sort : {Desc : 12, Asc : 13}}
                    ], target : "listings/getAllDeletedListings", edit: showListingModal, restore : "listings/restoreListing", defaultSort : {Header : "Name", Position : "Desc"}, paginate : true},
                    ];
        let activeTable = 0;
        let savedTable = readFromLocalStorage("activeAdminTable");
        if(savedTable){
            activeTable = parseInt(savedTable);
        }
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
            html += `<a href="#" data-id="${table}" class="btn ${active ? "deep-blue" : "soft-blue"} admin-tab">${currTable["title"]}</a>`;
        }
        tabHolder.innerHTML = html;
        //To every button, add an event listener
        let adminTabs = document.querySelectorAll(".admin-tab");
        for(let tab of adminTabs){
            tab.addEventListener("click", function(e){
                e.preventDefault();
                //Only change tab if different tab is selected
                if(activeTable == this.dataset.id){
                    return;
                }
                activeTable = this.dataset.id;
                //Clear selected sort type before switching tabs
                //Correct sorting will be preselected during header generation
                //If any is to be preselected
                sortType = -1;
                saveToLocalStorage(activeTable, "activeAdminTable");
                updateNumOfUsersLoggedIn();
                generateTable(this.dataset.id);
                applyCurrentTab(this.dataset.id);
            })
        }
        updateNumOfUsersLoggedIn();
        generateTable();
        //Set up all modals
        setUpModals();
        function showNoRows(data, args){
            let tableResultHolder = args.tableResultHolder;

            let text = data;

            if(args.noRowsText){
                text = args.noRowsText;
            }

            html = `<p class="text-center w-100 d-block text-dark">${text}</p>`
            tableResultHolder.innerHTML = html;
        }
        function updateNumOfUsersLoggedIn(){
            let target = "activities/getSuccessfulLoginsNum";

            let displayLocation = document.querySelector("#numOfUsers");

            readAjax(target, function(data){
                displayLocation.innerHTML = data;
            }, {}, {});
        }
        //Generates the structure of a table
        function generateTable(){
            generateHeaderTableRow(activeTable)
            //Makas an AJAX request and fills table with resulting information
            updateTable();
        }
        function updateTable(){
            let tableId = activeTable;
            let target = tables[tableId].target;
            let params = {};
            if(sortType != -1){
                params.sort = sortType;
            }
            if(tables[tableId].paginate){
                let page = readFromLocalStorage(tables[tableId].title + "Page");
                params.page = page ? page : 1;
            }
            let args = {};
            args.tableResultHolder = document.querySelector("#table-result-holder");
            args.errorFunction = showNoRows;
            args.noRowsText = "No rows found";
            readAjax(target, fillTable, params, args);
        }
        function applyCurrentTab(){
            activeTable;
            for(let tab of adminTabs){
                if(tab.dataset.id != activeTable){
                    tab.classList.remove("deep-blue");
                    tab.classList.add("soft-blue");
                }
                else{
                    tab.classList.add("deep-blue");
                    tab.classList.remove("soft-blue");
                }
            }
        }
        function fillTable(data, args){
            let html = "";
            let counter = 1;
            let lines = data;
            let tableResultHolder = args.tableResultHolder;
            let currentTable = tables[activeTable];
            let headers = currentTable.headers;
            let paginate = currentTable.paginate && data.perPage;
            if(paginate){
                lines = data.lines;

                var page = data.page;

                var maxPage = data.maxPage;

                var perPage = data.perPage;

                counter = perPage * (page - 1) + 1;
            }
            if(lines.length < 1){
                html += `<p class="text-center w-100 d-block text-dark">No rows to display</p>`
                tableResultHolder.innerHTML = "";
                tableResultHolder.innerHTML += html;
                if(paginate){
                    paginatinHolder = document.querySelector("#table-pagination");

                    paginatinHolder.classList.add("hidden");
                }
                return;
            }
            if(paginate){
                saveToLocalStorage(page, currentTable.title + "Page");
            }
            for(let row of lines){
                html += 
                `
                <tr>
                <td>
                ${counter++}
                </td>
                `
                for(let header of headers){
                    html += 
                    `
                    <td>
                    ${row[header.Key] + (header.Suffix ? header.Suffix : "")}
                    </td>
                    `
                }
                if(!(currentTable.delete || currentTable.edit || currentTable.restore || currentTable.viewAnswers)){
                    continue;
                }
                html += `<td>`
                if(currentTable.edit){
                    html += `<a href="#" data-id="${row["id"]}" class="btn btn-light edit-button">Edit</button>`
                }
                if(currentTable.viewAnswers){
                    html += `<a href="#" data-table="${currentTable.title}" data-id="${row["id"]}" class="btn btn-info view-answers-button">View answers</a>`
                }
                if(currentTable.restore){
                    html += `<a href="#"  data-table="${currentTable.title}" data-id="${row["id"]}" class="btn btn-success restore-button">Restore</a>`
                }
                else if(currentTable.delete){
                    html += `<a href="#" data-table="${currentTable.title}" data-id="${row["id"]}" class="btn btn-danger delete-button">Delete</a>`
                }
                html += `</td></tr>`;
            }

            //Code for generating the "Insert new" button;
            //if the table should show a create new button
            let createNew = currentTable.createNew;
            let edit = currentTable.edit;
            let viewAnswers = currentTable.viewAnswers;
            let restore = currentTable.restore;
            if(createNew)
            {
                html += 
                `
                <tr>
                <a href="#" type="button" class="btn btn-success new-button">Insert new</a>
                </tr>
                `
            }
            tableResultHolder.innerHTML = "";
            tableResultHolder.innerHTML += html;
            if(createNew){
                let newButton = document.querySelector(".new-button");
                newButton.addEventListener("click", function(e){
                    e.preventDefault();
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
            if(viewAnswers){
                let viewAnswerButtons = document.querySelectorAll(".view-answers-button");
                for(let button of viewAnswerButtons){
                    addEventListenerOnce("click", button, function(e){
                        e.preventDefault();
                        let elemId = this.dataset.id;
                        let data = {questionId : elemId};
                        submitAjax(viewAnswers, showQuestionAnswers, data, {closeModal : false});
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
                        submitAjax(restore, showResultGenerateTable, data, {closeModal : false});
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
            if(paginate){
                let paginationHTML = generatePaginationButtons(page, maxPage);

                paginatinHolder = document.querySelector("#table-pagination");

                paginatinHolder.innerHTML = paginationHTML;

                paginatinHolder.classList.remove("hidden");

                addPaginationClick(currentTable.title + "Page", updateTable);
            }
            else{ 
                paginatinHolder = document.querySelector("#table-pagination");

                paginatinHolder.classList.add("hidden");
            }
        }
        function generateHeaderTableRow(){
            let headerTableRow = document.querySelector("#header-table-row");
            let currentTable = tables[activeTable];
            let headers = currentTable.headers;
            let html = "";
            html += 
            `
            <th>
            #
            </th>`
            let headerCount = 0;
            for(let header of headers){
                let sortHtml = "";
                if(header.Sort){
                    sortHtml = `data-state=-1 data-header="${headerCount}" class="clickable sortButton"`;
                }
                html += `
              <th ${sortHtml}>
                ${header.Name}
              </th>`
              headerCount++;
            }
            if((currentTable.delete || currentTable.edit || currentTable.restore || currentTable.viewAnswers)){
                html += 
                `
                <th>
                Options
                </th>
                `
            }
            headerTableRow.innerHTML = html;
            let sortButtons = document.querySelectorAll(".sortButton");

            let selectedHeaderId = readFromLocalStorage("selectedAdminHeaderFor" + currentTable.title.replaceAll(" ", "_"));
            let selectedHeaderPosition = readFromLocalStorage("selecteAdminHeaderPositionFor" + currentTable.title.replaceAll(" ", "_"));

            let preselectSort = selectedHeaderId != null && selectedHeaderPosition != null;

            let defaultSort = currentTable.defaultSort;

            //If supposed to preselect to default value
            if(!preselectSort && defaultSort){
                //This is faster if defaultSort has index instead of name, harder to use/read
                if(typeof defaultSort.Header === "string"){
                    let headerTableElement = headers.filter(el => el.Name == defaultSort.Header)[0];
                    selectedHeaderId = headers.indexOf(headerTableElement);
                }
                else{
                    selectedHeaderId = defaultSort.Header;
                }

                selectedHeaderPosition = defaultSort.Position;

                //If no id found, do not select a default
                if(typeof parseInt(selectedHeaderId) !== "number"){
                    defaultSort = false;
                    console.warn("Invalid header passed as an argument for defaultSort");
                }
            }

            let supposedToSort = preselectSort || defaultSort;

            for(let button of sortButtons){
                if(supposedToSort){
                    let bHeader = button.dataset.header;
                    if(bHeader == selectedHeaderId){
                        //Preselect saved/default header
                        preselectSortHeader(button, selectedHeaderPosition, selectedHeaderId);
                    }
                }
                addEventListenerOnce("click", button, function(e){
                    e.preventDefault();
                    let header = parseInt(this.dataset.header);
                    let currentState = parseInt(this.dataset.state);
                    let direction = "";
                    let newIcon = "";
                    //Flip the state of the element on click
                    switch(currentState){
                        case -1:
                        case 1:
                            currentState = 0;
                            direction = "Asc";
                            newIcon = "	&uarr;";
                            break;
                        case 0:
                            currentState = 1;
                            direction = "Desc";
                            newIcon = "	&darr;";
                            break;
                    }
                    this.dataset.state = currentState;

                    let currentText = this.innerHTML;
                    currentText = currentText.replace(/(↑)|(↓)/, "");
                    currentText += newIcon;
                    this.innerHTML = currentText;

                    //Remove sort representation on all other sortable fields in the table
                    removeAllOtherSorts(header);
                    //Save the header that the user is currently sorting by for this table
                    saveToLocalStorage(header, "selectedAdminHeaderFor" + currentTable.title.replaceAll(" ", "_"));
                    //Save the position that the user is currently sorting by (Desc/Asc) for this table
                    saveToLocalStorage(direction, "selecteAdminHeaderPositionFor" + currentTable.title.replaceAll(" ", "_"));
                    //Save the newly selected sort value
                    sortType = headers[header].Sort[direction];
                    //Make a call with the new data
                    updateTable();
                })
            }
            function preselectSortHeader(button, selectedPosition, selectedHeader){
                let newIcon = "";
                let currentState = -1;
                if(selectedPosition == "Asc"){
                    newIcon = "	&uarr;";
                    currentState = 0;
                }
                else if(selectedPosition == "Desc"){
                    newIcon = "	&darr;";
                    currentState = 1;
                }
                button.dataset.state = currentState;
                button.innerHTML = button.innerHTML + newIcon;
                sortType = headers[selectedHeader].Sort[selectedPosition];
                // console.log(selectedPosition);
                removeAllOtherSorts(selectedHeader);
            }
        }
        function removeAllOtherSorts(currentHeader){
            let sortButtons = document.querySelectorAll(".sortButton");
            for(let button of sortButtons){
                if(button.dataset.state == -1) continue;
                if(button.dataset.header != currentHeader){
                    button.dataset.state = -1;
                    button.innerHTML = button.innerHTML.replace(/(↑)|(↓)/, "");
                }
            }
        }
        function adminDeleteRequest(table, elementId){
            let data = {table, id : elementId};
            submitAjax("general/deleteFromTable", showResultGenerateTable, data, {closeModal : false});
        }
        function showResultGenerateTable(data, args){
            generateTable();
            showResult(data, args);
        }
        function showUserModal(existingId = 0){
            setupUserModal();
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
                removeError(elem, 1);
                removeSuccess(elem);
            }

            if(type == "edit"){
                let data = {id : existingId};
                userIdField.value = existingId;
                submitAjax("users/getSpecificUser", function(data){
                    let firstRow = data;

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
                removeError(elem, 1);
                removeSuccess(elem);
            }
            if(type == "edit"){
                let data = {id : existingId};
                submitAjax("links/getSpecificLink", function(data){
                    let firstRow = data;

                    linkTitleField.value = firstRow.title;
                    LinkHrefField.value = firstRow.href;
                    LinkIconField.value = firstRow.icon ? firstRow.icon : "";
                    accessLevelSelect.value = firstRow.access_level_id;
                    LinkLocation.value = firstRow.location;
                    LinkRoot.checked = firstRow.landing > 0;
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
                LinkPriorityField.value = 1;

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
                submitAjax("boroughs/getSpecificBorough", function(data){
                    boroughNameField.value = data.title;
                }, data);
            }
            else{
                boroughTitle.innerText = "Create new borough";
                boroughSubmitButton.innerText = "Create borough";
            }
            


            let elems = new Array(boroughNameField);

            for(let elem of elems){
                removeError(elem, 1);
                removeSuccess(elem);
            }

            globalData.currModal = modal;
            openModal(modal, globalData.modalBackground);
        }
        function showMessageTypeModal(existingId = 0){
            let modal = document.querySelector("#messageType-modal");

            let type = existingId ? "edit" : "create";

            let messageTypeNameField = document.querySelector("#messageTypeName");

            let messageTypeIdField = document.querySelector("#messageTypeId");

            messageTypeNameField.value = "";
            messageTypeIdField.value = existingId;

            let messageTypeTitle = document.querySelector("#messageType-modal-title");
            let messageTypeSubmitButton = document.querySelector("#messageType-submit");

            if(type == "edit"){
                let data = {id : existingId};
                messageTypeTitle.innerText = "Edit message type";
                messageTypeSubmitButton.innerText = "Edit message type";
                submitAjax("messagetypes/getSpecificMessageType", function(data){
                    messageTypeNameField.value = data.title;
                }, data);
            }
            else{
                messageTypeTitle.innerText = "Create new message type";
                messageTypeSubmitButton.innerText = "Create message type";
            }

            let elems = new Array(messageTypeNameField);

            for(let elem of elems){
                removeError(elem, 1);
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
                submitAjax("buildingtypes/getSpecificBuildingType", function(data){
                    buildingTypeNameField.value = data.title;
                }, data);
            }
            else{
                buildingTypeTitle.innerText = "Create new building type";
                buildingTypeSubmitButton.innerText = "Create building type";
            }
            


            let elems = new Array(buildingTypeNameField);

            for(let elem of elems){
                removeError(elem, 1);
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
                submitAjax("roomtypes/getSpecificRoomType", function(data){
                    roomTypeNameField.value = data.title;
                }, data);
            }
            else{
                roomTypeTitle.innerText = "Create new room type";
                roomTypeSubmitButton.innerText = "Create room type";
            }
            


            let elems = new Array(roomTypeNameField);

            for(let elem of elems){
                removeError(elem, 1);
                removeSuccess(elem);
            }

            globalData.currModal = modal;
            openModal(modal, globalData.modalBackground);
        }
        function showListingModal(existingId = 0){
            setupListingModal();
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
                removeError(elem, 1);
                removeSuccess(elem);
            }

            listingIdField.value = existingId;
            removeAllRooms();
            listingImagePreview.src = "";

            if(type == "edit"){
                let data = {id : existingId};
                submitAjax("listings/getSpecificListing", function(data){
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
                    listingImagePreview.src = `./resources/imgs/${photo.path}`;
                    
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
            let modalTitle = document.querySelector("#question-modal-title");
            let modalSubmitButton = document.querySelector("#question-submit");
            let answerHolder = document.querySelector("#question-answer-holder");
            let questionNameField = document.querySelector("#questionName");
            let questionIdField = document.querySelector("#questionId");

            questionIdField.value = existingId;

            let type = existingId ? "edit" : "create";

            globalData.numberOfAnswers = 1;

            questionNameField.value = "";
            answerHolder.innerHTML = "";
            if(type == "edit"){
                modalTitle.innerText = "Edit survey question";
                modalSubmitButton.innerText = "Edit survey question";
                let data = {questionId : existingId};
                submitAjax("questions/getSpecificQuestion", function(data){
                    let question = data["name"];
                    questionNameField.value = question;
                    let answers = data["answers"];
                    for(let answer of answers){
                        addAnswer(globalData.numberOfAnswers++, answer["answer"], answer["answer_id"]);
                    }
                }, data);
            }
            else{
                modalTitle.innerText = "Create new survey question";
                modalSubmitButton.innerText = "Create new question";
            }


            globalData.currModal = modal;
            openModal(modal, globalData.modalBackground);
        }
        function showQuestionAnswers(data){
            let modal = document.querySelector("#question-results-modal");

            let questionTitleField = document.querySelector("#question-title");
            let questionResultsField = document.querySelector("#question-results");

            let questionTitle = data["name"];
            let questionAnswers = data["answers"]; 

            questionTitleField.innerText = `Question: ${questionTitle}?`;
            questionResultsField.innerHTML = "";
            for(let answer of questionAnswers){
                questionResultsField.innerHTML += addResult(answer["answer"], answer["count"]);
            }

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
            setupMessageTypeModal();
        }
        function setupUserModal(){
            let userRoleSelect = document.querySelector("#userRole"); 
            readAjax("roles/getAllRoles", fillDropdown, {}, [userRoleSelect]);

            let modalSubmitButton = document.querySelector("#user-submit");
            addEventListenerOnce("click", modalSubmitButton, function(e){
                e.preventDefault();
                submitUserForm();
            })
        }
        function setupListingModal(){
            let boroughSelect = document.querySelector("#listingBorough");
            let listingBuildingType = document.querySelector("#listingBuildingType");
            let listingRoomsList = document.querySelector("#listingRoomsList");

            readAjax("boroughs/getAllBoroughs", fillDropdown, {}, [boroughSelect]);
            readAjax("buildingtypes/getAllBuildingTypes", fillDropdown, {}, [listingBuildingType]);
            readAjax("roomtypes/getAllRoomTypes", fillDropdown, {}, [listingRoomsList]);
            

            let fileReader = new FileReader();
            let previewHolder = document.querySelector("#main-photo-preview");
            fileReader.onload = function (e) {previewHolder.src = this.result;}
            let listingPhotoField = document.querySelector("#listingPhoto");
            addEventListenerOnce("change", listingPhotoField, function(e){
                fileReader.readAsDataURL(listingPhotoField.files[0]);
            })

            let addListingRoomButton = document.querySelector("#addListingRoomButton");
            addEventListenerOnce("click", addListingRoomButton, function(e){
                e.preventDefault();
                let roomId = listingRoomsList.options[listingRoomsList.selectedIndex].value;
                let roomText = listingRoomsList.options[listingRoomsList.selectedIndex].text;
                if(roomId==0){
                    addError(listingRoomsList, "Must select a room before adding");
                    removeSuccess(listingRoomsList);
                    return;
                }
                removeError(listingRoomsList, 1);
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
            readAjax("accesslevels/getAllAccessLevels", fillDropdown, {}, [accessLevelSelect]);

            let locationSelect = document.querySelector("#LinkLocation");
            readAjax("links/getAllNavigationLocations", fillDropdown, {}, [locationSelect, true]);

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
        function setupMessageTypeModal(){
            let modalSubmitButton = document.querySelector("#messageType-submit");
            addEventListenerOnce("click", modalSubmitButton, function(e){
                e.preventDefault();
                submitMessageTypeForm();
            })
        }
        function setupQuestionModal(){
            let questionAddAnswerButton = document.querySelector("#questionAddAnswer");
            addEventListenerOnce("click", questionAddAnswerButton, function(e){
                e.preventDefault();
                addAnswer(globalData.numberOfAnswers++, "");
            })

            let modalSubmitButton = document.querySelector("#question-submit");
            addEventListenerOnce("click", modalSubmitButton, function(e){
                e.preventDefault();
                submitQuestionForm();
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
                {re : rePass5, field: userPasswordField, message : `Password must be between 7 and 30 characters long, contain an uppercase letter, a lowercase letter, a digit, and ! or ? or .`},
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

            target = "users/editUser";

            data.userId = userIdField.value;

            data.name = userNameField.value;
            data.lastName = userLastNameField.value;
            data.email = userEmailField.value;
            data.password = userPasswordField.value;
            data.roleId = userRoleField.value;

            submitAjax(target, showResultGenerateTable, data, {closeModal : true});
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

            let target = "listings/createNewListing";

            let errors = 0;

            if(type == "edit"){
                formData.append("listingId", listingId);
                target = "listings/editListing";
            }

            let reTitle = /^[A-Z][a-z]{2,15}(\s[A-Za-z][a-z]{2,15}){0,2}$/;
            let reAddress = /^(([A-Z][a-z\d']+)|([0-9][1-9]*\.?))(\s[A-Za-z\d][a-z\d']+){0,7}\s(([1-9][0-9]{0,5}[\/-]?[A-Z])|([1-9][0-9]{0,5})|(NN))\.?$/;
            let reDescription = /^[A-Z][a-z']{0,50}(\s[A-Za-z][a-z']{0,50})*$/;

            //tests

            if(reTestText(reTitle, listingTitleField, `Title does not match format, eg "Great New Listing" (One to three words)`, 1)) errors++;

            if(reTestText(reDescription, listingDescriptionField, `Description does not match format, only words are allowed in descriptions`, 1)) errors++;

            if(reTestText(reAddress, listingAddressField, `Address does not match format, eg "First Street 20"`, 1)) errors++;

            if(testGeneric(listingSizeField, listingSizeField.value < 30, "Size cannot be below 30 feet", 1)) errors++;

            if(testGeneric(listingSizeField, listingSizeField.value > 100000, "Size cannot be above 100000 feet", 1)) errors++;

            if(testGeneric(listingPriceField, listingPriceField.value < 1000, "Price cannot be below 1000$", 1)) errors++;

            if(testGeneric(listingPriceField, listingPriceField.value > 1000000000, "Price cannot be above 1000000000$", 1)) errors++;

            if(testDropdown(listingBoroughSelect, 0, "You must select a borough", 1)) errors++;

            if(testDropdown(listingBuildingTypeSelect, 0, "You must select a building type", 1)) errors++;

            if(type === "create"){
                if(testImage(listingPhotoField, "")) {
                    errors++
                };
            }
            let roomSelects = document.querySelectorAll(".listingRoom");
            let arrayOfRooms = new Array();
            for(let roomElement of roomSelects){
                if(testGeneric(roomElement, roomElement.value < 1, "Number of rooms cannot be less than one", 2)) {
                    errors++;
                    continue;
                }
                arrayOfRooms.push({roomId : parseInt(roomElement.dataset.id), count : parseInt(roomElement.value)});
            }

            let listingRooms = JSON.stringify(arrayOfRooms);

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

            submitFormDataAjax(target, showResultGenerateTable, formData, {closeModal : true});
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

            let target = "links/createNewLink";

            if(submitType === "edit"){
                data.linkId = linkId;
                target = "links/editLink";
            }
            
            let errors = 0;

            if(reTestText(reTitle, LinkTitleField, "Title does not match format", 1)) errors++;

            if(reTestText(reHref, LinkHrefField, "Link href does not match format", 1)) errors++;

            if(reTestText(reIcon, LinkIconField, "Link icon does not match format", 1)) errors++;

            if(testDropdown(accessLevelSelect, 0, "You must select an access level", 1)) errors++;

            if(testDropdown(LinkLocationSelect, 0, "You must select a location", 1)) errors++;

            if(testGeneric(LinkPriorityField, linkPriority < 1, "Link priority cannot be lower than 1", 1)) errors++;

            if(testGeneric(LinkPriorityField, linkPriority > 99, "Link priority cannot be higher than 99", 1)) errors++;

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

            submitAjax(target, showResultGenerateTable, data, {closeModal : true});
        }
        function submitBoroughForm(){
            let boroughNameField = document.querySelector("#boroughName");
            let boroughIdField = document.querySelector("#boroughId");

            let boroughId = boroughIdField.value;

            let reBoroughName = /^[A-Z][a-z']{1,50}(\s[A-Za-z][a-z']{1,50}){0,3}$/;

            let errors = 0;

            let type = boroughId > 0 ? "edit" : "create";

            let target = "boroughs/createNewBorough";

            let data = {};

            if(type == "edit"){
                target = "boroughs/editBorough";
                data.id = boroughId;
            }

            if(reTestText(reBoroughName, boroughNameField, `Borough name does not match format, eg "The Queens"`)) errors ++;

            if(errors != 0){
                return;
            }

            data.boroughName = boroughNameField.value;

            submitAjax(target, showResultGenerateTable, data, {closeModal : true});
            // submitAjax(target, callMultipleFunctions, data, [showResultGenerateTable, setupListingModal]);
        }
        function submitBuildingTypeForm(){
            let buildingTypeNameField = document.querySelector("#buildingTypeName");
            let buildingTypeIdField = document.querySelector("#buildingTypeId");

            let buildingTypeId = buildingTypeIdField.value;

            let reBuildingTypeName = /^[A-Z][a-z']{1,50}(\s[A-Za-z][a-z']{1,50}){0,3}$/;

            let errors = 0;

            let type = buildingTypeId > 0 ? "edit" : "create";

            let target = "buildingtypes/createNewBuildingType";

            let data = {};

            if(type == "edit"){
                target = "buildingtypes/editBuildingType";
                data.id = buildingTypeId;
            }

            if(reTestText(reBuildingTypeName, buildingTypeNameField, `Building type name does not match format, eg "House"`)) errors ++;

            if(errors != 0){
                return;
            }

            data.buildingTypeName = buildingTypeNameField.value;

            submitAjax(target, showResultGenerateTable, data, {closeModal : true});
        }
        function submitRoomTypeForm(){
            let roomTypeNameField = document.querySelector("#roomTypeName");
            let roomTypeIdField = document.querySelector("#roomTypeId");

            let roomTypeId = roomTypeIdField.value;

            let reRoomTypeName = /^[A-Z][a-z']{1,50}(\s[A-Za-z][a-z']{1,50}){0,3}$/;

            let errors = 0;

            let type = roomTypeId > 0 ? "edit" : "create";

            let target = "roomtypes/createNewRoomType";

            let data = {};

            if(type == "edit"){
                target = "roomtypes/editRoomType";
                data.id = roomTypeId;
            }

            if(reTestText(reRoomTypeName, roomTypeNameField, `Room type name does not match format, eg "Livingroom"`)) errors ++;

            if(errors != 0){
                return;
            }

            data.roomTypeName = roomTypeNameField.value;

            submitAjax(target, showResultGenerateTable, data, {closeModal : true});
        }
        function submitQuestionForm(){
            let questionNameField = document.querySelector("#questionName");
            let questionIdField = document.querySelector("#questionId");

            let reQuestion = /^[A-Z][a-z']{1,20}(\s[A-Za-z][a-z']{0,20}){2,10}$/;
            let reAnswer = /^[A-Z][a-z']{0,20}(\s[A-Za-z][a-z']{0,20}){2,10}$/;

            let type = questionIdField.value > 0 ? "edit" : "create";

            let target = "questions/createNewQuestion";

            let data = {};

            if(type == "edit"){
                data.questionId = questionIdField.value;
                target = "questions/editQuestion"
            }

            let providedAnswers = document.querySelectorAll(".questionAnswer");

            let errors = 0;

            if(reTestText(reQuestion, questionNameField, `Question does not fit format, eg: "Do you like this website"`)) errors++;

            let arrayOfAnswers = new Array();
            for(let answer of providedAnswers){
               if(reTestText(reAnswer, answer, `Answer does not fit format, eg: "Yes I do" (at least three words)`, 2)){
                errors++;
                continue;
               };
               if(type == "create"){
                arrayOfAnswers.push(answer.value);
               }
               else{
                arrayOfAnswers.push({answerId : parseInt(answer.dataset.id), text : answer.value});
               }
            };

            if(errors != 0){
                return;
            }

            let addAnswerButton = document.querySelector("#questionAddAnswer");
            if(arrayOfAnswers.length < 2){
                addError(addAnswerButton, "Must provide at least two answers before submitting", 1);
                errors++;
            }
            else{
                removeError(addAnswerButton, 1);
                addSuccess(addAnswerButton);
            }

            if(errors != 0){
                return;
            }

            data.questionName = questionNameField.value;
            if(type == "create"){
                data.questionAnswers = arrayOfAnswers;
            }
            else{
                data.newAnswers = arrayOfAnswers.filter(answer => answer.answerId === 0);
                data.modifiedAnswers = arrayOfAnswers.filter(answer => answer.answerId != 0);
            }

            submitAjax(target, showResultGenerateTable, data, {closeModal : true});
        }
        function submitMessageTypeForm(){
            let messageTypeNameField = document.querySelector("#messageTypeName");
            let messageTypeIdField = document.querySelector("#messageTypeId");

            let messageTypeId = messageTypeIdField.value;

            let reMessageTypeName = /^[A-Z][a-z]{1,19}$/;

            let errors = 0;

            let type = messageTypeId > 0 ? "edit" : "create";

            let target = "messagetypes/createNewmessageType";

            let data = {};

            if(type == "edit"){
                target = "messagetypes/editMessageType";
                data.id = messageTypeId;
            }

            if(reTestText(reMessageTypeName, messageTypeNameField, `Message type name has to be one capitalized word`)) errors ++;

            if(errors != 0){
                return;
            }

            data.messageTypeName = messageTypeNameField.value;

            submitAjax(target, showResultGenerateTable, data, {closeModal : true});
        }
        function addResult(text, count){
            return `<tr><td>${text}</td><td>${count}</td></tr>`
        }
        function addAnswer(num, answerText, answerId = 0){
            let answerHolder = document.querySelector("#question-answer-holder");
            let newAnswerHolder = document.createElement("div");

            let html = 
            `
            <label for="answer${num}" class="d-block">Answer ${num}</label>
            <input type="text" value="${answerText}" class="form-control d-inline questionAnswer w-50" data-id="${answerId}" name="answer${num}" id="answer${num}">
            <a href="#" class="btn btn-danger d-inline removeAnswer">Remove</a>
            <span class="error-msg hidden d-block"></span>
            `
            newAnswerHolder.innerHTML += html;
            answerHolder.appendChild(newAnswerHolder);
            let removeButtons = document.querySelectorAll(".removeAnswer");

            for(let elem of removeButtons){
                addEventListenerOnce("click", elem, function(e){
                    e.preventDefault();
                    let parentElement = this.parentElement;
                    parentElement.remove();
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
            <a href="#" class="btn btn-danger d-inline removeRoom" id="removeButton${roomId}">Remove</a>
            <span class="error-msg hidden d-block"></span>`
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
        function addRemoveParentOnClickListener(element){
            element.addEventListener("click", function(e){
            e.preventDefault();
            let parentElement = this.parentElement;
            parentElement.remove();
        });
        }
    }
    if(currentPage === "contact.html"){
        let messageTypeSelect = document.querySelector("#messageType");
        let messageTitleField = document.querySelector("#messageTitle");
        let messageBodyField = document.querySelector("#messageBody");
        readAjax("messagetypes/getAllMessageTypes", fillDropdown, {}, [messageTypeSelect]);

        let sendMessageButton = document.querySelector("#sendMessage");
        addEventListenerOnce("click", sendMessageButton, function(e){
            e.preventDefault();
            sendMessage();
        })

        function sendMessage(){
            let data = {};

            let errors = 0;

            let reTitle = /^[A-Z][a-z']{0,19}(\s[A-Za-z][a-z']{0,20}){1,4}$/;
            let reBody = /^[A-Z][a-z']{0,19}(\s[A-Za-z][a-z']{0,20}){2,14}$/;

            if(reTestText(reTitle, messageTitleField, "Message title does not match format (Between two and five words, no punctuation)", 1)) errors++;

            if(reTestText(reBody, messageBodyField, "Message body does not match format (Between three and fifteen words, no punctuation)", 1)) errors++;

            if(testDropdown(messageTypeSelect, 0, "You must select a message type", 1));

            if(errors != 0) return;

            data.message_type_id = messageTypeSelect.value;
            data.title = messageTitleField.value;
            data.body = messageBodyField.value;

            submitAjax("messages/createNewMessage", showResult, data);
        }
    }
    if(currentPage === "survey.html"){
        getQuestionsForUser();
    }
    if(currentPage === "listings.html"){
        let data = {};

        let args = {};

        let boroughArgs = {checkboxHolder : document.querySelector("#boroughFilters"), checkboxName : "boroughFilter"};

        let buildingTypeArgs = {checkboxHolder : document.querySelector("#buildingTypeFilters"), checkboxName : "buildingTypeFilter"};

        args.listingHolder = document.querySelector("#listingHolder");

        let queryString = window.location.search;

        let urlParams = new URLSearchParams(queryString);

        let boroughid = urlParams.get("boroughid");

        let buildingTypeid = urlParams.get("buildingtypeid");

        if(boroughid != null){
            let id = Number(boroughid);
            let boroughFilter = [id];
            saveToLocalStorage(boroughFilter, "boroughFilter");
        }

        if(buildingTypeid != null){
            let buildingFilter = [Number(buildingTypeid)];
            saveToLocalStorage(buildingFilter, "buildingTypeFilter");
        }

        let readBoroughs = readFromLocalStorage("boroughFilter");

        if(readBoroughs){
            data.boroughFilter = readBoroughs;
        }

        let readBuildingType = readFromLocalStorage("buildingTypeFilter");

        if(readBuildingType){
            data.buildingTypeFilter = readBuildingType;
        }

        let selectedSort = readFromLocalStorage("selectedSort");

        if(selectedSort){
            data.sortType = parseInt(selectedSort);
            document.querySelector("#listingsSort").value = selectedSort;
        }

        let page = readFromLocalStorage("listingPage");

        console.log(page);

        data.page = page? page : 1; 

        readAjax("boroughs/getAllBoroughsWithListings", fillCheckbox, {}, boroughArgs);

        readAjax("buildingtypes/getAllBuildingTypesWithListings", fillCheckbox, {}, buildingTypeArgs);

        //Get preexisting filters from local storage, then query for listings

        args.errorFunction = showNoListings;

        submitAjax("listings/getListingsForFilter", displayListings, data, args);

        let searchButton = document.querySelector("#searchListings");
        addEventListenerOnce("click", searchButton, function(e){
            e.preventDefault();
            sendFiltersDisplayListings();
        })
    }
    if(currentPage === "favorites.html"){
        let data = {};

        let args = {};

        data.onlyFavorite = true;

        args.listingHolder = document.querySelector("#listingHolder");

        args.noListingsMessage = "No listings saved as favorite"

        submitAjax("listings/getListingsForFilter", displayListings, data, args);
    }
    if(currentPage === "listing.html"){
        let queryString = window.location.search;

        let urlParams = new URLSearchParams(queryString);

        let id = urlParams.get("listing_id");

        console.log(id);

        if(!id) redirect({ newLocation : "listings.html", landing : false});

        data = {};

        data.listing_id = id;

        args = {};

        args.errorFunction = showListingNotFound;

        args.listingHolder = document.querySelector("#singleListingHolder");

        submitAjax("listings/getSpecificDetailedListing", showSingleListing, data, args);
    }
    if(currentPage === "index.html"){
        let boroughSelect = document.querySelector("#landing-input");
        readAjax("boroughs/getAllBoroughsWithListings", fillDropdown, {}, [boroughSelect]);

        let searchButton = document.querySelector("#landing-search");
        addEventListenerOnce("click", searchButton, function(e){
            e.preventDefault();
            let selectedId = boroughSelect.value;

            if(selectedId == 0){
                redirect({newLocation : "listings.html", landing : false});
            }
            else{
                redirect({ newLocation : `listings.html&boroughid=${selectedId}`, landing : false});
            }
        })
    }
}

function showNoListings(text, args){
    let listingHolder = args.listingHolder;

    let errorText = text;

    if(args.noListingsMessage){
        errorText = args.noListingsMessage;
    }

    listingHolder.innerHTML = `<p class="h3">${errorText}</p>`;
    return;
}

function showSingleListing(data){
    let listingHolder = document.querySelector("#singleListingHolder");

    let body = data["body"];
    let img = data["img"];
    let rooms = data["rooms"];
    let number = data["number"];
    let favorite = data["body"]["favorite"] > 0;

    html = "";

//     <div class="col-12 col-md-4">
//     <div class="listing-img w-100">
//       <img src="../resources/imgs/168217066120666745526443e325515b3.jpg" alt="" class="img-fluid mk-main-img"/>
//         <a href="#" data-id="4" class="mk-favorite-icon-holder">
//           <span class="iconify mk-favorite-icon" data-icon="mdi:cards-heart-outline">Heart</span>
//         </a>
//     </div>
//   </div>
//   <div class="col-12 col-md-8" id="listingHolder">
//     <div class="listing-body">
//       <h3 class="">Listing title</h3>
//       <p class="">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Tempore velit magni necessitatibus voluptates minus nostrum sunt vel, beatae delectus eligendi.</p>
//       <p>Borough: Queens</p>
//       <p>Building type: Duplex</p>
//       <p>Size: 3000 feet</p>
//     </div>
// <div class="d-flex justify-content-between">
    //   <div class="listing-rooms listing-padding">
    //     <h3>Listing rooms</h3>
    //     <p class="d-inline">Livingroom: 5</p>
    //     <p class="d-inline">Bedroom: 5</p>
    //     <p class="d-inline">Bathroom: 5</p>
    //   </div>
//       <div class="contact listing-padding">
//         <a href="#" class="btn btn-success">Copy phone number</a>
//         <p>Price: 3000$</p>
//       </div>
//     </div>
//   </div>

    html += 
    `
    <div class="col-12 col-md-4">
    <div class="listing-img w-100 listing-img-height">
      <img src="./resources/imgs/${img}" alt="${body["listing_name"]} img" class="img-fluid mk-main-img"/>
    <a href="#" data-id="${body["id"]}" class="mk-favorite-icon-holder">
        <span class="iconify mk-favorite-icon" data-icon="${favorite ? "mdi:cards-heart": "mdi:cards-heart-outline"}">Heart</span>
    </a>
    </div>
    </div>
    <div class="col-12 col-md-8" id="listingHolder">
    <div class="listing-body">
      <h3 class="">${body["listing_name"]}</h3>
      <p class="">${body["description"]}</p>
      <a class="router-link" href="${prefix}listings.html&boroughid=${body["borough_id"]}">Borough: ${body["borough"]}</a>
      </br>
      <a class="router-link" href="${prefix}listings.html&buildingtypeid=${body["type_id"]}">Building type: ${body["Type"]}</a>
      <p>Size: ${body["size"]} feet</p>
    </div>
    <div class="d-flex justify-content-between">
    ` 
    if(rooms.length > 0){
        html += 
        `
        <div class="listing-rooms listing-padding">
        <h3>Rooms:</h3>
        `
        for(let room of rooms){
            html+=
            `
            <p class="d-inline">${room["room_name"]}: ${room["numberOf"]}</p>
            `
        }
        html += 
        `
        </div>
        `
    }
    html += 
    `
    <div class="contact listing-padding">
    <a href="#" class="btn soft-blue" id="copy-number-button" data-number="${number}">Copy phone number</a>
    <p>Price: ${body["price"]}$</p>
  </div>
</div>
</div>
    `
    
    listingHolder.innerHTML = html;

    addRouterClick();
    addFavoriteFunctionality();

    let copyNumberButton = document.querySelector("#copy-number-button");

    addEventListenerOnce("click", copyNumberButton, function(e){
        e.preventDefault();
        let num = this.dataset.number;
        navigator.clipboard.writeText(num);
        showSuccess("Successfully copied phone number " + num);
    })

    console.log(data);
}

function addFavoriteFunctionality(){
    let favoriteButtons = document.querySelectorAll(".mk-favorite-icon-holder");
    for(let button of favoriteButtons){
        addEventListenerOnce("click", button, function(e){
            e.preventDefault();
            let idOfListing = this.dataset.id;
            let iconHolder = this.firstElementChild;
            let newIcon = iconHolder.dataset.icon === "mdi:cards-heart" ? "mdi:cards-heart-outline" : "mdi:cards-heart";

            let data = {};

            data.listingId = idOfListing;

            args = {};

            args.additionalFunctions = new Array(flipListingFavoriteIcon)

            args.additionalFunctionArgs = {idOfListing};

            if(newIcon === "mdi:cards-heart"){
                submitAjax("favorites/addToFavoriteListings", showResult, data, args);
            }
            else{
                submitAjax("favorites/removeFromFavoriteListings", showResult, data, args);
            }
        })
    }
}

function showListingNotFound(data, args){
    errorHandler(data);
    let listingHolder = args.listingHolder;
    listingHolder.innerHTML =  `<p class="h3">${data}</p>`;
}

function sendFiltersDisplayListings(){
    let data = {};

    let args = {};

    args.listingHolder = document.querySelector("#listingHolder");

    let selectedBoroughs = new Array();
    let boroughFilters = document.querySelectorAll(".boroughFilter");

    for(let borough of boroughFilters){
        if(borough.checked){
            selectedBoroughs.push(parseInt(borough.value));
        }
    }

    let selectedBuildingTypes = new Array();
    let buildingTypeFilters = document.querySelectorAll(".buildingTypeFilter");

    for(let buildingType of buildingTypeFilters){
        if(buildingType.checked){
            selectedBuildingTypes.push(parseInt(buildingType.value));
        }
    }

    if(document.querySelector("#listingsSort") != null){
        var selectedSort = parseInt(document.querySelector("#listingsSort").value);
        saveToLocalStorage(selectedSort, "selectedSort");
        data.sortType = selectedSort;
    }

    if(boroughFilters.length > 0){
        saveToLocalStorage(selectedBoroughs, "boroughFilter");
        data.boroughFilter = selectedBoroughs;
    }

    if(buildingTypeFilters.length > 0){
        saveToLocalStorage(selectedBuildingTypes, "buildingTypeFilter");
        data.buildingTypeFilter = selectedBuildingTypes;
    }

    if(document.querySelector("#titleFilter") != null){
        let titleFilter = document.querySelector("#titleFilter");
        data.titleFilter = titleFilter.value;
    }

    let page = readFromLocalStorage("listingPage");

    data.page = page ? page : 1; 

    submitAjax("listings/getListingsForFilter", displayListings, data, args);
}

function saveToLocalStorage(data, name){
    localStorage.setItem(name, JSON.stringify(data));
}

function readFromLocalStorage(name){
    return JSON.parse(localStorage.getItem(name));
}

function fillCheckbox(data, args){
    let checkboxHolder = args.checkboxHolder;
    let checkboxName = args.checkboxName;
    let localStoragePresence = readFromLocalStorage(args.checkboxName);
    let html = "";
    let checked = "";
    for(let row of data){
    if(localStoragePresence){
        checked = "";
        for(let elem of localStoragePresence){
            if(elem == row["id"]) checked = `checked="checked"`;
        }
    }
    html += 
    `
    <span class="custom-check">
        <input type="checkbox" ${checked} class="${checkboxName}" name="${checkboxName}" id="${checkboxName}${row["id"]}" value="${row["id"]}">
        <label class="text-dark" for="${checkboxName}${row["id"]}">${row["title"]}</label>
        <span class="custom-check-target"></span>
    </span>
    `
    //     <div>
    //     <input type="checkbox" class="boroughFilter" name="borough" id="The Bronx" value="The Bronx">
    //     <label for="The Bronx">Apartment</label>
    //   </div>
    }
    checkboxHolder.innerHTML = html;
}

function displayListings(data, args){
    let listingHolder = args.listingHolder;

    let paginatinHolder = document.querySelector("#paginationHolder");

    let html = "";

    listingHolder.innerHTML = "";

    let page = data.page;

    saveToLocalStorage(page, "listingPage");

    globalData.listingPage = page;

    let maxPage = data.maxPage;

    let listings = data.listings;

    if(listings.length < 1){
        let errorText = "No listings found for filters provided";
        if(args.noListingsMessage){
            errorText = args.noListingsMessage;
        }
        listingHolder.innerHTML = `<p class="h3">${errorText}</p>`;
        paginatinHolder.innerHTML = generatePaginationButtons(page, maxPage);
        return;
    }

    for(let row of listings){
        let body = row["body"];
        let img = row["img"];
        let rooms = row["rooms"]; 

        let favorite = body["favorite"] > 0;

    // <!--Start of custom listing-->
    // <div class="listing">
    //   <div class="listing-main">
    //     <div class="listing-img w-100 listing-img-height">
    //       <img src="../resources/imgs/168217066120666745526443e325515b3.jpg" alt="" class="img-fluid mk-img-fluid">
    //     </div>
    //     <div class="listing-body">
    //       <h3 class="h5 listing-title">New listing</h3>
    //       <p class="listing-desc">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Mollitia ex architecto quaerat officia assumenda, veritatis dolorem tempora eos nostrum libero?</p>
    //       <p class="listing-size">Size: 3000 feet</p>
    //       <p class="listing-price">Price: 3000$</p>
    //     </div>
    //   </div>
    //   <div class="listing-cover">
    //     <p class="h5 text-center">Additional information</p>
    //     <ul class="list-group list-group-flush w-75">
    //       <li class="list-group-item">Bedrooms: 5</li>
    //       <li class="list-group-item">Livingrooms: 3</li>
    //       <li class="list-group-item">Bathrooms: 2</li>
    //     </ul> 
    //   </div>
    //   <div class="listing-footer w-100">
    //     <a href="#" class="card-link  w-100 listing-read-more text-center">Read more</a>
    //   </div>
    // </div>
    // <!-- End of custom listing-->

        html += 
        `
        <div class="listing">
        <a href="#" data-id="${body["id"]}" class="mk-favorite-icon-holder">
            <span class="iconify mk-favorite-icon" data-icon="${favorite ? "mdi:cards-heart": "mdi:cards-heart-outline"}">Heart</span>
        </a>
        <div class="listing-main">
          <div class="listing-img w-100 listing-img-height">
            <img src="./resources/imgs/thumb${img}" alt="${body["listing_name"]} img" class="img-fluid mk-img-fluid">
          </div>
          <div class="listing-body">
            <h3 class="h5 listing-title">${body["listing_name"]}</h3>
            <p class="listing-desc">${body["description"]}</p>
            <p class="listing-size">Size: ${body["size"]} feet</p>
            <p class="listing-price">Price: ${body["price"]}$</p>
          </div>
        </div>
        <div class="listing-cover">
        <p class="h5 text-center">Additional information</p>
        <ul class="list-group list-group-flush w-75">
          <li class="list-group-item">Building type: ${body["Type"]}</li>
          <li class="list-group-item">Borough: ${body["borough"]}</li>
        `
        for(let room of rooms){
            html += `<li class="list-group-item">${room["room_name"]}: ${room["numberOf"]}</li>`
        }
        html += 
        `
        </ul> 
        </div>
        <div class="listing-footer w-100">
        <a href="${prefix}listing.html&listing_id=${body["id"]}" class="card-link w-100 listing-read-more text-center router-link">Read more</a>
        </div>
    </div>
        `
    }

    let paginationHTML = generatePaginationButtons(page, maxPage);

    listingHolder.innerHTML = html;

    paginatinHolder.innerHTML = paginationHTML;

    addRouterClick();
    addFavoriteFunctionality();
    addPaginationClick("listingPage", sendFiltersDisplayListings);
}

function generatePaginationButtons(page, maxPage){
    paginationHTML = "";
    if(maxPage <= 1){
        return "";
    }
    if(page == maxPage && page > 2){
        paginationHTML += 
        `
        <a data-page=${page - 2} class="col-2 btn soft-blue paginate">${page - 2}</a>
        `;
    }

    if(page > 1){
        paginationHTML += 
        `
        <a data-page=${page - 1} class="col-2 btn soft-blue paginate">${page - 1}</a>
        `;
    }

    let nextPage = page

    paginationHTML += 
    `
    <a data-page=${nextPage} class="col-2 btn deep-blue">${nextPage}</a>
    `;

    if(page < maxPage - 1 && page == 1){
        paginationHTML += 
        `
        <a data-page=${++nextPage} class="col-2 btn soft-blue paginate">${nextPage}</a>
        `;
    }

    if(page < maxPage){
        paginationHTML += 
        `
        <a data-page=${++nextPage} class="col-2 btn soft-blue paginate">${nextPage}</a>
        `;
    }

    return paginationHTML;
}

function addRouterClick(){
    if(canUseRouter){
    let routerLinks = document.querySelectorAll(".router-link");

    for(let elem of routerLinks){
        addEventListenerOnce("click", elem, function(e){
            e.preventDefault();
            let href = this.href;
            removeActiveFromAllOtherLinks();
            this.classList.add("active");
            changeUrl(href);
        })
    }
    }
}

function addPaginationClick(storeName, onClick){
    let paginationButtons = document.querySelectorAll(".paginate");
    for(let button of paginationButtons){
        addEventListenerOnce("click", button, function(e){
            e.preventDefault();
            let page = this.dataset.page;
            saveToLocalStorage(page, storeName);
            onClick();
        })
    }
}

function flipListingFavoriteIcon(args){
    let favoriteButtons = document.querySelectorAll(".mk-favorite-icon-holder");
    let listingId = args.idOfListing;
    for(let button of favoriteButtons){
        if(button.dataset.id == listingId){
            let iconHolder = button.firstElementChild;
            iconHolder.dataset.icon = iconHolder.dataset.icon === "mdi:cards-heart" ? "mdi:cards-heart-outline" : "mdi:cards-heart";
        }
    }
}

function getQuestionsForUser(){
    readAjax("questions/getQuestionsForUser", displaySurveyQuestions, {});
}

function setGlobal(data, args){
    globalData[args.name] = data;
}

function displaySurveyQuestions(data){
    // <!--One form-->
    // <form id="surveyQuestion1" class="my-2 question-form">
    //     <div class="mb-3">
    //         <p class="h2">Question: How do you like this website?</p>
    //     </div>
    //     <div class="mb-3">
    //         <div class="form-check">
    //             <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
    //             <label class="form-check-label" for="flexRadioDefault1">
    //               Default radio
    //             </label>
    //         </div>
    //           <div class="form-check">
    //             <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" checked>
    //             <label class="form-check-label" for="flexRadioDefault2">
    //               Default checked radio
    //             </label>
    //         </div>
    //     </div>
    //     <button type="submit" id="sendMessage" name="sendMessage" class="btn btn-primary">Submit answer</button>
    //   </form>
    // <!--End of one form-->
    let html = ``;
    let surveyHolder = document.querySelector("#surveyHolder");
    surveyHolder.innerHTML = "";
    let question;
    let answers;

    if(data.length < 1){
        html += `<p class="h2 text-center">No more questions left</p>`;
        surveyHolder.innerHTML = html;
        return;
    }

    for(let row of data){
        question = row["question"];
        answers = row["answers"];

        html+= `
        <form id="surveyQuestion${question["id"]}" class="my-2 question-form bg-dark">
        <div class="mb-3">
            <p class="h2">Question: ${question["question"]}?</p>
        </div>
        <div class="mb-3">
        `
        for(let answer of answers){
        html += 
        `
        <div class="form-check">
            <input class="form-check-input" type="radio" value="${answer["answer_id"]}" required="required" name="questionAnswers${question["id"]}" id="answer${answer["answer_id"]}">
            <label class="form-check-label" for="answer${answer["answer_id"]}">
                ${answer["answer"]}
            </label>
        </div>
        `
        }
        html += `
        </div>
        <button type="submit" data-id="${question["id"]}" id="submitForm${question["id"]}" name="submitForm${question["id"]}" class="btn btn-primary submit">Submit answer</button>
        <span class="error-msg hidden"></span>
        </form>`
    }
    surveyHolder.innerHTML = html;
    let submitButtons = document.querySelectorAll(".submit");
    for(let button of submitButtons){
        addEventListenerOnce("click", button, function(e){
            e.preventDefault();
            let questionId = this.dataset.id;
            submitQuestionAnswer(questionId);
        })
    }
}

function submitQuestionAnswer(questionId){
    let answerCheckboxes = document.getElementsByName(`questionAnswers${questionId}`);
    let selectedAnswerId = 0;

    for(let checkbox of answerCheckboxes){
        if(checkbox.checked){
            selectedAnswerId = checkbox.value;
            break;
        }
    }
    let submitButton = document.querySelector(`#submitForm${questionId}`);
    if(selectedAnswerId === 0){
        addError(submitButton, "You must select an answer before submitting", 1);
        return;
    }
    else{
        removeError(submitButton, 1);
    }
    data = {};
    data.answerId = selectedAnswerId;
    args = {};
    args.additionalFunctions = new Array(getQuestionsForUser);
    submitAjax("questions/submitAnswer", showResult, data, args);
}

function showSuccess(data){
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
}

function showResult(data, args){
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

function addEventListenerOnce(event, element, onEvent, listenerMark = ""){
    let listenerMarker = event + listenerMark;

    if(element.classList.contains(listenerMarker)){
        return;
    }

    element.classList.add(listenerMarker);
    element.addEventListener(event, onEvent);
}

function fillDropdown(data, args){
    let selectElement = args[0];
    let simple = args[1];
    let value;
    let title;
    let newElement;
    let identifier = selectElement.id;
    let children = document.querySelectorAll(`.${identifier}`);
    for(let child of children){
        child.remove();
    }
    for(let row of data){
        if(simple){
            value = row;
            title = row;
        }
        else{
            value = row.id;
            title = row.title;
            if(row.Count){
                title += ` (${row.Count})`;
            }
        }
        let firstLetter = title.charAt(0);

        let firstLetterCap = firstLetter.toUpperCase();

        let remainingLetters = title.slice(1);

        title = firstLetterCap + remainingLetters;

        newElement = document.createElement("option");

        newElement.innerText = title;

        newElement.setAttribute("value", value);

        newElement.classList.add(identifier);

        selectElement.appendChild(newElement);
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

    let navbarHTML = ""
    let footerHTML = ""

    url = generateUrl(headerElement, prefix);

    headerHolder.href = url;
    headerHolder.text = headerElement.link_title;
    headerHolder.classList.add("router-link");

    footerHeaderHolder.href = url;
    footerHeaderHolder.text = headerElement.link_title;
    footerHeaderHolder.classList.add("router-link");

    for(let navbarElement of navbarElements){
        navbarHTML += generateLinkElement(navbarElement, prefix, true);
    }

    if(accessLevel > 1){
        navbarHTML += 
        `
          <li class="nav-item">
            <a class="nav-link" id="logoutButton" aria-current="page" href="#">Log out</a>
          </li>
        `;
    }

    for(let footerElement of footerElements){
        footerHTML += generateLinkElement(footerElement, prefix, false);
    }

    navbarHolder.innerHTML = navbarHTML;
    footerHolder.innerHTML = footerHTML;

    if(accessLevel > 1){
        let logoutButton = document.querySelector("#logoutButton");
        logoutButton.addEventListener("click", function(e){
            e.preventDefault();
            readAjax("users/logout", redirectSuccess, {}, { newLocation : "login.html", landing : false});
        })
    }

    addRouterClick();
}

function addActiveToLinkThatContains(page){
    let links = document.querySelectorAll(".router-link");
    for(let link of links){
        if(link.href.includes(page)){
            link.classList.add("active");
        }
    }
}

function removeActiveFromAllOtherLinks(){
    let links = document.querySelectorAll(".router-link");
    for(let link of links){
        link.classList.remove("active");
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

function testGeneric(field, statement, errorMessage, errorHolderDistance = 1){
    if(statement){
        removeSuccess(field);
        addError(field, errorMessage, errorHolderDistance);
        return 1;
    }
    removeError(field, errorHolderDistance);
    addSuccess(field);
    return 0;
}

function testNumericBounds(field, minimalValue, errorMessage, errorHolderDistance = 1){
    let value = field.value;
    if(value < minimalValue){
        removeSuccess(field);
        addError(field, errorMessage, errorHolderDistance);
        return 1;
    }
    removeError(field, errorHolderDistance);
    addSuccess(field);
    return 0;
}

function testDropdown(field, negativeValue, errorMessage, errorHolderDistance = 1){
    let value = field.value;
    //On success
    if(value != negativeValue){
        removeError(field, errorHolderDistance);
        addSuccess(field);
        return 0;
    }
    //On fail
    else{
        removeSuccess(field);
        addError(field, errorMessage, errorHolderDistance);
        return 1;
    }
}

function testImage(field, errorMessage, errorHolderDistance = 1){
    let value = field.value;
    if(value == ""){
        removeSuccess(field);
        addError(field, errorMessage, errorHolderDistance);
        return 1;
    }
    removeError(field, errorHolderDistance);
    addSuccess(field);
}

function reTestText(regex, field, errorMessage, errorHolderDistance = 1){
    let textValue = field.value;
    let passes = regex.test(textValue);
    if(passes){
        if(errorMessage != ""){
            removeError(field, errorHolderDistance);
            addSuccess(field);
        }
        return 0;
    }
    else{
        if(errorMessage != ""){
            removeSuccess(field);
            addError(field, errorMessage, errorHolderDistance);
        }
        return 1;
    }
}

function getNthNextElement(field, n){
    let elem = field;
    for(let i = 0; i < n; i++){
        elem = elem.nextElementSibling;
    }
    return elem;
}

function addSuccess(field){
    field.classList.add("success-outline");
}

function removeError(field, errorHolderDistance = 0 ){
    if(errorHolderDistance > 0){
        let errorBox = getNthNextElement(field, errorHolderDistance);
        errorBox.innerText = "";
        errorBox.classList.add("hidden");
    }
    field.classList.remove("error-outline");
}

function removeSuccess(field){
    field.classList.remove("success-outline");
}

function addError(field, msg, errorHolderDistance){
    if(msg != ""){
    let errorBox = getNthNextElement(field, errorHolderDistance);
        console.log(errorBox);
        errorBox.innerText = msg;
        errorBox.classList.remove("hidden");
    }
    field.classList.add("error-outline");
}

function redirect(args){
    let newLocation = args.newLocation;
    if(canUseRouter){
        changeUrl(prefix + newLocation);
        return;
    }
    
    let additionalText = ""
    if(window.location.hostname === "localhost"){
        additionalText = "/nycestatee";
    }

    // let newLink = window.location.hostname + additionalText + (landing ? `/${newLocation}` : `/pages/${newLocation}`); 
    let newLink = window.location.hostname + additionalText + (`/index.php?page=${newLocation}`); 
    window.location.assign("https://" + newLink);

}

//Wrapper for the redirect function
function redirectSuccess(data, args){
    showSuccess(data);
    redirect(args);
}

function readAjax(url, resultFunction, params, args = {}){
    let request = createRequest();
    request.onreadystatechange = function(){
        if(request.readyState == 4){
            handleServerResponse(resultFunction, args, request);
        }
    }
    request.open("GET", ajaxPath+url+".php?" + new URLSearchParams(params));
    request.send(JSON.stringify(params));
}

function submitAjax(url, resultFunction, data, args = {}){
    let request = createRequest();
    request.onreadystatechange = function(){
        if(request.readyState == 4){
            handleServerResponse(resultFunction, args, request);
        }
    }

    request.open("POST", ajaxPath+url+".php");
    request.setRequestHeader("Content-type", "application/json");
    console.log(data);
    request.send(JSON.stringify(data));
}

function submitFormDataAjax(url, resultFunction, data, args = {}){
    let request = createRequest();
    request.onreadystatechange = function(){
        if(request.readyState == 4){
            handleServerResponse(resultFunction, args, request);
        }
    }
    request.open("POST", ajaxPath+url+".php");
    console.log(data);
    request.send(data);
}

function handleServerResponse(resultFunction, args, request){
    if(request.status >= 200 && request.status < 300){
        console.log(request.responseText);
        let data = JSON.parse(request.responseText);
        if(args != {}){
            resultFunction(data.general, args);
            if(args.additionalFunctions){
                let additionalArgs = args.additionalFunctionArgs ? true : false;
                for(let func of args.additionalFunctions){
                    if(additionalArgs){
                        func(args.additionalFunctionArgs);
                        continue;
                    }
                    func();
                }
            }
        }
        else{
            resultFunction(data.general);
        }
    }
    else if(request.status >= 300 && request.status < 400){
        redirect(args);
    }
    else if(args.redirectOnNotAllowed && (request.status === 401 || request.status === 403)){
        redirect(args);
    }
    else{
        let data = JSON.parse(request.responseText);
        if(args.errorFunction){
            args.errorFunction(data["error"], args);
            return;
        }
        errorHandler(data["error"]);
        console.log(data["error"]);
    }
}


function generateUrl(object, redirect = ""){
    let url = "";
    if(object.landing == 1){
       url += '';
    }
    else{
       url += `${redirect}`;
    }
    url += object.href;
    return url;
 }


 function generateLinkElement(object, redirect, routerLink){
    let html;
    let url = generateUrl(object, redirect);
    let text = object.link_title;
    let icon = object.icon;
    if(icon == null){
        html = 
        `
        <li class="nav-item">
          <a class="nav-link ${currentPage == object.href ? "active" : ""} ${routerLink ? "router-link" : ""}" id=${text} aria-current="page" href="${url}">${text}</a>
        </li>
      `
    }
    else{
        html = `
        <li class="col-md-3 col-6 icon-holder">
            <a class="${currentPage == object.href ? "active" : ""} ${routerLink ? "router-link" : ""}" href="${url}">
                <span class="iconify" id="${text}-icon" data-icon="${icon}"></span>
            </a>
        </li>
        `
    }
    return html;
 }