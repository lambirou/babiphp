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

use BabiPHP\Negotiation\Exception\InvalidLanguage;

final class AcceptLanguage extends BaseAccept implements AcceptHeader
{
    private $language;
    private $script;
    private $region;

    public function __construct($value)
    {
        parent::__construct($value);

        $parts = explode('-', $this->type);

        if (2 === count($parts)) {
            $this->language = $parts[0];
            $this->region   = $parts[1];
        } elseif (1 === count($parts)) {
            $this->language = $parts[0];
        } elseif (3 === count($parts)) {
            $this->language = $parts[0];
            $this->script   = $parts[1];
            $this->region   = $parts[2];
        } else {
            // TODO: this part is never reached...
            throw new InvalidLanguage();
        }
    }

    /**
     * @return string
     */
    public function getSubPart()
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getBasePart()
    {
        return $this->language;
    }
}
