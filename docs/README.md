# Documentation

List of modules and their methods:

## TinyPHP

### RegisterRoute

Attach a route to a page

```php
$path = "/login";
$controller = "html/login.php";
TinyPHP::RegisterRoute($path, $controller);
```

You can specify dynamic route params in the URL (e.g. :groupid).

```php
TinyPHP::RegisterRoute("/groups/:groupid/categories", "html/categories.php");
```

## GetRouteParam

Gets the route param "groupid" from the URL.

```php
TinyPHP::GetRouteParam("groupid");
```

### RegisterRoot

Specifies the root path common to all routes. That's to avoid specifying the common path over and over on all `RegisterRoute()` calls.

```php
$rootPath = "/demo";
TinyPHP::RegisterRoot($rootPath);
```

### Register404

Register the special 404 not found controller.

```php
$notFoundController = "/html/404.php";
TinyPHP::Register404($notFoundController);
```

### Register500

Register the special 500 internal error page.

```php
$internalErrorController = "/html/500.php";
TinyPHP::Register500($internalErrorController);
```

### ThrowError500

Display the pre-registered 500 internal error page.

```php
$msg = "My error message";
TinyPHP::ThrowError500($msg);
```

### RegisterMaintenance

Register the special maintenance page (visible to any client trying to request any route).

```php
$maintenanceController = "/html/maintenance.php";
TinyPHP::RegisterMaintenance($maintenanceController);
```

### EnableMaintenance

Activate maintenance mode. The specified IP address will be exempted from maintenance mode, allowing normal access to the website. Typically, this would be the IP address of the client conducting maintenance on the website.

```php
$allowedIPAddress = "192.168.1.100";
TinyPHP::EnableMaintenance($allowedIPAddress);
```

### Run

Serves the incoming client request. This method should be called last, after all the route registration and modules initialization has been done.

```php
TinyPHP::Run();
```

## API

### Ok

Return 200 and an optional custom payload:

```php
$res = ["result" => "success"];
API::Ok($res);
```

### Error

Return an HTTP error code and a custom message:

```php
API::Error(500, "message");
```

### Post

Get a json payload sent by POST:

```php
$post = API::Post();
echo $post->userid;
echo $post->email;
```

### Get

Get the query parameters sent by GET:

```php
$getParams = API::Get();
echo $getParams->userid;
echo $getParams->email;
```

## Config

### Init

Initialization:

```php
function OnFieldNotFoundCallback($msg)
{
    Api::Error(500, $msg);
}

Config::Init("configFile.json", OnFieldNotFoundCallback);
```

### GetField

Get a configuration field:

```php
$field = Config::GetField("myConfigField");
echo $field;
```

## Crypt

### GetRandomHex

Get a random hex string:

```php
$hex = Crypt::GetRandomHex(5);
echo $hex; // "0a56b"
```

### GetRandomUUID

Get a random UUID:

```php
$uuid = Crypt::GetRandomUUID();
echo $uuid; // "d1577826-9ec4-4704-90db-c9240b441e86"
```

### Encrypt

Encrypt a string using AES-256 (CTR mode). Returns a base64 cyphertext:

```php
$cypherBase64 = Crypt::Encrypt("mymessage", "mykey");
```

### Decrypt

Decrypt a base64 cyphertext using AES-256 (CTR mode). Returns the plain text message:

```php
$plainBase64 = Crypt::Decrypt("mycyphertext", "mykey");
```

## Currency

Manages currency values.

### Init

Initialization:

```php
$format = "€ #,#";
$decimalDigits = 2;
Currency::Init($format, $decimalDigits);
```

### Format

Format an amount:

```php
$amount = 1999;
$options = [
    "format" => "€ #,#", // optional, if a different format is required
    "fractDigits" => 2, // optional, fractional digits to display
];
$formattedAmount = Currency::Format($amount);
echo $formattedAmount; // "€ 19,99"
```

```php
$amount = 1999;
$options = [
    "fractDigits" => 1,
];
$formattedAmount = Currency::Format($amount);
echo $formattedAmount; // "€ 19,9"
```

