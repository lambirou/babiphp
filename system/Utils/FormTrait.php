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

use Psr\Http\Message\RequestInterface;

/**
 * Utilities used by middlewares that manipulate forms.
 */
trait FormTrait
{
    use StreamTrait;

    private $autoInsert = false;

    /**
     * Configure if autoinsert or not the inputs automatically.
     *
     * @param bool $autoInsert
     *
     * @return self
     */
    public function autoInsert($autoInsert = true)
    {
        $this->autoInsert = $autoInsert;

        return $this;
    }

    /**
     * Insert content into all POST forms.
     *
     * @param ResponseInterface $response
     * @param callable          $replace
     *
     * @return ResponseInterface
     */
    private function insertIntoPostForms(ResponseInterface $response, callable $replace)
    {
        $html = (string) $response->getBody();
        $html = preg_replace_callback('/(<form\s[^>]*method=["\']?POST["\']?[^>]*>)/i', $replace, $html, -1, $count);

        if (!empty($count)) {
            $body = self::createStream();
            $body->write($html);

            return $response->withBody($body);
        }

        return $response;
    }
}
