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

namespace BabiPHP\Routing;

class Router
{
    /**
     * A route map.
     *
     * @var Map
     */
    protected static $map;

    /**
     * A proto-route for the map.
     *
     * @var Route
     */
    protected $route;

    /**
     * @var Self
     */
    private static $instance;

    /**
     * Constructor.
     */
    public function __construct(Map $map)
    {
        self::$map = $map;
        self::$instance = $this;
    }

    /**
     * @return Self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Router();
        }

        return self::$instance;
    }

    /**
     * Get a collection of route objects.
     *
     * @return Map
     */
    public static function map()
    {
        return self::$map;
    }

    /**
     *
     * Adds a generic route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public static function route($name, $path, $handler = null)
    {
        return self::$map->route($name, $path, $handler);
    }

    /**
     *
     * Adds a GET route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public static function get($name, $path, $handler = null)
    {
        return self::$map->get($name, $path, $handler);
    }

    /**
     *
     * Adds a POST route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public static function post($name, $path, $handler = null)
    {
        return self::$map->post($name, $path, $handler);
    }

    /**
     *
     * Adds a DELETE route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public static function delete($name, $path, $handler = null)
    {
        return self::$map->delete($name, $path, $handler);
    }

    /**
     *
     * Adds an OPTIONS route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public static function options($name, $path, $handler = null)
    {
        return self::$map->options($name, $path, $handler);
    }

    /**
     *
     * Adds a PATCH route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public static function patch($name, $path, $handler = null)
    {
        return self::$map->patch($name, $path, $handler);
    }

    /**
     *
     * Adds a PUT route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public static function put($name, $path, $handler = null)
    {
        return self::$map->put($name, $path, $handler);
    }

    /**
     *
     * Adds a HEAD route.
     *
     * @param string $name The route name.
     *
     * @param string $path The route path.
     *
     * @param mixed $handler The route leads to this handler.
     *
     * @return Route The newly-added route object.
     *
     */
    public static function head($name, $path, $handler = null)
    {
        return self::$map->head($name, $path, $handler);
    }

    /**
     *
     * Attaches routes to a specific path prefix, and prefixes the attached
     * route names.
     *
     * @param string $namePrefix The prefix for all route names being attached.
     *
     * @param string $pathPrefix The prefix for all route paths being attached.
     *
     * @param callable $callable A callable that uses the Map to add new
     * routes. Its signature is `function (\Aura\Router\Map $map)`; $this
     * Map instance will be passed to the callable.
     *
     * @return null
     *
     */
    public static function attach($namePrefix, $pathPrefix, callable $callable)
    {
        self::$map->attach($name, $path, $handler);
    }
}