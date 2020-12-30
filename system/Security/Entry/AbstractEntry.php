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
 * Firewall entry model
 *
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
abstract class AbstractEntry implements EntryInterface
{
    /**
     * @var string $template Entry type pattern
     */
    protected $template;

    /**
     * Constructor
     *
     * @param string $entry Entry type pattern
     */
    public function __construct($entry)
    {
        $this->template = $entry;
    }
}