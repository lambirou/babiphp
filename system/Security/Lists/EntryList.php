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
 * Firewall rules wrapper
 * 
 * @author Jérémy JOURDIN <jjourdin.externe@m6.fr>
 */
class EntryList
{
    /**
     * @var array $entries Registered entries
     */
    protected $entries;

    /**
     * @var boolean $matchingResponse Response for a matching entity
     */
    protected $matchingResponse;

    /**
     * @var array $matchingEntries Array of matching entries
     */
    protected $matchingEntries;

    /**
     * Constructor
     *
     * @param array   $list    Array with entries
     * @param boolean $trusted Whether or not this list is trusted by the firewall
     */
    public function __construct(array $list = array(), $trusted = false)
    {
        $this->entries = $list;
        $this->matchingResponse = ($trusted === true);
    }

    /**
     * Whether or not the Entry is allowed by this list
     *
     * @param string $entry Entry
     *
     * @return boolean|null TRUE = allowed, FALSE = rejected, NULL = not handled
     */
    public function isAllowed($entry)
    {
        foreach ($this->entries as $elmt) {
            if ($elmt->check($entry)) {
                return $this->matchingResponse;
            }
        }

        return null;
    }

    /**
     * Resolve all entries match the list
     *
     * @return array
     */
    public function getMatchingEntries()
    {
        if ($this->matchingEntries === null) {
            $this->matchingEntries = array();
            foreach ($this->entries as $entry) {
                $this->matchingEntries = array_merge($this->matchingEntries, $entry->getMatchingEntries());
            }
        }

        return $this->matchingEntries;
    }
}