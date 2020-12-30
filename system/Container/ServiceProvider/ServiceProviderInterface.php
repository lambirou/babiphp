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

namespace BabiPHP\Container\ServiceProvider;

use BabiPHP\Container\ContainerAwareInterface;

interface ServiceProviderInterface extends ContainerAwareInterface
{
    /**
     * Returns a boolean if checking whether this provider provides a specific
     * service or returns an array of provided services if no argument passed.
     *
     * @param  string $service
     * @return boolean|array
     */
    public function provides($service = null);
    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register();
}
