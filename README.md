# cosmicvelocity/lumen-helpers
Lumen provides a helper to support Laravel 's unsupported features.

Since Lumen started focusing on providing stateless APIs from 5.2, session etc etc has been removed.
However, Lumen, which has a low processing speed, is also attractive for regular web application development.

Fortunately, Lumen can incorporate the components of Laravel and reintroduce the removed function.
This library provides functions that are missing when you use Lumen like that.

In addition, it supports the upgrading of applications developed under Lumen 5.1,
It can be used for improving implementation compatibility, assuming that you are planning to migrate to Laravel in the future.

## Installation
If composer is used, it can be introduced by adding the following description.

```json
{
    "require": {
        "cosmicvelocity/lumen-helpers": ">=1.0"
    }
}
```

When using helpers related to sessions, Lumen needs to be set so that sessions can be used.

To enable sessions with Lumen 5.4, set the Application in bootstrap.php as shown below.
  
```php
// Add required alias.
$app->alias('cookie', \Illuminate\Cookie\CookieJar::class);
$app->alias('cookie', \Illuminate\Contracts\Cookie\Factory::class);
$app->alias('cookie', \Illuminate\Contracts\Cookie\QueueingFactory::class);
$app->alias('session', \Illuminate\Session\SessionManager::class);
$app->alias('session.store', \Illuminate\Session\Store::class);
$app->alias('session.store', \Illuminate\Contracts\Session\Session::class);

// Read the configuration file.
// You need to prepare a configuration file similar to Laravel in config/session.php.
$app->configure('session');

$app->withFacades(true, [
    \Illuminate\Support\Facades\Config::class => 'Config'
]);

// Set cookie, Session service provider.
$app->register(\Illuminate\Cookie\CookieServiceProvider::class);
$app->register(\Illuminate\Session\SessionServiceProvider::class);

// Set middleware.
$app->middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
]);
```

## How to use
The helper currently provided is as follows.

I will do the same operation as Laravel 's same name helper.

- **abort_if**
- **abort_unless**
- **action**
- **app_path**
- **asset**
- **auth**
- **back**
- **bcrypt**
- **cache**
- **cookie**
- **csrf_field**
- **csrf_token**
- **logger**
- **method_field**
- **mix**
- **old**
- **policy**
- **public_path**
- **report**
- **session**
- **validator**

In addition, we provide our own helper as below.

- **app_with**:
    An app () compatible helper corresponding to makeWith call.
    illuminate/container: From 5.4, you can not pass $parameters to make, instead makeWith prepared,
    By using app_with, make can be done by passing parameters that ask Lumen, illuminate / container version.

- **redirect_with_session**:
    Since the redirect helper from Lumen 5.2 onward does not take over the session, we provide redirect_with_session to inherit the session.
    However, it must be built in advance such as SessionServiceProvider and session must be enabled in Lumen.

    ```php
    return redirect_with_session(route('index'))
        ->withErrors($errors)
        ->withInput();
    ```

    By using redirect_with_session, you will be expecting withErrors, withInput etc.
    If you want to replace redirect with redirect_with_session you can replace it with bootstrap/app.php as follows.
    
    ```php
    // Define redirect first.
    function redirect($to = null, $status = 302, $headers = [], $secure = null) {
      return redirect_with_session($to, $status, $headers, $secure);
    }
  
    require_once __DIR__ . '/../vendor/autoload.php';
    ```
