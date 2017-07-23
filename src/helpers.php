<?php

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\HtmlString;
use Laravel\Lumen\Routing\UrlGenerator;

if (!function_exists('asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool $secure
     *
     * @return string
     */
    function asset($path, $secure = null)
    {
        return (new UrlGenerator(app()))->to($path, null, $secure);
    }
}

if (!function_exists('auth')) {
    /**
     * Get the available auth instance.
     *
     * @param string|null $guard
     *
     * @return AuthFactory|Guard|StatefulGuard
     */
    function auth($guard = null)
    {
        if (is_null($guard)) {
            return app(AuthFactory::class);
        } else {
            return app(AuthFactory::class)->guard($guard);
        }
    }
}

if (!function_exists('back')) {
    /**
     * Create a new redirect response to the previous location.
     *
     * @param int $status
     * @param array $headers
     * @param mixed $fallback
     *
     * @return RedirectResponse
     */
    function back($status = 302, $headers = [], $fallback = false)
    {
        return redirect()->back($status, $headers, $fallback);
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate a CSRF token form field.
     *
     * @return HtmlString
     */
    function csrf_field()
    {
        return new HtmlString('<input type="hidden" name="_token" value="' . csrf_token() . '">');
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get the CSRF token value.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    function csrf_token()
    {
        /** @var \Illuminate\Contracts\Session\Session $session */
        $session = app('session');

        if (isset($session)) {
            return $session->token();
        }

        throw new RuntimeException('Application session store not set.');
    }
}

if (!function_exists('logger')) {
    /**
     * Log a debug message to the logs.
     *
     * @param null $message
     * @param array $context
     *
     * @return Log|null
     */
    function logger($message = null, array $context = [])
    {
        if (is_null($message)) {
            return app('log');
        }

        return app('log')->debug($message, $context);
    }
}

if (!function_exists('method_field')) {
    /**
     * Generate a form field to spoof the HTTP verb used by forms.
     *
     * @param $method
     *
     * @return HtmlString
     */
    function method_field($method)
    {
        return new HtmlString('<input type="hidden" name="_method" value="' . $method . '" />');
    }
}

if (!function_exists('mix')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param string $path
     * @param string $manifestDirectory
     *
     * @return HtmlString
     *
     * @throws Exception
     */
    function mix($path, $manifestDirectory = '')
    {
        static $manifest;

        if (!starts_with($path, '/')) {
            $path = "/{$path}";
        }

        if ($manifestDirectory && !starts_with($manifestDirectory, '/')) {
            $manifestDirectory = "/{$manifestDirectory}";
        }

        if (file_exists(public_path($manifestDirectory . '/hot'))) {
            return new HtmlString(url() . ":8080{$path}");
        }

        if (!$manifest) {
            if (!file_exists($manifestPath = public_path($manifestDirectory . '/mix-manifest.json'))) {
                throw new Exception('The Mix manifest does not exist.');
            }

            $manifest = json_decode(file_get_contents($manifestPath), true);
        }

        if (!array_key_exists($path, $manifest)) {
            throw new Exception(
                "Unable to locate Mix file: {$path}. Please check your " .
                'webpack.mix.js output paths and try again.'
            );
        }

        return new HtmlString($manifestDirectory . $manifest[$path]);
    }
}

if (!function_exists('old')) {
    /**
     * Retrieve an old input item.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    function old($key = null, $default = null)
    {
        return app('request')->old($key, $default);
    }
}

if (!function_exists('public_path')) {
    /**
     * Get the path to the public folder.
     *
     * @param string $path
     *
     * @return string
     */
    function public_path($path = '')
    {
        return app()->basePath() . '/public' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);;
    }
}

if (!function_exists('redirectWithSession')) {
    /**
     * Get an instance of the redirector.
     *
     * @param  string|null $to
     * @param  int $status
     * @param  array $headers
     * @param  bool $secure
     *
     * @return \Laravel\Lumen\Http\Redirector|\Illuminate\Http\RedirectResponse
     */
    function redirectWithSession($to = null, $status = 302, $headers = [], $secure = null)
    {
        $redirector = new CosmicVelocity\LumenHelpers\Http\Redirector(app());

        if (is_null($to)) {
            return $redirector;
        }

        return $redirector->to($to, $status, $headers, $secure);
    }
}
