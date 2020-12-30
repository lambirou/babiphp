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
 * Entry Interface
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
interface EntryInterface
{
    /**
     * Check in the template match the entry
     *
     * @param string $entry Template
     *
     * @return boolean
     */
    public static function match($entry);

    /**
     * Check if a string match the template
     *
     * @param string $entry
     *
     * @return boolean
     */
    public function check($entry);

    /**
     * Get all possible values in the list
     *
     * @return array
     */
    public function getMatchingEntries();
}