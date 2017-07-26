# lumen-helpers
Lumen ではサポートされていない Laravel の機能をサポートするヘルパーを提供します。

Lumen は 5.2 からステートレスな API を提供する事に焦点をあてるようになったため、session などが外されています。
しかしながら処理速度の軽い Lumen は通常のウェブアプリケーション開発にも魅力的です。

幸い、Lumen は Laravel のコンポーネントを組み込む事で、外された機能を再度取り込む事もできます。
そういった Lumen の使い方をした際に不足する機能を提供するのがこのライブラリです。

そのほか、Lumen 5.1 未満で開発したアプリケーションのアップグレード時の補助や、
将来的に Laravel へ移行することを想定しての実装互換性の向上などに利用できます。

## インストール
composer を使っている場合は、下記のような記述を追加する事で導入できます。

```json
{
    "repositories": [
        {
          "type": "vcs",
          "url": "https://github.com/cosmicvelocity/lumen-helpers.git"
        }
    ],
    "require": {
        "cosmicvelocity/lumen-helpers": ">=1.0"
    }
}
```

セッションに関連するヘルパーを使用する場合、Lumen でセッションが使えるように設定されている必要があります。

Lumen 5.4 でセッションを有効にする場合、bootstrap.php で下記のように Application を設定します。
  
```php
// 必要なエイリアスを追加。
$app->alias('cookie', \Illuminate\Cookie\CookieJar::class);
$app->alias('cookie', \Illuminate\Contracts\Cookie\Factory::class);
$app->alias('cookie', \Illuminate\Contracts\Cookie\QueueingFactory::class);
$app->alias('session', \Illuminate\Session\SessionManager::class);
$app->alias('session.store', \Illuminate\Session\Store::class);
$app->alias('session.store', \Illuminate\Contracts\Session\Session::class);

// 設定ファイルを読み込み。
// ※Laravel 同様の設定ファイルを config/session.php で用意する必要があります。
$app->configure('session');

$app->withFacades(true, [
    \Illuminate\Support\Facades\Config::class => 'Config'
]);

// Cookie, Session のサービスプロバイダを設定。
$app->register(\Illuminate\Cookie\CookieServiceProvider::class);
$app->register(\Illuminate\Session\SessionServiceProvider::class);

// ミドルウェアを設定。
$app->middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
]);
```

## 使い方
現在提供されているヘルパーは下記のようなものがあります。

それぞれ Laravel の同名ヘルパーと同様の動作を行います。

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
- **session**
- **validator**

その他、下記のような独自のヘルパーを提供します。

- **app_with**:
    makeWith の呼び出しに対応する app() 互換のヘルパー。
    illuminate/container:5.4 から make に $parameters を渡せなくなり、代わって makeWith が用意されましたが、
    app_with を使う事で Lumen, illuminate/container のバージョンを問わすパラメーターを渡しての make を行えます。

- **redirect_with_session**:
    Lumen 5.2 以降の redirect ヘルパーはセッションの引継ぎは行わないため、セッションの引継ぎを行う redirect_with_session を提供しています。
    ただし、事前に SessionServiceProvider などの組み込みを行い、Lumen でセッションが有効になっている必要があります。

    ```php
    return redirect_with_session(route('index'))
        ->withErrors($errors)
        ->withInput();
    ```

    redirect_with_session を使う事で withErrors, withInput 等も期待する動作をするようになります。
    redirect を redirect_with_session に置き換えたい場合は、bootstrap/app.php で下記のようにする事で置き換える事ができます。
    
    ```php
    // 先に redirect を定義。
    function redirect($to = null, $status = 302, $headers = [], $secure = null) {
      return redirect_with_session($to, $status, $headers, $secure);
    }
  
    require_once __DIR__ . '/../vendor/autoload.php';
    ```
