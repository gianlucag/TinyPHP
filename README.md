![Logo](./logo.svg)

# What is TinyPHP?

TinyPHP is a fast, simple, extensible collection of PHP scripts to
quickly and easily build RESTful api and web applications.

It provides the following functionalities:

- Routing
- REST API basic functions (Access GET and POST data)
- Cryptographic functions (Generate random UUIDs, nonces etc..)
- MySQL (Launch a query, get the results)
- L10n (Translations via json dictionaries)
- Logging (.log files)
- Authentication (Cookie and X-Auth-Token)
- File download
- Excel/CSV import and export
- Configuration
- File upload

Basic example

```php
require 'tinyphp/tinyphp.php';

TinyPHP::RegisterRoute("/login", "html/login.php");
TinyPHP::Run();
```

# Is TinyPHP a framework?

Kind of. It provides built-in functions for common tasks, but it doesn't force any folder structure nor naming conventions for you project. In that sense, TinyPHP is not really a framework like CakePHP or Laravel.

# So what is it?

It's really just a set of libraries for commonly used functions, cutting down on the amount of code you have to write.

# Requirements

TinyPHP requires `PHP 7.4` or greater.

# License

TinyPHP is released under the [MIT](LICENSE.txt) license.

# Installation

1\. Download the files, take the lib folder and place it in your web environment.

2\. Configure your webserver.

For _Apache_, edit your `.htaccess` file with the following:

```
RewriteEngine On
RewriteRule ^(.*)$ index.php [QSA,L]
```

3\. Create your `index.php` file.

First include TinyPHP.

```php
require_once 'tinyphp/tinyphp.php';
```

Then define a route and assign a php script to handle the request. The path to the php file is relative to your index.php file.

```php
TinyPHP::RegisterRoute("/", "html/main.php");
TinyPHP::RegisterRoute("/login", "html/login.php");
```

If you want, assign a 404 handler.

```php
TinyPHP::Register404("html/404.php");
```

Finally, start TinyPHP.

```php
TinyPHP::Run();
```

# Routing

Routing in TinyPHP is done by matching a URL pattern with a php script.

```php
TinyPHP::RegisterRoute("/login", "html/login.php");
```

## Root path

If you have a common root path for all your endpoint, you can use the `RegisterRoot` function.

```php
TinyPHP::RegisterRoot("/my/common/path");
```

# Examples

Some useful examples.

## Web application

```php
TinyPHP::RegisterRoot("/demo");
TinyPHP::RegisterRoute("/", "html/dashboard.php");
TinyPHP::RegisterRoute("/login", "html/login.php");
TinyPHP::RegisterRoute("/user", "html/user.php");
TinyPHP::RegisterRoute("/menu", "html/menu.php");
TinyPHP::Register404("html/404.php");

TinyPHP::Run();
```

## REST API with one endopoint

File `index.php`

```php
TinyPHP::RegisterRoot("/api/1.0.0");
TinyPHP::RegisterRoute("/getuser", "endpoints/getuser.php");

TinyPHP::Run();
```

File `getuser.php`

```php
function DbErrorCallback($msg)
{
    Api::Error(500, $msg);
}

$dbConfig = (object)[
    "host" => "host",
    "name" => "dbname",
    "user" => "username",
    "pass" => "password"
];

Db::Init($dbConfig, "DbErrorCallback");

// get the userid from query string
$get = API::Get();
$userid = $get->id;

// launch query
$res = Db::Query("SELECT * FROM users WHERE id = ?", [$userid]);

Api::Ok($res);
```

Invoking the API

```
https://myhost/api/1.0.0/getuser?id=12
```
