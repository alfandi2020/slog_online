<?php

namespace App\Console\Commands;

use Illuminate\Routing\Route;
use Illuminate\Foundation\Console\RouteListCommand;

class RoutesCommand extends RouteListCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $headers = ['Method', 'URI', 'Name', 'Action', 'Middleware'];

    protected function getRouteInformation(Route $route)
    {
        $action = str_replace(['Barryvdh\Debugbar\Controllers\\', 'App\Http\Controllers\\'], '', $route->getActionName());
        return $this->filterRoute([
            'method' => implode('|', $route->methods()),
            'uri'    => $route->uri(),
            'name'   => $route->getName(),
            'action' => $action,
            'middleware' => $this->getMiddleware($route),
        ]);
    }

    protected function getRoutes()
    {
        $routes = collect($this->routes)->map(function ($route) {
            return $this->getRouteInformation($route);
        })->all();

        return array_filter($routes);
    }

    protected function filterRoute(array $route)
    {
        return $route;
    }
}
