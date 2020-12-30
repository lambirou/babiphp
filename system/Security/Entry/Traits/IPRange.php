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
 * IP Range Trait
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
trait IPRange
{
    /**
     * {@inheritdoc}
     */
    public static function match($entry)
    {
        $entries = preg_split('/' . static::$separatorRegex .'/', $entry);

        if (count($entries) == 2) {
            foreach ($entries as $ent) {
                if (!static::matchIp(trim($ent))) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function check($entry)
    {
        if (!self::matchIp($entry)) {
            return false;
        }
        
        $entryLong = $this->ip2long($entry);

        $range = $this->getRange();

        return (bool) $this->IPLongCompare($range['begin'], $entryLong, '<=') && $this->IPLongCompare($range['end'], $entryLong, '>=');
    }

    /**
     * Récupérer la plage d'IP valide sous forme d'entier
     *
     * @param boolean $long Retourner un entier plutot qu'une IP
     *
     * @return array
     */
    protected function getRange($long = true)
    {
        $parts = $this->getParts();
        $keys = array('begin', 'end');

        $parts['ip_start'] = $this->ip2long($parts['ip_start']);
        $parts['ip_end'] = $this->ip2long($parts['ip_end']);

        natsort($parts);

        $parts = array_combine($keys, array_values($parts));

        if (!$long) {
            $parts['begin'] = $this->long2ip($parts['begin']);
            $parts['end'] = $this->long2ip($parts['end']);
        }

        return $parts;
    }

    /**
     * Récupérer l'ip et le masque sous forme de tableau
     *
     * @return array
     */
    public function getParts()
    {
        $keys = array('ip_start', 'ip_end');

        return array_combine($keys, preg_split('/'. self::$separatorRegex .'/', $this->template));
    }

    /**
     * Calcul et retourne toutes les valeurs possible du range
     *
     * @return array
     */
    public function getMatchingEntries()
    {
        $limits = $this->getRange();
        $current = $limits['begin'];
        $entries[] = $this->long2ip($current);
        $entries = array();

        while ($current != $limits['end']) {
            $current = $this->IpLongAdd($current, 1);
            $entries[] = $this->long2ip($current);
        }

        return $entries;
    }
}