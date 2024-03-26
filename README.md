![Logo](./logo.svg)

# What is TinyPHP?

TinyPHP is a fast, simple, extensible collection of PHP scripts to
quickly and easily build RESTful APIs and web applications.

It provides the following functionalities:

- Routing
- REST API basic functions (Access GET and POST data, return payloads and http error codes)
- Cryptographic functions (Generate random UUIDs, nonces etc..)
- MySQL (Launch queries, get results)
- L10n (Translations via json dictionaries)
- Logging (.log files)
- Authentication (Cookie and X-Auth-Token)
- File download
- File upload
- Excel/CSV import and export
- Configuration
- Datetime (parse, format, math on dates)
- Google Captcha v3
- Input sanitization
- Email send (html templates, attachments, cc and bcc recipients)
- Stripe (payments)
- QRCodes
- Currency (format currency amounts)

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

# Why another framewor...ehm... collection of libraries?

I frequently find myself repeatedly writing certain functions or transferring them from one project to another. Over the years, I've accumulated a variety of functionalities that address a wide range of API and web use cases. Ultimately, I've made the decision to centralize them on GitHub, assigning them a uniform name, and adopting proper tagging and versioning practices, something I should have done right from the beginning :-)

# Why I should use it?

If you aim to swiftly create an API, a straightforward web application, or a website without introducing the intricacies associated with PHP frameworks such as CakePHP and Laravel, this tool is well-suited for the task.

# Requirements

TinyPHP requires `PHP 7.4` or greater.

Also, some features require external, third-party libraries, here's the list:

- PHPMailer 6.9.1 (used by `Mail`)
- PhpSpreadsheet 1.29.0 (used by `SpreadSheet`)

These libraries should be placed inside the `vendor` folder.
If you don't plan to use these functionalities, simply comment out the appropriate include file in `tinyphp.php`

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

Routing in TinyPHP is done by matching a URL pattern with a php script (the controller).

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

## REST API with one endpoint

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
