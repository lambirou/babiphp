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

if (version_compare(PHP_VERSION, '7.1.3') === -1) {
    $version = explode('-', PHP_VERSION);
    echo 'This version of BabiPHP requires at least PHP 7.1.3. ';
    echo 'You are currently running ' . $version[0] . '. Please update your PHP version.';
    return;
}

define('BABIPHP_START', microtime(true));
define('ROOT', dirname(dirname(__FILE__)));
define('BASEPATH', '/system/');
define('WEBROOT', '/public/');
define('PUBLICPATH', ROOT . WEBROOT);
define('APPPATH', ROOT . '/src/');
define('CONFIG', ROOT . '/config/');
define('RESOURCES', ROOT . '/resources/');

require ROOT . '/vendor/autoload.php';

new \BabiPHP\Core\Application();
