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

namespace BabiPHP\Routing\Rule;

use BabiPHP\Routing\Route;
use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * A rule for special matching logic on individual routes.
 *
 */
class Special implements RuleInterface
{
    /**
     *
     * Invokes the special matching logic on each individual Route, if any.
     *
     * @param ServerRequestInterface $request The HTTP request.
     *
     * @param Route $route The route.
     *
     * @return bool True on success, false on failure.
     *
     */
    public function __invoke(ServerRequestInterface $request, Route $route)
    {
        $special = $route->special;
        if (! $special) {
            return true;
        }

        return (bool) $special($request, $route);
    }
}
