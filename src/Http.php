<?php
namespace Yuyue8\TpSwooleMultiApp;

use think\Http as SwooleHttp;
use think\Middleware;
use think\Route;
use think\swoole\concerns\ModifyProperty;

class Http extends SwooleHttp
{
    use ModifyProperty;

    /** @var Middleware */
    protected static $middleware = [];

    /** @var Route */
    protected static $route = [];

    protected function loadMiddleware(): void
    {
        $appname = strtolower($this->getName());
        if (!isset(self::$middleware[$appname])) {
            parent::loadMiddleware();
            self::$middleware[$appname] = clone $this->app->middleware;
            $this->modifyProperty(self::$middleware[$appname], null);
        }

        $tempMiddleware = clone self::$middleware[$appname];
        $this->modifyProperty($tempMiddleware, $this->app);
        $this->app->instance('middleware', $tempMiddleware);
    }

    protected function loadRoutes(): void
    {
        $appname = strtolower($this->getName());
        if (!isset(self::$route[$appname])) {
            parent::loadRoutes();
            self::$route[$appname] = clone $this->app->route;
            $this->modifyProperty(self::$route[$appname], null);
            $this->modifyProperty(self::$route[$appname], null, 'request');
        }
    }

    protected function dispatchToRoute($request)
    {
        $appname = strtolower($this->getName());
        if (!isset(self::$route[$appname]) && $this->app->config->get('app.with_route', true)) {
            $this->loadRoutes();
        }
        if (isset(self::$route[$appname])) {
            $newRoute = clone self::$route[$appname];
            $this->modifyProperty($newRoute, $this->app);
            $this->app->instance('route', $newRoute);
        }

        return parent::dispatchToRoute($request);
    }
}
