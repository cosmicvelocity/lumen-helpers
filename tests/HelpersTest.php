<?php

namespace CosmicVelocity\LumenHelpers\Tests;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Http\Request;
use Illuminate\Session\CacheBasedSessionHandler;
use Illuminate\Session\Store;
use Laravel\Lumen\Application;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class HelpersTest extends TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testAction()
    {
        $app = new Application();
        $app->get('/', 'IndexController@index');
        $app->get('/version.html', function () {
            return 'Hello !!';
        });
        $app->get('/view/{id}', 'IndexController@view');

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
