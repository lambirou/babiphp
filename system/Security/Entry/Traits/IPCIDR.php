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

namespace BabiPHP\Security\Entry\Traits;

/**
 * IP CIDR Mask Trait
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
trait IPCIDR
{
    /**
     * {@inheritdoc}
     */
    public static function match($entry)
    {
        $entries = preg_split('/' . static::$separatorRegex .'/', $entry);

        if (count($entries) == 2) {
            $checkIp = static::matchIp($entries[0]);
            
            if ($checkIp && ($entries[1] >= 0) && ($entries[1] <= static::NB_BITS)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getParts()
    {
        $keys = array('ip', 'mask');
        $parts = array_combine($keys, preg_split('/'. self::$separatorRegex .'/', $this->template));

        $bin = str_pad(str_repeat('1', (int) $parts['mask']), self::NB_BITS, 0);

        $parts['mask'] = $this->long2ip($this->IPLongBaseConvert($bin, 2, 10));

        return $parts;
    }
}
