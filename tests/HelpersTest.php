<?php

namespace CosmicVelocity\LumenHelpers\Tests;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Http\Request;
use Illuminate\Session\CacheBasedSessionHandler;
use Illuminate\Session\Store;
use Laravel\Lumen\Application;
use Mockery as m;

class HelpersTest extends TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testAction()
    {
        $app = new Application();

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
        new Application();

        $this->assertEquals('/app', app_path());
    }

    public function testMix()
    {
        $app = new Application(__DIR__);
        $app->make('config')->set('app.url', 'http://127.0.0.1:8080');
        $app->make('config')->set('mix.port', '18081');

        $request = Request::create('/', 'GET', [], [], [], ['HTTP_HOST' => '127.0.0.1:8080']);
        $app->instance('request', $request);

        file_put_contents(public_path('/hot'), '');

        $this->assertEquals((string)mix('/css/default.css'), 'http://127.0.0.1:18081/css/default.css');

        @unlink(public_path('/hot'));
    }

    public function testRedirectWithSession()
    {
        $session = new Store('session', new CacheBasedSessionHandler(new Repository(new ArrayStore()), 30));
        $session->put('Hello', 'World');

        $app = new Application();
        $app->instance('session.store', $session);

        $response = redirect_with_session('/');

        $this->assertEquals($session, $response->getSession());
        $this->assertEquals('World', $response->getSession()->get('Hello'));
    }

}
