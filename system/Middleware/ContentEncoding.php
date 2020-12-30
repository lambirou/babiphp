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
use BabiPHP\Negotiation\EncodingNegotiator;

class ContentEncoding implements MiddlewareInterface
{
    use Utils\NegotiationTrait;

    /**
     * @var array Available encodings
     */
    private $encodings = [
        'gzip',
        'deflate',
    ];

    /**
     * Define de available encodings.
     *
     * @param array|null $encodings
     */
    public function __construct(array $encodings = null)
    {
        if ($encodings !== null) {
            $this->encodings = $encodings;
        }
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
        if ($request->hasHeader('Accept-Encoding')) {
            $accept = $request->getHeaderLine('Accept-Encoding');
            $encoding = $this->negotiateHeader($accept, new EncodingNegotiator(), $this->encodings);

            if ($encoding === null) {
                return $handler->handle($request->withoutHeader('Accept-Encoding'));
            }

            return $handler->handle($request->withHeader('Accept-Encoding', $encoding));
        }

        return $handler->handle($request);
    }
}
