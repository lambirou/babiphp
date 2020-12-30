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
use \Psr\Http\Message\MessageInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;
use \BabiPHP\Http\Utils\Factory;
use \BabiPHP\Http\Utils\Helpers;
use \BabiPHP\Debug\Debugbar as MainDebugbar;

/**
 * PSR-15 middleware to show BabiPHP debugbar
 */
class Debugbar implements MiddlewareInterface
{
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
        $response = $handler->handle($request);
        
        $debugbar = new MainDebugbar($request);

        if (stripos($response->getHeaderLine('Content-Type'), 'text/html') === 0) {
            $html = (string) $response->getBody();

            if (!$request->isAjax()) {
                $html = self::injectHtml($html, $debugbar->renderHead(), '</head>');
            }
            
            $html = self::injectHtml($html, $debugbar->render(), '</body>');
            $body = Factory::createStream();
            $body->write($html);

            $response = self::fixContentLength($response->withBody($body));
        }

        return $response;
    }

    /**
     * Inject html code before a tag.
     *
     * @param string $html
     * @param string $code
     * @param string $before
     *
     * @return ResponseInterface
     */
    private static function injectHtml($html, $code, $before)
    {
        $pos = strripos($html, $before);
        if ($pos === false) {
            return $html.$code;
        }
        return substr($html, 0, $pos).$code.substr($html, $pos);
    }
    
    /**
     * Fix the Content-Length header
     * Used by middlewares that modify the body content
     *
     * @param MessageInterface $response
     *
     * @return MessageInterface
     */
    public static function fixContentLength(MessageInterface $response)
    {
        if (!$response->hasHeader('Content-Length')) {
            return $response;
        }

        if ($response->getBody()->getSize() !== null) {
            return $response->withHeader('Content-Length', (string) $response->getBody()->getSize());
        }

        return $response->withoutHeader('Content-Length');
    }
}