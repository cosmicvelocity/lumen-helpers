<?php

namespace CosmicVelocity\LumenHelpers\Http;

use Laravel\Lumen\Http\Redirector as BaseRedirector;

/**
 * セッションの引継ぎを行うリダイレクタ。
 *
 * @package CosmicVelocity\LumenHelpers\Http
 */
class Redirector extends BaseRedirector
{

    /**
     * @inheritdoc
     */
    protected function createRedirect($path, $status, $headers)
    {
        $redirect = parent::createRedirect($path, $status, $headers);

        // lumen の Redirector では setSession を行わないので行います。
        $redirect->setSession($this->app->make('session.store'));

        return $redirect;
    }

}
