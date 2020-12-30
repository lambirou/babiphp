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

class TrailingSlash implements MiddlewareInterface
{
    /**
     * @var bool Add or remove the slash
     */
    private $trailingSlash;

    /**
     * @var bool Returns a redirect response or not
     */
    private $redirect = false;

    /**
     * Configure whether add or remove the slash.
     */
    public function __construct(bool $trailingSlash = false)
    {
        $this->trailingSlash = $trailingSlash;
    }
    /**
     * Whether returns a 301 response to the new path.
     */
    public function redirect(bool $redirect = true)
    {
        $this->redirect = $redirect;
        return $this;
    }

    /**
     * Process a request and return a response.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $base_path = $request->getBasePath();
        $path = $uri->getPath();

        if (rtrim($path, '/') !== $base_path) {
            $path = $this->normalize($path);
        }

        if ($this->redirect && ($uri->getPath() !== $path)) {
            return Factory::createResponse(301)->withHeader('Location', (string) $uri->withPath($path));
        }

        $response = $handler->handle($request->withUri($uri->withPath($path)));

        return $response;
    }

    /**
     * Normalize the trailing slash.
     */
    private function normalize(string $path)
    {
        if ($path === '') {
            return '/';
        }

        if (strlen($path) > 1) {
            if ($this->trailingSlash) {
                return rtrim($path, '/');
            } else {
                if (substr($path, -1) !== '/' && !pathinfo($path, PATHINFO_EXTENSION)) {
                    return $path.'/';
                }
            }
        }

        return $path;
    }
}