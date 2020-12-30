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

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;
use \BabiPHP\Http\Utils\Factory;
use \InvalidArgumentException;
use \LogicException;

/**
 * A PSR-15 request handler.
 */
class Dispatcher implements RequestHandlerInterface
{
    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares;

     /**
     * @var callable|null
     */
    private $resolver;

    /**
     * @var integer
     */
    private $index = 0;

    /**
     * Constructor
     * 
     * @param callable $resolver Converts queue entries to middleware instances.
     */
    public function __construct(callable $resolver = null)
    {
        if ($resolver === null) {
            $resolver = function ($entry) {
                return $entry;
            };
        }

        $this->resolver = $resolver;
    }

    /**
     * Permet d'enregistrer un nouveau middleware
     *
     * @param callable|MiddlewareInterface $middleware
     * @return void
     */
    public function pipe($middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Permet d'orienter la requête vers le bon controller et la bonne methode
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request)
    {
        return $this->handle($request);
    }
    
    /**
     * Permet d'exécuter les middlewares
     *
     * @param ServerRequestInterface $request
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->dispatch($request);
    }

    /**
     * Handles the current entry in the middleware queue and advances.
     *
     * @param ServerRequestInterface $request The request.
     *
     * @return ResponseInterface
     *
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (isset($this->middlewares[$this->index])) {
            $entry = $this->middlewares[$this->index];
            $middleware = call_user_func($this->resolver, $entry);

            $this->index++;

            if ($middleware instanceof MiddlewareInterface) {
                return $middleware->process($request, $this);
            }

            if ($middleware instanceof Closure) {
                return self::createMiddlewareFromClosure($middleware);
            }

            throw new InvalidArgumentException(sprintf('No valid middleware provided (%s)', is_object($middleware) ? get_class($middleware) : gettype($middleware)));
        } else {
            return Factory::createResponse();
        }
    }
    
    /**
     * Create a middleware from a closure
     */
    private static function createMiddlewareFromClosure(Closure $handler): MiddlewareInterface
    {
        return new class($handler) implements MiddlewareInterface {
            private $handler;
            public function __construct(Closure $handler)
            {
                $this->handler = $handler;
            }
            public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
            {
                return call_user_func($this->handler, $request, $next);
            }
        };
    }
}