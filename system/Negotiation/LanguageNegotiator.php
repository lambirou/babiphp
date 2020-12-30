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

class LanguageNegotiator extends AbstractNegotiator
{
    /**
     * {@inheritdoc}
     */
    protected function acceptFactory($accept)
    {
        return new AcceptLanguage($accept);
    }

    /**
     * {@inheritdoc}
     */
    protected function match(AcceptHeader $acceptLanguage, AcceptHeader $priority, $index)
    {
        if (!$acceptLanguage instanceof AcceptLanguage || !$priority instanceof AcceptLanguage) {
            return null;
        }

        $ab = $acceptLanguage->getBasePart();
        $pb = $priority->getBasePart();

        $as = $acceptLanguage->getSubPart();
        $ps = $priority->getSubPart();

        $baseEqual = !strcasecmp($ab, $pb);
        $subEqual  = !strcasecmp($as, $ps);

        if (($ab == '*' || $baseEqual) && ($as === null || $subEqual)) {
            $score = 10 * $baseEqual + $subEqual;

            return new Match($acceptLanguage->getQuality() * $priority->getQuality(), $score, $index);
        }

        return null;
    }
}
