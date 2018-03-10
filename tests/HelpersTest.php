<?php

namespace CosmicVelocity\LumenHelpers\Tests;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Http\Request;
use Illuminate\Session\CacheBasedSessionHandler;
use Illuminate\Session\Store;
use Laravel\Lumen\Application;
use Laravel\Lumen\Routing\Router;
use Mockery as m;
use Exception;

class HelpersTest extends TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testAction()
    {
        $app = new Application();

        $this->assertEquals(Application::getInstance(), $app);

        /** @var Router $router */
        $router = null;
        $matches = [];

        if (preg_match('/Lumen \(([0-9\.]+)\)/', $app->version(), $matches)) {
            $version = floatval(trim($matches[1]));

            // lumen 5.5 以降であれば router を取得する。
            if (5.5 <= $version) {
                $router = app('router');
            } else {
                $router = $app;
            }
        } else {
            $router = $app;
        }

        $router->get('/', 'IndexController@index');
        $router->get('/version.html', function () {
            return 'Hello !!';
        });
        $router->get('/view/{id}', 'IndexController@view');

        $request = Request::create('/', 'GET');
        $app->instance('request', $request);

        $this->assertEquals('http://localhost', action('IndexController@index'));
        $this->assertEquals('/', action('IndexController@index', [], false));
        $this->assertEquals('http://localhost/view/1', action('IndexController@view', ['id' => '1']));
        $this->assertEquals('/view/1', action('IndexController@view', ['id' => '1'], false));
    }

    public function testAppPath()
    {
        $app = new Application();

        $this->assertEquals(Application::getInstance(), $app);
        $this->assertEquals('/app', app_path());
    }

    /**
     * @covers ::mix
     */
    public function testMix1()
    {
        try {
            $hotPath = public_path('/hot');

            $app = new Application(__DIR__);
            $app->make('config')->set('app.url', 'http://127.0.0.1:8080');
            $app->make('config')->set('mix.port', '18081');
            $app->instance('request', Request::create('/', 'GET', [], [], [], ['HTTP_HOST' => '127.0.0.1:8080']));

            file_put_contents($hotPath, '');

            $this->assertEquals(Application::getInstance(), $app);
            $this->assertEquals((string)mix('/css/default.css'), '//localhost:18081/css/default.css');

            $app->make('config')->set('mix.host', '192.168.1.1');
            $app->make('config')->set('mix.port', '18081');

            $this->assertEquals((string)mix('/css/default.css'), '//192.168.1.1:18081/css/default.css');

        } catch (Exception $ex) {

        } finally {
            @unlink($hotPath);
        }
    }

    /**
     * @throws Exception
     */
    public function testMix()
    {
        try {
            $app = new Application(__DIR__);
            $app->make('config')->set('app.url', 'http://127.0.0.1:8080');
            $app->make('config')->set('mix.port', '18081');
            $app->instance('request', Request::create('/', 'GET', [], [], [], ['HTTP_HOST' => '127.0.0.1:8080']));

            file_put_contents(public_path('/hot'), '');

            $this->assertEquals(Application::getInstance(), $app);
            $this->assertEquals((string)mix('/css/default.css'), '//localhost:18081/css/default.css');

            @unlink(public_path('/hot'));

            $app2 = new Application(__DIR__);
            $app2->make('config')->set('app.url', 'http://127.0.0.1:28080/dir1/dir2/');
            $app2->instance('request', Request::create('/dir1/dir2/', 'GET', [], [], [], [
                'SCRIPT_FILENAME' => '/var/www/public/dir1/dir2/index.php',
                'SCRIPT_NAME' => '/dir1/dir2/index.php',
                'HTTP_HOST' => '127.0.0.1:28080',
                'PHP_SELF' => '/dir1/dir2/index.php'
            ]));

            $this->assertEquals(Application::getInstance(), $app2);
            $this->assertEquals((string)mix('/css/default.css'), '/css/default.css');

            $app3 = new Application(__DIR__);
            $app3->make('config')->set('app.url', 'http://127.0.0.1:28080/dir1/dir2/');
            $app3->make('config')->set('mix.port', '18081');
            $app3->instance('request', Request::create('/dir1/dir2/', 'GET', [], [], [], [
                'SCRIPT_FILENAME' => '/var/www/public/dir1/dir2/index.php',
                'SCRIPT_NAME' => '/dir1/dir2/index.php',
                'HTTP_HOST' => '127.0.0.1:28080',
                'PHP_SELF' => '/dir1/dir2/index.php'
            ]));

            file_put_contents(public_path('/hot'), '');

            $this->assertEquals(Application::getInstance(), $app3);
            $this->assertEquals((string)mix('/css/default.css'), '//localhost:18081/css/default.css');

            @unlink(public_path('/hot'));

        } catch (Exception $ex) {
            @unlink(public_path('/hot'));

            throw $ex;
        }
    }

    public function testRedirectWithSession()
    {
        $session = new Store('session', new CacheBasedSessionHandler(new Repository(new ArrayStore()), 30));
        $session->put('Hello', 'World');

        $app = new Application();
        $app->instance('session.store', $session);

        $response = redirect_with_session('/');

        $this->assertEquals(Application::getInstance(), $app);
        $this->assertEquals($session, $response->getSession());
        $this->assertEquals('World', $response->getSession()->get('Hello'));
    }

}
