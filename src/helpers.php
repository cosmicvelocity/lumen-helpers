<?php

if (!function_exists('asset')) {

    /**
     *
     * @param $path
     * @param null $secure
     * @return string
     */
    function asset($path, $secure = null)
    {
        return (new Laravel\Lumen\Routing\UrlGenerator(app()))->to($path, null, $secure);
    }

}

if (!function_exists('auth')) {

    /**
     *
     * @return Illuminate\Auth\Guard
     */
    function auth()
    {
        return app('Illuminate\Contracts\Auth\Guard');
    }

}

if (!function_exists('back')) {

    /**
     *
     * @param int $status
     * @param array $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    function back($status = 302, $headers = [])
    {
        return redirect()->back($status, $headers);
    }

}

if (!function_exists('csrf_field')) {

    /**
     *
     * @return \Illuminate\View\Expression
     */
    function csrf_field()
    {
        return new Illuminate\View\Expression('<input type="hidden" name="_token" value="' . csrf_token() . '">');
    }

}

if (!function_exists('logger')) {

    /**
     *
     * @param null $message
     * @param array $context
     * @return null|\Illuminate\Contracts\Logging\Log
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
     *
     * @param $method
     * @return \Illuminate\View\Expression
     */
    function method_field($method)
    {
        return new Illuminate\View\Expression('<input type="hidden" name="_method" value="' . $method . '">');
    }

}
