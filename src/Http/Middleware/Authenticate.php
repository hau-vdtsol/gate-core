<?php

namespace GateGem\Core\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use GateGem\Core\Facades\Core;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $response = parent::handle($request, $next, ...$guards);
        // Like: users.index
        $route = $request->route()->getName();
        // Hasn't permission
        if (!Core::checkPermission($route)) {
            return abort(403);
        }
        return $response;
    }
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('core.login');
        }
    }
}
