# lumen-helpers
Lumen ではサポートされていない Laravel の機能をサポートするヘルパーを提供します。

Lumen は 5.2 からステートレスな API を提供する事に焦点をあてるようになったため、session などが外されています。
しかしながら軽量な Lumen は通常の WEB アプリケーション開発にも魅力的です。

幸い、Lumen は Laravel のコンポーネントを組み込む事で外された機能を追加する事もできますので、
そういった用途に便利に使える Lumen で外されてしまった Laravel の機能を提供するのがこのライブラリです。

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

- **app_with**: makeWith の呼び出しに対応する app() 互換のヘルパー。
- **redirect_with_session**: Lumen の redirect ヘルパーはセッションの引継ぎは行いませんが、redirect_with_session ではセッションの引継ぎを行います。ただし、事前に SessionServiceProvider などの組み込みを行い、Lumen でセッションが有効になっている必要があります。
