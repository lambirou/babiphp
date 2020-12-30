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

namespace BabiPHP\Middleware;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;
use BabiPHP\Security\Firewall as IpFirewall;

/**
 * PSR-15 Middleware to provide IP filtering
 */
class Firewall implements MiddlewareInterface
{
    /**
     * @var array|null
     */
    private $whitelist = null;

    /**
     * @var array|null
     */
    private $blacklist;

    /**
     * @var string|null
     */
    private $ipAttribute;

    /**
     * @var bool
     */
    private $allowed_all = true;

    /**
     * Constructor. Set allowed all.
     *
     * @param bool $allowed_all
     */
    public function __construct(bool $allowed_all = true)
    {
        $this->allowed_all = $allowed_all;
    }

    /**
     * Set the whitelist.
     *
     * @param array $whitelist
     *
     * @return self
     */
    public function whitelist(array $whitelist)
    {
        $this->whitelist = $whitelist;

        return $this;
    }


    /**
     * Set ips not allowed.
     *
     * @param array $blacklist
     *
     * @return self
     */
    public function blacklist(array $blacklist)
    {
        $this->blacklist = $blacklist;

        return $this;
    }

    /**
     * Set the attribute name to get the client ip.
     *
     * @param string $ipAttribute
     *
     * @return self
     */
    public function ipAttribute($ipAttribute)
    {
        $this->ipAttribute = $ipAttribute;

        return $this;
    }

    /**
     * Process a request and return a response.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $ip = $this->getClientIp($request);

        if (empty($ip)) {
            $response = $response->forbidden();
        } else {
            $firewall = new IpFirewall();

            if (!empty($this->whitelist)) {
                $firewall->addList($this->whitelist, 'whitelist', true);
            }

            if (!empty($this->blacklist)) {
                $firewall->addList($this->blacklist, 'blacklist', false);
            }

            $firewall->setIpAddress($ip)->setDefaultState($this->allowed_all);

            if (!$firewall->handle()) {
                $response = $response->forbidden();
            }
        }

        return $response;
    }

    /**
     * Get the client ip.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    private function getClientIp(ServerRequestInterface $request)
    {
        $server = $request->getServerParams();

        if ($this->ipAttribute !== null) {
            return $request->getAttribute($this->ipAttribute);
        }

        return $request->getIp();
    }
}