### Parse

Parse a currency value:

```php
$raw = "19,99"
$value = Currency::Parse($raw);
echo $value; // 1999
```

## Date

All timestamps are represented as "Y-m-d H:i:s" strings.

### Now

Get current timestamp

```php
$now = Date::Now();
echo $now; // "1982-05-19 21:30:45"
```

### Format

Format a timestamp using a specific format:

```php
$timestamp = "1982-05-19 21:30:45";
$formattedDate = Date::Format($timestamp, "Y");
echo $formattedDate; // "1982"
```

### FormatYMD

Format a timestamp using the "Y-m-d" format:

```php
$timestamp = "1982-05-19 21:30:45";
$formattedDate = Date::FormatYMD($timestamp);
echo $formattedDate; // "1982-05-19"
```

### FormatYMDHis

Format a timestamp using the "Y-m-d H:i:s" format:

```php
$timestamp = "1982-05-19 21:30:45";
$formattedDate = Date::FormatYMDHis($timestamp);
echo $formattedDate; // "1982-05-19 21:30:45"
```

### Parse

Parse a date in a custom format:

```php
$date = "19 05 1982";
$timestamp = Date::Parse($date, "d m Y");
echo $timestamp; // "1982-05-19 21:30:45"
```

### ParseYMD

Parse a date in the "Y-m-d" format:

```php
$date = "1982-05-19";
$timestamp = Date::ParseYMD($date);
echo $timestamp; // "1982-05-19 21:30:45"
```

### ParseYMDHis

Parse a date in the "Y-m-d H:i:s" format:

```php
$date = "1982-05-19 21:30:45";
$timestamp = Date::ParseYMDHis($date);
echo $timestamp; // "1982-05-19 21:30:45"
```

### AddDays

Add days to a timestamp:

```php
$timestamp = "1982-05-19 21:30:45";
$timestamp = Date::AddDays($timestamp, 2);
echo $timestamp; // "1982-05-21 21:30:45"
```

### AddMonths

Add months to a timestamp:

```php
$timestamp = "1982-05-19 21:30:45";
$timestamp = Date::AddMonths($timestamp, 2);
echo $timestamp; // "1982-07-19 21:30:45"
```

### AddYears

Add years to a timestamp:

```php
$timestamp = "1982-05-19 21:30:45";
$timestamp = Date::AddYears($timestamp, 2);
echo $timestamp; // "1984-05-19 21:30:45"
```

### DiffDays

Compute the numbers of days between two timestamps:

```php
$timestamp1 = "1982-05-19 21:30:45";
$timestamp2 = "1982-05-21 21:30:45";
$diffDays = Date::DiffDays($timestamp1, $timestamp2);
echo $diffDays; // 2
```

## Db

Execute MySQL queries and get results.

### Init

Initialization:

```php
function OnDbErrorCallback($msg)
{
    Api::Error(500, $msg);
}

$config = (object)[
    "host" => "myhost",
    "user" => "myuser",
    "pass" => "mypass",
    "name" => "mydbname",
];

Db::Init($config, "OnDbErrorCallback");
```

### Query

Launch a query:

```php

$res = Db::Query("SELECT * FROM users WHERE id = ? AND username = ?;", [123, "gianluca"]);
```

## Dictionary

### Init

Initialization:

```php
$config = (object)[
    "default_language" => "it",
    "languages": [
        [
            "lang" => "it",
            "file" => "dictionaries/it.json"
        ],
        [
            "lang" => "en",
            "file" => "dictionaries/en.json"
        ]
    ];

Dictionary::Init($config);
```

Dictionary example:

```json
{
	"404_TITLE": "404",
	"404_BODY": "Page not found"
}
```

### txt

Translate a dictionary key:

```php
$res = txt("404_BODY");
echo $res; // "Page not found"
```

### etxt

Translate and echo a dictionary key:

```php
etxt("404_BODY"); // "Page not found"
```

