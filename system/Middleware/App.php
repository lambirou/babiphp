<?php

/**
 * BabiPHP : The flexible PHP Framework
 *
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) BabiPHP.
 * @link          https://github.com/lambirou/babiphp BabiPHP Project
 * @license       MIT
 *
 * Not edit this file
 */

namespace BabiPHP\Middleware;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;
use \BabiPHP\Http\Utils\Factory;
use \BabiPHP\Routing\RouterContainer;
use \BabiPHP\Routing\Router;
use \BabiPHP\Routing\Route;
use \BabiPHP\Config\Config;
use \BabiPHP\Misc\Utils;
use \BabiPHP\Auth\Authentication;
use \BabiPHP\Core\Renderer;

/**
 * BabiPHP App PSR-15 middleware
 */
class App implements MiddlewareInterface
{
    /**
     * @var \BabiPHP\Routing\RouterContainer
     */
    private $routerContainer;

    /**
     * @var array
     */
    private $access_control;

    /**
     * @var bool
     */
    private $url_suffix;

    /**
     * @var string
     */
    private $url_suffix_extension;

    /**
     * Constructor
     *
     * @param string $base_path
     */
    public function __construct()
    {
        $this->routerContainer = new RouterContainer();

        $map = $this->routerContainer->getMap();
        $router = new Router($map);

        // Include routes configurations
        require CONFIG . 'Routes.php';

        $this->access_control = Config::get('app.access_control');
        $this->url_suffix = Config::get('system.suffix.enable');
        $this->url_suffix_extension = Config::get('system.suffix.extension');
    }

    /**
     * Process a request and return a response.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        if ($this->url_suffix) {
            $matcher_request = $this->normalizeSuffix($request);
        } else {
            $matcher_request = clone $request;
        }

        $this->routerContainer->setBasePath($request->getBasePath());

        $matcher = $this->routerContainer->getMatcher();
        $route = $matcher->match($matcher_request);
        $response = $handler->handle($request);

        if (!$route) {
            $failedRoute = $matcher->getFailedRoute();
            $failedRule = $failedRoute->failedRule;

            if ($failedRule == 'BabiPHP\Routing\Rule\Allows') {
                $response = $response->notAllowed($failedRoute->allows);
            } else if ($failedRule == 'BabiPHP\Routing\Rule\Accepts') {
                $response = $response->sendError(406);
            } else {
                $response = $response->notFound('The page you are looking for could not be found.');
            }
        } else {
            $request = $this->parseRequest($request, $route);
            $controller = $this->loadController($request, $response);
            $action = $request->getAction();
            $attributes = $request->getAttributes();

            if (method_exists($controller, $action)) {
                $response = call_user_func_array([$controller, $action], [$request, $response, $attributes]);
                $response = $this->accessControl($request, $response);
            } else {
                $response = $response->notFound('The page you are looking for could not be found.');
            }
        }

        return $response;
    }

    /**
     * Normalize url suffix
     *
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    private function normalizeSuffix(ServerRequestInterface $request)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        $extension = '.' . pathinfo($path, PATHINFO_EXTENSION);
        $suffix = $this->url_suffix_extension;

        if ($extension === $suffix) {
            $path = substr($path, 0, -strlen($suffix));

            $uri = $uri->withPath($path);
        }

        return $request->withUri($uri, true);
    }

    /**
     * Permet de parser la requête
     *
     * @param ServerRequestInterface $request
     * @param Route $route
     * @return ServerRequestInterface
     */
    private function parseRequest(ServerRequestInterface $request, Route $route)
    {
        $route_map = explode('@', $route->handler);
        $ctrl_map = array_map('ucfirst', explode('/', $route_map[0]));

        $controller = (count($ctrl_map) > 1) ? implode(DIRECTORY_SEPARATOR, $ctrl_map) : $ctrl_map[0];
        $action = isset($route_map[1]) ? $route_map[1] : 'index';

        $request->setRouteName($route->name);
        $request->setController($controller);
        $request->setAction($action);

        foreach ($route->attributes as $key => $val) {
            $request = $request->withAttribute($key, $val);
        }

        return $request;
    }

    /**
     * Permet de charger le controlleur demandé
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return Controller
     */
    private function loadController(ServerRequestInterface $request, ResponseInterface $response)
    {
        $name = $request->getController();
        $container = Renderer::container();

        $container->bind('request', $request);
        $container->bind('response', $response);

        if (file_exists(APPPATH . 'Controller/' . $name . '.php')) {
            $class = 'App\\Controller\\' . str_replace('/', '\\', $name);
            $controller = new $class($container);
        } else {
            $controller = new \stdClass();
        }

        return $controller;
    }

    /**
     * Permet de définir less règles de sécurité du pare-feu
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    private function accessControl(ServerRequestInterface $request, ResponseInterface $response)
    {
        $access_stack = [];
        $i = 0;

        foreach ($this->access_control as $role => $ressources) {
            $access_stack[$i]['role'] = $role;
            $access_stack[$i]['ressource'] = $ressources;
            $i++;
        }

        foreach ($access_stack as $key => $access) {
            $allowed_roles = explode('|', $access['role']);
            $ressources = explode('|', $access['ressource']);

            if (in_array($request->getController(), $ressources)) {
                $user_role = Authentication::getInstance()->getRole();

                if (!in_array($user_role, $allowed_roles)) {
                    $response = $response->forbidden();
                }
            }
        }

        return $response;
    }
}