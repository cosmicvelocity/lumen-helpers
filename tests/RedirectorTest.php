<?php

namespace CosmicVelocity\LumenHelpers\Tests;

use CosmicVelocity\LumenHelpers\Http\Redirector;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Session\CacheBasedSessionHandler;
use Illuminate\Session\Store;
use Laravel\Lumen\Application;
use Mockery as m;

class RedirectorTest extends TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testSessionInheritance()
    {
        $session = new Store('session', new CacheBasedSessionHandler(new Repository(new ArrayStore()), 30));
        $session->put('Hello', 'World');

        $app = new Application();
        $app->instance('session.store', $session);

        $redirector = new Redirector($app);
        $response = $redirector->to('/');

        $this->assertEquals($session, $response->getSession());
        $this->assertEquals('World', $response->getSession()->get('Hello'));
    }

}
