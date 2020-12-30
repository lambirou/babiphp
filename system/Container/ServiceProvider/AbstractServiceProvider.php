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

use BabiPHP\Container\ContainerAwareTrait;

abstract class AbstractServiceProvider implements ServiceProviderInterface
{
    use ContainerAwareTrait;
    
    /**
     * @var array
     */
    protected $provides = [];

    /**
     * {@inheritdoc}
     */
    public function provides($alias = null)
    {
        if (! is_null($alias)) {
            return (in_array($alias, $this->provides));
        }
        return $this->provides;
    }

    protected function bind(...$arguments){
        return $this->getContainer()->bind(...$arguments);
    }

    protected function share(...$arguments){
        return $this->getContainer()->share(...$arguments);
    }
}
