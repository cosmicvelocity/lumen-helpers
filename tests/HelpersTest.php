<?php

namespace CosmicVelocity\LumenHelpers\Tests;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Session\CacheBasedSessionHandler;
use Illuminate\Session\Store;
use Laravel\Lumen\Application;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class HelpersTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testRedirectWithSession()
    {
        $session = new Store('session', new CacheBasedSessionHandler(new Repository(new ArrayStore()), 30));
        $session->put('Hello', 'World');

        $app = new Application();
        $app->instance('session.store', $session);

        $response = redirectWithSession('/');

        $this->assertEquals($session, $response->getSession());
        $this->assertEquals('World', $response->getSession()->get('Hello'));
    }

}