### SetLanguage

Set the language to a specific one. If not present in the language list, the default is used (see config object). If the language is not passed, it auto detects the client language.

```php
Dictionary::SetLanguage("it");
```

### GetLanguage

Get the current set language

```php
$lang = Dictionary::GetLanguage();
echo $lang; // "it"
```

```php
Dictionary::SetLanguage();
```

## Download

Download a file to the browser.

### Start

Start the download:

```php
$binaryContent = [binarydata];
Download::Start("myfile.pdf", "application/pdf", $binaryContent);
```

## Input

### Clean

Generic sanitization of the user input:

```php
$cleanMessage = Input::Clean($userMessage);
```

## Logger

Log messages to file.

### Init

Initialization:

```php
$logFolder = "/logs";
Logger::Init($logFolder);
```

### Write

Write a log line:

```php
$sink = "LOGIN";
$message = "User logged in";
Logger::Write($sink, $message);
```

## QRCodeGenerator

Generates QR-Codes.

### GetPng

Send the QR-Code to the browser as a PNG:

```php
$qrcodedata = "test";
QRCodeGenerator::GetPng($qrcodedata);
```

### GetImage

Get the QR-Code image as Data URI Scheme (i.e. "data:image/png;base64,...")

```php
$qrcodedata = "test";
$image = QRCodeGenerator::GetImage($qrcodedata);
```

## Upload

Get a file uploaded by the browser.

### IsFileUploaded

Is there a file being uploaded in the current POST request ?

```php
$res = Upload::IsFileUploaded();
```

### GetFileSize

Get the file size in bytes:

```php
$size = Upload::GetFileSize();
echo $size; // 1048576
```

### GetFileExtension

Get the file extension:

```php
$extension = Upload::GetFileExtension();
echo $extension; // "pdf"
```

### GetUploadedFilePath

Get the actual file:

```php
$path = Upload::GetUploadedFilePath();
$fileContent = file_get_content($path);
```

## Captha

Google Re-Captcha test.

### Init

Initialization:

```php
$pubKey = "6Lf_GlQpAA.....";
$privKey = "6Lf_GlQpAA.....";
Captcha::Init($pubKey, $privKey);
```

### InjectReCaptchaOnClick

This call injects the necessary JavaScript function to trigger the ReCAPTCHA test upon user interaction. Invoke the `submitFormWithCaptchaCheck()` JavaScript function on form button click:

```php
Captcha::InjectReCaptchaOnClick();
```

### IsHuman

Server side verification:

```php
$isHuman = Captcha::IsHuman($score = 0.5);
```

## Mail

Send an email

### Send

Send an email:

```php
$from = "senderi@gmail.com";
$fromname = "Gianluca";
$to = "receiver@gmail.com";
$subject = "Email subject";
$content = "Hello world!\nBye bye!";
$attachments = [
    "cid1", "email_attachments/logo.jpg",
    "cid2", "email_attachments/file.pdf",
];
$ccs = [
    "receiver_ccs1@receiver.com",
    "receiver_ccs2@receiver.com",
    "receiver_ccs3@receiver.com",
];

$isSent = Mail::Send(
    $from,
    $fromname,
    $to,
    $subject,
    $content,
    $attachments,
    $ccs
);
```

### SetDebug

For debugging purposes. All emails will be sent to the provided test email.

```php
$testEmail = "test@email.com";
Mail::SetDebug($testEmail);
```

### SetEmailSignature

Set the email signature. The signature is appended automatically to all emails.

```php
$signature = "My email signature";
Mail::SetEmailSignature($signature);
```

## SpreadSheet

Read and write Excel/CSV files.

### Load

Load an Excel file:

```php
$isLoaded = SpreadSheet::Load("my/spread/sheet.xlsx", null);
```

Load an CSV file:

```php
$isLoaded = SpreadSheet::Load("my/spread/sheet.csv", ";"); // ";" = delimiter
```

### GetTotRows

Get total rows:

```php
$totRows = SpreadSheet::GetTotRows();
```

