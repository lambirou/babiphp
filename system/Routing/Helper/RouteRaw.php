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
 * Generic Raw Route Helper class
 *
 */
class RouteRaw
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
     * Returns the Generator
     *
     * @param string $name The name of the route to lookup.
     *
     * @param array $data The data to pass into the route.
     *
     * @return string The results of calling _Generator::generateRaw_.
     *
     * @throws RouteNotFound When the route cannot be found.
     *
     */
    public function __invoke($name, array $data = [])
    {
        return $this->generator->generateRaw($name, $data);
    }
}