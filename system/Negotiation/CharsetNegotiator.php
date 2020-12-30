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

namespace BabiPHP\Negotiation;

class CharsetNegotiator extends AbstractNegotiator
{
    /**
     * {@inheritdoc}
     */
    protected function acceptFactory($accept)
    {
        return new AcceptCharset($accept);
    }
}
