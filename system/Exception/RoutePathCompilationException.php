<?php
/**
 * BabiPHP : The Simple and Fast Development Framework (http://babiphp.org)
 * Copyright (c) BabiPHP. (http://babiphp.org)
 *
 * Licensed under The GNU General Public License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) BabiPHP. (http://babiphp.org)
 * @link          http://babiphp.org BabiPHP Project
 * @package       system.BASEPATH.exception
 * @since         BabiPHP v 0.8.5
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * Not edit this file
 */

    namespace BabiPHP\Exception;

    use Exception;
    use BabiPHP\Routing\Route;
    use RuntimeException;

    /**
     * RoutePathCompilationException
     *
     * Exception used for when a route's path fails to compile
     */
    class RoutePathCompilationException extends RuntimeException implements BabiPHPExceptionInterface
    {

        /**
         * Constants
         */

        /**
         * The exception message format
         *
         * @type string
         */
        const MESSAGE_FORMAT = 'Route failed to compile with path "%s".';

        /**
         * The extra failure message format
         *
         * @type string
         */
        const FAILURE_MESSAGE_TITLE_FORMAT = 'Failed with message: "%s"';


        /**
         * Properties
         */

        /**
         * The route that failed to compile
         *
         * @type Route
         */
        protected $route;


        /**
         * Methods
         */

        /**
         * Create a RoutePathCompilationException from a route
         * and an optional previous exception
         *
         * @param Route $route          The route that failed to compile
         * @param Exception $previous   The previous exception
         * @return RoutePathCompilationException
         */
        public static function createFromRoute(Route $route, Exception $previous = null)
        {
            $error = (null !== $previous) ? $previous->getMessage() : null;
            $code  = (null !== $previous) ? $previous->getCode() : null;

            $message = sprintf(static::MESSAGE_FORMAT, $route->getPath());
            $message .= ' '. sprintf(static::FAILURE_MESSAGE_TITLE_FORMAT, $error);

            $exception = new static($message, $code, $previous);
            $exception->setRoute($route);

            return $exception;
        }

        /**
         * Gets the value of route
         *
         * @sccess public
         * @return Route
         */
        public function getRoute()
        {
            return $this->route;
        }

        /**
         * Sets the value of route
         *
         * @param Route The route that failed to compile
         * @sccess protected
         * @return RoutePathCompilationException
         */
        protected function setRoute(Route $route)
        {
            $this->route = $route;

            return $this;
        }
    }
