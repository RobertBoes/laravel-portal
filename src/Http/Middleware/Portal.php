<?php


namespace Robertboes\LaravelPortal\Http\Middleware;


use Closure;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Robertboes\LaravelPortal\Exceptions\InvalidPortalConfig;

class Portal
{
    protected $routeName;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param null $routeName
     * @return mixed
     * @throws \Robertboes\LaravelPortal\Exceptions\InvalidPortalConfig
     */
    public function handle($request, Closure $next, $routeName = null)
    {
        $this->routeName = $routeName;

        if (!$portalConfig = $this->resolvePortalConfig()) {
            throw new InvalidPortalConfig("Portal route {$routeName} not found!");
        }

        $portalAction = $portalConfig->get('guest');

        if (Auth::guard($portalConfig->get('guard'))->check()) {
            $portalAction = $portalConfig->get('authenticated');
        }

        if ($portalAction) {
            $this->overwriteCurrentRoute($request->route(), $portalAction);
        }

        return $next($request);
    }

    protected function resolvePortalConfig()
    {
        if ($portalConfig = Arr::get($this->routes, $this->routeName)) {
            return collect($portalConfig);
        }

        return false;
    }

    protected function overwriteCurrentRoute(Route $route, string $action)
    {
        $routeAction = array_merge($route->getAction(), [
            'uses'       => $action,
            'controller' => $action,
        ]);

        $route->setAction($routeAction);
        $route->controller = false;
    }
}
