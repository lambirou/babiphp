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
use \BabiPHP\Core\Renderer;
use \BabiPHP\Config\Config;

class ShutdownDefault
{
    /**
     * Execute the shutdown handler.
     *
     * @param ServerRequestInterface $request
     */

    public function __invoke(ServerRequestInterface $request)
    {
        $config = Config::get('custom_error.template');
        $page = new \stdClass;
        $page->title = 'Site under maintenance';
        
        echo (string) Renderer::make($config['view'][503], ['page'=>$page], $config['template']);
    }
}
