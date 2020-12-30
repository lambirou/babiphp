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

final class Match
{
    /**
     * @var float
     */
    public $quality;

    /**
     * @var int
     */
    public $score;

    /**
     * @var int
     */
    public $index;

    public function __construct($quality, $score, $index)
    {
        $this->quality = $quality;
        $this->score   = $score;
        $this->index   = $index;
    }

    /**
     * @param Match $a
     * @param Match $b
     *
     * @return int
     */
    public static function compare(Match $a, Match $b)
    {
        if ($a->quality !== $b->quality) {
            return $a->quality > $b->quality ? -1 : 1;
        }

        if ($a->index !== $b->index) {
            return $a->index > $b->index ? 1 : -1;
        }

        return 0;
    }

    /**
     * @param array $carry reduced array
     * @param Match $match match to be reduced
     *
     * @return Match[]
     */
    public static function reduce(array $carry, Match $match)
    {
        if (!isset($carry[$match->index]) || $carry[$match->index]->score < $match->score) {
            $carry[$match->index] = $match;
        }

        return $carry;
    }
}
