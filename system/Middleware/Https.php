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
use \Psr\Http\Message\UriInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;
use \BabiPHP\Http\Utils\Factory;

/**
 * PSR-15 middleware to redirect to https and adds the Strict-Transport-Security header
 */
class Https implements MiddlewareInterface
{
    const HEADER = 'Strict-Transport-Security';

    /**
     * @param int One year by default
     */
    private $maxAge = 31536000;

    /**
     * @param bool Whether add the preload directive or not
     */
    private $preload = false;

    /**
     * @param bool Whether include subdomains
     */
    private $includeSubdomains = false;

    /**
     * @param bool Whether check the headers "X-Forwarded-Proto: https" or "X-Forwarded-Port: 443"
     */
    private $checkHttpsForward = false;

    /**
     * Configure the max-age HSTS in seconds.
     *
     * @param int $maxAge
     *
     * @return self
     */
    public function maxAge($maxAge)
    {
        $this->maxAge = $maxAge;

        return $this;
    }

    /**
     * Configure the includeSubDomains HSTS directive.
     *
     * @param bool $includeSubdomains
     *
     * @return self
     */
    public function includeSubdomains($includeSubdomains = true)
    {
        $this->includeSubdomains = $includeSubdomains;

        return $this;
    }

    /**
     * Configure the preload HSTS directive.
     *
     * @param bool $preload
     *
     * @return self
     */
    public function preload($preload = true)
    {
        $this->preload = $preload;

        return $this;
    }

    /**
     * Configure whether check the following headers before redirect:
     * X-Forwarded-Proto: https
     * X-Forwarded-Port: 443.
     *
     * @param bool $checkHttpsForward
     *
     * @return self
     */
    public function checkHttpsForward($checkHttpsForward = true)
    {
        $this->checkHttpsForward = $checkHttpsForward;

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

        if (strtolower($uri->getScheme()) !== 'https') {
            if ($this->mustRedirect($request)) {
                return Factory::createResponse(301)
                    ->withHeader('Location', (string) self::withHttps($uri));
            }

            $request = $request->withUri(self::withHttps($uri));
        }

        $response = $handler->handle($request);

        if (!empty($this->maxAge)) {
            $header = sprintf(
                'max-age=%d%s%s',
                $this->maxAge,
                $this->includeSubdomains ? ';includeSubDomains' : '',
                $this->preload ? ';preload' : ''
            );
            $response = $response->withHeader(self::HEADER, $header);
        }

        if ($response->hasHeader('Location')) {
            $location = Factory::createUri($response->getHeaderLine('Location'));

            if ($location->getHost() === '' || $location->getHost() === $uri->getHost()) {
                return $response->withHeader('Location', (string) self::withHttps($location));
            }
        }

        return $response;
    }

    /**
     * Check whether the request must be redirected or not.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    private function mustRedirect(ServerRequestInterface $request)
    {
        return !$this->checkHttpsForward || (
            $request->getHeaderLine('X-Forwarded-Proto') !== 'https' &&
            $request->getHeaderLine('X-Forwarded-Port') !== '443'
        );
    }

    /**
     * Converts a http uri to https.
     *
     * @param UriInterface $uri
     *
     * @return UriInterface
     */
    private static function withHttps(UriInterface $uri)
    {
        return $uri->withScheme('https')->withPort(443);
    }
}