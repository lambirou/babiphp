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
 * A rule for HTTPS/SSL/TLS.
 *
 */
class Secure implements RuleInterface
{
    /**
     *
     * Checks that the Route `$secure` matches the corresponding server values.
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
        if ($route->secure === null) {
            return true;
        }

        $server = $request->getServerParams();
        $secure = $this->https($server) || $this->port443($server);
        return $route->secure == $secure;
    }

    /**
     *
     * Is HTTPS on?
     *
     * @param array $server The server params.
     *
     * @return bool
     *
     */
    protected function https($server)
    {
        return isset($server['HTTPS'])
            && $server['HTTPS'] == 'on';
    }


    /**
     *
     * Is the request on port 443?
     *
     * @param array $server The server params.
     *
     * @return bool
     *
     */
    protected function port443($server)
    {
        return isset($server['SERVER_PORT'])
            && $server['SERVER_PORT'] == 443;
    }
}
