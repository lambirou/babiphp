<?php
/**
 * BabiPHP : The Simple and Fast Development Framework (http://babiphp.org)
 * Copyright (c) BabiPHP. (http://babiphp.org)
 *
 * Licensed under The GNU General Public License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) BabiPHP. (http://babiphp.org)
 * @author        Lambirou <lambirou225@gmail.com>
 * @link          http://babiphp.org BabiPHP Project
 * @since         BabiPHP v 0.3
 * @license       http://www.gnu.org/licenses/ GNU License
 *
 * Not edit this file
 *
 */

namespace BabiPHP\Session;

interface SessionInterface extends \ArrayAccess
{
    /**
     * Permet de récupérer une information depuis la session.
     *
     * @param string $key
     */
    public function get(string $key);

    /**
     * Permet de stocker une information en session.
     *
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value);

    /**
     * Permet de vérifier une information en session.
     *
     * @param string $key
     * @param $value
     */
    public function check(string $key);

    /**
     * Permet de supprimer une clef en session.
     *
     * @param string $key
     * @return mixed
     */
    public function delete(string $key);

    /**
     * Détruit complètement la session.
     *
     * @return mixed
     */
    public function destroy();

    public function offsetExists($offset);

    public function &offsetGet($offset);

    public function offsetSet($offset, $value);

    public function offsetUnset($offset);
}
