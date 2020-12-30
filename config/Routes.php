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
 */

use \BabiPHP\Routing\Router;

Router::get('blog.home', '/', 'blog@index');
Router::route('blog.portfolio', '/portfolio/{name}', 'blog@portfolio');
Router::route('blog.post', '/post', 'blog@post')->allows('GET');
Router::post('blog.ajax', '/ajax', 'blog@ajax')->allows(['GET']);
