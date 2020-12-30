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

namespace BabiPHP\Security\Lists;

/**
 * List wrapper
 *
 * @author Jérémy Jourdin <jjourdin.externe@m6.fr>
 */
class ListMerger
{
    /**
     * @var array $lists  Lists
     */
    protected $lists = array();

    /**
     * Add a list
     *
     * @param EntryList $list List
     * @param string    $name List name
     *
     * @return $this
     */
    public function addList(EntryList $entryList, $name)
    {
        $this->lists[$name] = $entryList;

        return $this;
    }

    /**
     * Check if a string is allowed
     *
     * @param string  $entry        String to compare
     * @param boolean $defaultState Default value
     *
     * @return boolean
     */
    public function isAllowed($entry, $defaultState)
    {
        $whited  = false;
        $blacked = false;

        if (is_array($this->lists)) {
            foreach ($this->lists as $list) {
                $allowed = $list->isAllowed($entry);
                if ($allowed !== null) {
                    if ($allowed) {
                        $whited = true;
                    } else {
                        $blacked = true;
                    }
                }
            }
        }

        return ($defaultState ? (!$blacked || $whited) : ($whited && !$blacked));
    }
}