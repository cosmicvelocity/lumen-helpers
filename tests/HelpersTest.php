<?php

namespace CosmicVelocity\LumenHelpers\Tests;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Http\Request;
use Illuminate\Session\CacheBasedSessionHandler;
use Illuminate\Session\Store;
use Laravel\Lumen\Application;
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
        Application::setInstance($app);

        $app->router->get('/', 'IndexController@index');
        $app->router->get('/version.html', function () {
            return 'Hello !!';
        });
        $app->router->get('/view/{id}', 'IndexController@view');

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
        Application::setInstance($app);

        $this->assertEquals('/app', app_path());
    }

    public function testMix()
    {
        try {
            $app = new Application(__DIR__);
            Application::setInstance($app);
            $app->make('config')->set('app.url', 'http://127.0.0.1:8080');
            $app->make('config')->set('mix.port', '18081');

            $request = Request::create('/', 'GET', [], [], [], ['HTTP_HOST' => '127.0.0.1:8080']);
            $app->instance('request', $request);

            file_put_contents(public_path('/hot'), '');

            $this->assertEquals((string)mix('/css/default.css'), '//localhost:18081/css/default.css');

            @unlink(public_path('/hot'));

            $app2 = new Application(__DIR__);
            Application::setInstance($app2);
            $app2->make('config')->set('app.url', 'http://127.0.0.1:28080/dir1/dir2/');
            $request = Request::create('/dir1/dir2/', 'GET', [], [], [], [
                'SCRIPT_FILENAME' => '/var/www/public/dir1/dir2/index.php',
                'SCRIPT_NAME' => '/dir1/dir2/index.php',
                'HTTP_HOST' => '127.0.0.1:28080',
                'PHP_SELF' => '/dir1/dir2/index.php'
            ]);
            $app2->instance('request', $request);

            $this->assertEquals((string)mix('/css/default.css'), '/css/default.css');

            $app3 = new Application(__DIR__);
            Application::setInstance($app3);
            $app3->make('config')->set('app.url', 'http://127.0.0.1:28080/dir1/dir2/');
            $app3->make('config')->set('mix.port', '18081');
            $request = Request::create('/dir1/dir2/', 'GET', [], [], [], [
                'SCRIPT_FILENAME' => '/var/www/public/dir1/dir2/index.php',
                'SCRIPT_NAME' => '/dir1/dir2/index.php',
                'HTTP_HOST' => '127.0.0.1:28080',
                'PHP_SELF' => '/dir1/dir2/index.php'
            ]);
            $app3->instance('request', $request);

            file_put_contents(public_path('/hot'), '');

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
        Application::setInstance($app);
        $app->instance('session.store', $session);

        $response = redirect_with_session('/');

        $this->assertEquals($session, $response->getSession());
        $this->assertEquals('World', $response->getSession()->get('Hello'));
    }

}
