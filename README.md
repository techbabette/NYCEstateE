# NYCEstate

## Tech stack

Made with vanilla PHP, Javascript and HTML.

## Notable features

### Vanilla Javascript/PHP page routing (SSR)

PHP serves the first page the user requests and any page navigation afterwards is handled by Javascript.

This rendering style most closely resembles that of Next.js and Nuxt.

### User activity tracking

On every page visit, the client sends the server information about which page it's attempting to access.

If the user is not allowed access to a certain page, they are redirected to the landing page and shown an error message. 

Admins can track all page visits and view which pages are the most popular in the admin panel.

### Extendable admin panel

The admin panel gives admins control over all dynamic content on the site.

Admin panel tabs are generated based on an array of objects.

An admin panel object contains a list of headers which determine which pieces of data (keys) to show, under what name, and whether a piece of data can be used to sort the table (Sorting is performed entirely on the backend).

An admin panel object also contains a datasource (target).

The example below results in a users table that can be sorted by every header, reads data from the users/getAllUsers endpoint, calls the showUserModal function when the edit button is pressed, allows for row deletion and is by default sorted by the header "Date of creation" in descending order.
```
{title : "Users", headers : 
[
{Name : "Name", Key : "name", Sort : {Desc : 0, Asc : 1}},
{Name : "Last name", Key : "lastName", Sort : {Desc : 2, Asc : 3}},
{Name : "Email", Key : "email", Sort : {Desc : 8, Asc : 9}}, 
{Name : "Date of creation", Key : "dateCreated", Sort : {Desc : 4, Asc : 5}}, 
{Name : "Role", Key : "role_name", Sort : {Desc : 6, Asc : 7}}
], target : "users/getAllUsers", edit : showUserModal, delete : true, 
defaultSort : {Header : "Date of creation", Position : "Desc"}, paginate : true},
```

![Users table](https://i.imgur.com/R6LdXkr.png "Users table")
