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

namespace BabiPHP\Routing\Helper;

use BabiPHP\Routing\Exception\RouteNotFound;
use BabiPHP\Routing\Generator;

/**
 *
 * Generic Route Helper class
 *
 */
class Route
{
    /**
     *
     * The Generator object used by the RouteContainer
     *
     * @var Generator
     *
     */
    protected $generator;

    /**
     *
     * Constructor.
     *
     * @param Generator $generator The generator object to use
     *
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     *
     * Returns the generated raw route
     *
     * @param string $name The name of the route to lookup.
     *
     * @param array $data The data to pass into the route.
     *
     * @return string The results of calling _Generator::generate_.
     *
     * @throws RouteNotFound When the route cannot be found.
     *
     */
    public function __invoke($name, array $data = [])
    {
        return $this->generator->generate($name, $data);
    }
}