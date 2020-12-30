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

namespace BabiPHP\Utils;

/**
 * Utilities used by authentication middlewares.
 */
trait AuthenticationTrait
{
    private $users;
    private $realm = 'Login';

    /**
     * Defines de users.
     *
     * @param array $users [username => password]
     */
    public function __construct(array $users)
    {
        $this->users = $users;
    }

    /**
     * Set the realm value.
     *
     * @param string $realm
     *
     * @return self
     */
    public function realm($realm)
    {
        $this->realm = $realm;

        return $this;
    }
}
