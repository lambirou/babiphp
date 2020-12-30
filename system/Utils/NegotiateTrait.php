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

use BabiPHP\Negotiation\AbstractNegotiator;

/**
 * Utilities used by middlewares that use a negotiator.
 */
trait NegotiateTrait
{
    /**
     * Returns the best value of a header.
     *
     * @param string             $accept     The header to negotiate
     * @param AbstractNegotiator $negotiator
     * @param array              $priorities
     *
     * @return string|null
     */
    private function negotiateHeader($accept, AbstractNegotiator $negotiator, array $priorities)
    {
        if (empty($accept) || empty($priorities)) {
            return;
        }

        try {
            $best = $negotiator->getBest($accept, $priorities);
        } catch (\Exception $exception) {
            return;
        }

        if ($best) {
            return $best->getValue();
        }
    }
}