### GetTotCols

Get total columns:

```php
$totCols = SpreadSheet::GetTotCols();
```

### GetRow

Get a row. The row index is zero based (e.g. 0 = first row)

```php
$rowIndex = 3;
$maxCellLength = 1000;
$totCols = SpreadSheet::GetRow($rowIndex, $maxCellLength);
```

### BuildCSVBinary

Creates a CSV file and save it to disk:

```php
$header = ["col1", "col2", "col3"];
$data = [
    ["a1", "b1", "c1"],
    ["a2", "b2", "c2"]
];
$separator = ";";
$dest = "my/spread/sheet.csv";

$isCreated = SpreadSheet::BuildCSVBinary($header, $data, $separator, $dest);
```

Creates a CSV file and returns it:

```php
$header = ["col1", "col2", "col3"];
$data = [
    ["a1", "b1", "c1"],
    ["a2", "b2", "c2"]
];
$separator = ";";

$csvBinaryData = SpreadSheet::BuildCSVBinary($header, $data, $separator);
```

Creates an Excel file and save it to disk:

```php
$header = ["col1", "col2", "col3"];
$data = [
    ["a1", "b1", "c1"],
    ["a2", "b2", "c2"]
];
$separator = ";";
$dest = "my/spread/sheet.xlsx";

$isCreated = SpreadSheet::BuildExcelBinary($header, $data, $separator, $dest);
```

Creates an Excel file and returns it:

```php
$header = ["col1", "col2", "col3"];
$data = [
    ["a1", "b1", "c1"],
    ["a2", "b2", "c2"]
];
$separator = ";";

$excelBinaryData = SpreadSheet::BuildExcelBinary($header, $data, $separator);
```

### GetExcelMime

Returns Excel mime type. Useful for sending the file to the browser as a download:

```php
$mimeType = SpreadSheet::GetExcelMime();
```

### GetCSVMime

Returns CSV mime type:

```php
$mimeType = SpreadSheet::GetCSVMime();
```

## Stripe

Payment processor.

### Init

Initialization.

```php
$config = (object)[
    "phpSecretKey" => "sk_live_xxxxxxxxxxxxxxx",
    "jsPublicKey" => "pk_live_xxxxxxxxxxxxxxx",
    "endpointSecret" => "whsec_xxxxxxxxxxxxxxx",
    "version" => "YYYY-MM-DD"
];

Stripe::Init($config);
```

### Start

Call `Start()` at the earliest possibile moment in your checkout php page to bootstrap the Stripe payment process. Pass a custom purchase payload (eg. product name and quantity). The purchase payload will be sent by Stripe as-is to your payment webhook upon succesul payment.

```php
$userMemail = "buyer@gmail.com";
$productName = "Product name shown on Stripe checkout page";
$description = "Product description shown on Stripe checkout page";
$imageUrl = "https://www.mywebsite.com/myproduct.png";
$price = 2099; // 20.99
$currency = "eur";
$successUrl = "https://www.mywebsite.com/paymentsuccess";
$cancelUrl = "https://www.mywebsite.com/paymentcancelled";
$purchasePayload = json_encode(["plan" => "PRO", "months" => 12]);

Stripe::Start(
    $userEmail,
    $productName,
    $description,
    $imageUrl,
    $price,
    $currency,
    $successUrl,
    $cancelUrl,
    $purchasePayload
);
```

### OutputJavaScript

Call `OutputJavaScript()` to make available the `launchStripePaymentProcess` JavaScript function, used to open the Stripe payment page on the client browser.

PHP side:

```php
Stripe::OutputJavaScript();
```

Javascript side:

```javascript
$("#buy_button").on("click", () => {
	launchStripePaymentProcess();
});
```

### ProcessWebHook

Call `ProcessWebHook()` on your Stripe webhook to process the response from Stripe. `paymentSuccessCallback` is called with the transaction id, user email and the purchase payload specified in `Start()`. The method reacts to the `checkout.session.completed` event only (payment successful).

