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
use \BabiPHP\Container\ServiceProvider\BootableServiceProviderInterface;
use \BabiPHP\Container\ServiceProvider\ServiceProviderInterface;

class ServiceProviderContainer extends Container implements ContainerInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $booted = false;

    protected $providers = [];

    protected $provides = [];

    public function addServiceProvider($provider)
    {
        if ($this->getContainer()->has($provider)) {
            $instance = $this->getContainer()->get($provider);
        } else {
            $instance = new $provider();
        }

        $this->providers[$provider] = $instance;

        if ($instance instanceof ContainerAwareInterface) {
            $instance->setContainer($this->getContainer());
        }

        if ($this->booted && $instance instanceof BootableServiceProviderInterface) {
            $instance->boot();
        }

        if ($instance instanceof ServiceProviderInterface) {
            foreach ($instance->provides() as $service) {
                $this->provides[$service] = $provider;
            }
            return $this;
        }

        throw new \InvalidArgumentException(
            'A service provider must be a fully qualified class name or instance ' .
            'of ('.ServiceProviderInterface::class.')'
        );
    }

    public function bootServiceProviders(){

        foreach($this->providers as $provider){
            if ($provider instanceof BootableServiceProviderInterface) {
                $provider->boot();
            }
        }

        $this->booted = true;
    }

    public function get(string $id = '')
    {
        if (!$this->has($id)) {
            throw new NotFoundException('Alias (' . $id . ') is not an existing class and therefore cannot be resolved');
        }

        $provider = $this->provides[$id];
        $instance = $this->providers[$provider];

        //register into the main container, this instance will never be called again so we could destroy it if we wanted to @TODO
        $instance->register();

        //should be registered so lets go back to the main container and fetch it
        return $this->getContainer()->get($id);
    }

    public function has(string $id = '') : bool
    {
        return array_key_exists($id, $this->provides);
    }
}
