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

namespace BabiPHP\Container;

use \Psr\Container\ContainerInterface;

interface ContainerAwareInterface
{
    /**
     * @param ContainerInterface $container
     * @return mixed
     */
    public function setContainer(ContainerInterface $container);

    /**
     * @return mixed
     */
    public function getContainer();
}