```php
function paymentSuccessCallback($transactionId, $userEmail, $purchasePayload)
{
    // payment was successful, do your stuff here (unlock features etc..)
}

Stripe::ProcessWebHook($paymentSuccessCallback);
```

## Auth

Authentication. This module provides a simple authentication system using cookie-based or token-based authentication. It allows for user login, session management, and user information retrieval.

### Plugins

This module utilizes plugins to handle user and session management. These plugins implement specific interfaces that define the required methods for interacting with user data and sessions. Here are the details of each plugin.

The database implementation for the plugins are already provided buy the module (`AuthPluginDbUser` and `AuthPluginDbSession`).

### Default user plugin

```php
$options = (object)[
    "tableName" => "customers",
    "userIdFieldName" => "email",
    "usernameFieldName" => "email",
    "passwordFieldName" => "password"
];

$userPlugin = new AuthPluginDbUser();
$userPlugin->Init($options);
```

If needed, it's also possible to redefine the password check algorithm:

```php
$options = (object)[
    "tableName" => "customers",
    "userIdFieldName" => "email",
    "usernameFieldName" => "email",
    "passwordFieldName" => "password"
];

function CustomPasswordCheckFunction($password, $storedPassword)
{
    return sha1($password) == $storedPassword;
}


$userPlugin = new AuthPluginDbUser();
$userPlugin->Init($options, "CustomPasswordCheckFunction");
```

### Default session plugin

```php
$options = (object)[
    "tableName" => "sessions",
    "sessionIdFieldName" => "sessionid",
    "tokenFieldName" => "token"
];

$sessionPlugin = new AuthPluginDbSession();
$sessionPlugin->Init($options);
```

If you need to use a different database or authentication mechanism, you can create custom plugins by implementing the respective interfaces with your custom behavior.

Here's the interface for the two plugins:

```php
interface AuthUserInterface {
    public function Login($username, $password); // returns token on success, false on failure/denied
    public function GetUserId($username); // returns the user id
    public function GetUserInfo($id); // returns the user object
}

interface AuthSessionInterface {
    public function AddSession($id, $token);
    public function DeleteSessions($id);  // if id is not specified, delete all session of current user
    public function DeleteSession($token);
    public function GetSessionId($token); // returns the session id
    public function TruncateSessions();  // delete all sessions of all users
}
```

### Init

Initialize the authentication module.

Using cookie:

```php
$options = (object)[
    "method" => "cookie",
    "cookieName" => "my-cookie-name"
];

Auth::Init($options, $userPlugin);
```

Using x-auth token:

```php
$options = (object)[
    "method": "xauth"
];

Auth::Init($options, $userPlugin);
```

### AddSessionPlugin

Registers a session plugin into the Auth module.

```php
$pluginName = "customers";
Auth::AddSessionPlugin($pluginName, $sessionCustomersPlugin);
```

The `AddSessionPlugin` method enables the registration of multiple session plugins, allowing for seamless switching between different entities on the fly, such as users and customers.

### SetSessionPlugin

Use a specific session plugin.

```php
Auth::SetSessionPlugin("customers");
```

### Login

Authenticate the user. Returns `false` if unsuccessful, returns a sessionToken if successful (and set the cookie, if method is `cookie`)

```php
$username = "username";
$password = "password";

$sessionToken = Auth::Login($username, $password);
```

### LogoutAllSessions

Logout all current logged in clients belonging to the user.

```php
Auth::LogoutAllSessions();
```

### LogoutThisSession

Logout the current client.

```php
Auth::LogoutThisSession();
```

### IsLogged

Is the user logged ?

```php
$isLogged = Auth::IsLogged($config);
```

### GetLoggedUserInfo

Get the current logged in user data.

```php
$user = Auth::GetLoggedUserInfo();
```

### SetNewPassword

Set a new password for the current user

```php
$newPassword = "myNewPassword";
Auth::SetNewPassword($newPassword);
```

### TruncateSessions

Delete all sessions of all users

```php
Auth::TruncateSessions();
```
