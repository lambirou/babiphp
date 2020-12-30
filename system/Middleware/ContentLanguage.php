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
use BabiPHP\Negotiation\LanguageNegotiator;

class ContentLanguage implements MiddlewareInterface
{
    use Utils\NegotiationTrait;

    /**
     * @var array Allowed languages
     */
    private $languages = [];

    /**
     * @var bool Use the path to detect the language
     */
    private $usePath = false;

    /**
     * @var bool Returns a redirect response or not
     */
    private $redirect = false;

    /**
     * Define de available languages.
     *
     * @param array $languages
     */
    public function __construct(array $languages)
    {
        $this->languages = $languages;
    }

    /**
     * Use the base path to detect the current language.
     *
     * @param bool $usePath
     *
     * @return self
     */
    public function usePath($usePath = true)
    {
        $this->usePath = $usePath;

        return $this;
    }

    /**
     * Whether returns a 302 response to the new path.
     * Note: This only works if usePath is true.
     *
     * @param bool $redirect
     *
     * @return self
     */
    public function redirect($redirect = true)
    {
        $this->redirect = (bool) $redirect;

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
        $language = $this->detectFromPath($uri->getPath());

        if ($language === null) {
            $language = $this->detectFromHeader($request);

            if ($this->redirect && $this->usePath) {
                $location = $uri->withPath(str_replace('//', '/', $language.'/'.$uri->getPath()));

                return Utils\Factory::createResponse(302)
                    ->withHeader('Location', (string) $location);
            }
        }

        $response = $handler->handle($request->withHeader('Accept-Language', $language));

        if (!$response->hasHeader('Content-Language')) {
            return $response->withHeader('Content-Language', $language);
        }

        return $response;
    }

    /**
     * Returns the format using the file extension.
     *
     * @param string $path
     *
     * @return null|string
     */
    private function detectFromPath($path)
    {
        if (!$this->usePath) {
            return;
        }

        $dirs = explode('/', ltrim($path, '/'), 2);
        $first = strtolower(array_shift($dirs));

        if (!empty($first) && in_array($first, $this->languages, true)) {
            return $first;
        }
    }

    /**
     * Returns the format using the Accept-Language header.
     *
     * @return null|string
     */
    private function detectFromHeader(ServerRequestInterface $request)
    {
        $accept = $request->getHeaderLine('Accept-Language');
        $language = $this->negotiateHeader($accept, new LanguageNegotiator(), $this->languages);

        if (empty($language)) {
            return isset($this->languages[0]) ? $this->languages[0] : '';
        }

        return $language;
    }
}
