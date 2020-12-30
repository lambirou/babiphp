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

namespace BabiPHP\Core;

use \BabiPHP\Http\Utils\Factory;
use \BabiPHP\Http\Response;
use \BabiPHP\Container\Container;
use \BabiPHP\Container\ReflectionContainer;
use \BabiPHP\Config\Config;
use \BabiPHP\Exception\BpException;
use \BabiPHP\View\Compilers\BladeCompiler as ViewCompiler;
use \BabiPHP\View\Compilers\BladeDirectivesExtended;
use \BabiPHP\View\Engines\CompilerEngine as ViewCompilerEngine;
use \BabiPHP\View\FileViewFinder as ViewFinder;
use \BabiPHP\View\Factory as ViewFactory;
use \BabiPHP\View\Template as ViewTemplate;

class Renderer
{
    /**
     * @var \BabiPHP\Container\Container
     */
    private static $container;

    /**
     * @var string
     */
    private static $template;

    /**
     * @var string
     */
    private static $view;

    /**
     * @var array
     */
    private static $attributs = [];

    /**
     * @var \BabiPHP\View\Compilers\BladeCompiler
     */
    private static $compiler;

    /**
     * @var \BabiPHP\View\Factory
     */
    private static $factory;

    /**
     * Constructor
     */
    public static function initialize()
    {
        // Set Current template
        $view_template = Config::get('view.default_template');
        self::$template = ($view_template) ? $view_template : null;

        // View cache path
        $view_cache_path = RESOURCES . 'cache/view';

        // Create view cache path if not exist
        if (!is_dir($view_cache_path)) {
            mkdir($view_cache_path, 0777);
        }

        /* View compiler & directives extended */
        self::$compiler = new ViewCompiler($view_cache_path);
        $directives_extended = new BladeDirectivesExtended(self::$compiler);
        $directives_extended->boot();

        // Set View extension
        $view_extensions = Config::get('view.extensions');

        // View finder
        $finder = new ViewFinder([RESOURCES . 'views'], $view_extensions);
        $engine = new ViewCompilerEngine(self::$compiler);

        // View factory
        self::$factory = new ViewFactory($engine, $finder);

        self::bindingContainer();
    }

    /**
     * Permet de récuperer l'instance du Container
     *
     * @return BabiPHP\Container\Container
     */
    public static function container()
    {
        return self::$container;
    }

    /**
     * Permet de définir le template à utiliser
     *
     * @param string $template
     */
    public static function setTemplate(string $template)
    {
        self::$template = $template;
    }

    /**
     * Register a handler for custom directives.
     *
     * @param  string $name
     * @param  callable $handler
     * @return void
     */
    public static function addViewDirective(string $name, callable $handler)
    {
        self::$compiler->directive($name, $handler);
    }

    /**
     * Permet de générer la page demandée
     *
     * @param string $view
     * @param array $data
     * @return void
     */
    public static function make(string $view, array $data = [], string $template = null)
    {
        self::$view = $view;
        self::$attributs = array_merge(self::$attributs, $data);

        if ($template === null) {
            $template = self::$template;
        }

        if ($template !== null) {
            $content = self::$factory->make(self::$view, self::$attributs)->render();
            $view = $template;
        } else {
            $content = '';
            $view = self::$view;
        }

        ViewTemplate::setOutput('view_content', $content);

        return self::$factory->make($view, self::$attributs)->render();
    }

    /**
     * Permet de rendre une vue
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    public static function getView(string $view, array $data = [])
    {
        return self::$factory->make($view, array_merge(self::$attributs, $data))->render();
    }

    /**
     * Permet de lier des class au Container
     *
     * @return void
     */
    public static function bindingContainer()
    {
        self::$container = new Container();

        self::$container->bind('auth', \BabiPHP\Auth\Authentication::getInstance());
        self::$container->bind('session', new \BabiPHP\Session\Session());
        self::$container->bind('cookie', new \BabiPHP\Misc\Cookie(self::$container->getBasePath()));
    }
}
