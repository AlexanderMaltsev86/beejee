###### Test project for BeeJee.
The following entities has been added:
1. Controllers: `TaskController`, `LoginController`, `ErrorController`. 
2. Models: base `Model` class, `Task` and `User`.
3. Middlewares: base `Middleware` class and `AuthMiddleware` implementation for authentification.
4. `View` - returned by controllers and contains template name and params. `View` renders template using parameters and supports a layout.
5. `Router` - contains routes stored in `Route` class. Router finds a route by URI or calls 404 error route. If route contains middlewares they will be called by `Router`. If controller returned an instance of `View` class it renders itself using template.
6. `RequestContext` - currently contains only variables, used by middlewares to pass variables to controllers and view templates. 
7. `UriResolver` - utility class to get a full URI by relative URI using `basePath` from configuration file. 
8. `DI` - simple dependency injection implementation. Currently used to store configuration, `UriResolver` and `RequestContext`.
9. `Application` - entry point called in `public/index.php` file. Used to move application logic from `index.php` file.

###### Database configuration
Change database config in `config/database.php` file and run this SQL code to your database:
```
 create table tasks
 (
 	id serial not null
 		constraint tasks_pk
 			primary key,
 	name text not null,
 	email text not null,
 	text text not null,
 	completed boolean default false not null,
 	updated_by text
 );
 
 create table users
 (
 	id serial not null
 		constraint users_pk
 			primary key,
 	login text not null,
 	password_hash text not null
 );
```
