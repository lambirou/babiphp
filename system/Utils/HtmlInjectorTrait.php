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

namespace BabiPHP\Utils;

use Psr\Http\Message\ResponseInterface;

/**
 * Utilities used by middlewares that inject html code in the responses.
 */
trait HtmlInjectorTrait
{
    use StreamTrait;

    /**
     * Inject some code just before any tag.
     *
     * @param ResponseInterface $response
     * @param string            $code
     * @param string            $tag
     *
     * @return ResponseInterface
     */
    private function inject(ResponseInterface $response, $code, $tag = 'body')
    {
        $html = (string) $response->getBody();
        $pos = strripos($html, "</{$tag}>");

        if ($pos === false) {
            $response->getBody()->write($code);

            return $response;
        }

        $body = self::createStream();
        $body->write(substr($html, 0, $pos).$code.substr($html, $pos));

        return $response->withBody($body);
    }
}
