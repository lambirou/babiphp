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

use BabiPHP\Negotiation\Exception\InvalidMediaType;

final class Accept extends BaseAccept implements AcceptHeader
{
    private $basePart;

    private $subPart;

    public function __construct($value)
    {
        parent::__construct($value);

        if ($this->type === '*') {
            $this->type = '*/*';
        }

        $parts = explode('/', $this->type);

        if (count($parts) !== 2 || !$parts[0] || !$parts[1]) {
            throw new InvalidMediaType();
        }

        $this->basePart = $parts[0];
        $this->subPart  = $parts[1];
    }

    /**
     * @return string
     */
    public function getSubPart()
    {
        return $this->subPart;
    }

    /**
     * @return string
     */
    public function getBasePart()
    {
        return $this->basePart;
    }
}
