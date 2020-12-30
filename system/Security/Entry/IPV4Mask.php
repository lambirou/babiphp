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

namespace BabiPHP\Security\Entry;

/**
 * IPV4 Subnet Mask Entry
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class IPV4Mask extends IPV4Range
{
    use Traits\IPMask;

    /**
     * @static string $separatorRegex Regular expression of separator
     */
    public static $separatorRegex = '(\s*)\/(\s*)';
}