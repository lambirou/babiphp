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

use BabiPHP\Transformers\ResolverInterface;

/**
 * Trait to provide a resolver to load transformers.
 */
trait ResolverTrait
{
    /**
     * @var ResolverInterface|null
     */
    protected $resolver;

    /**
     * Load the resolver.
     *
     * @param ResolverInterface $resolver
     *
     * @return self
     */
    public function resolver(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;

        return $this;
    }
}
