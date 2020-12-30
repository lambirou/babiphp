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
 * IPV4 CIDR Mask Entry
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class IPV4CIDR extends IPV4Mask
{
    use Traits\IPCIDR;
}