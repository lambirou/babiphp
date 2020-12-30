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

use \BabiPHP\Config\Config;

/*
|--------------------------------------------------------------------------
| Application Auth Enable
|--------------------------------------------------------------------------
*/
Config::set('app.auth.enable', false);

/*
|--------------------------------------------------------------------------
| Application Auth name
|--------------------------------------------------------------------------
*/
Config::set('app.auth_name', '__bp_auth_user');

/*
|--------------------------------------------------------------------------
| Application Auth Roles
|--------------------------------------------------------------------------
*/
Config::set('app.auth_roles', array(
	'anonym' => 'ROLE_ANONYM',
    'user' => 'ROLE_USER',
    'admin' => 'ROLE_ADMIN',
    'super_admin' => 'ROLE_SUPER_ADMIN'
));

/*
|--------------------------------------------------------------------------
| Application Access Control Roles
|--------------------------------------------------------------------------
*/
Config::set('app.access_control', array(
	'ROLE_ANONYM' => 'admin',
	'ROLE_USER' => '*',
	'ROLE_ADMIN' => '*'
));

/*
|--------------------------------------------------------------------------
| Application Auth Admin Roles
| 
| ROLE_ANONYM, ROLE_USER, ROLE_ADMIN
|--------------------------------------------------------------------------
*/
Config::set('app.admin_role', ['ROLE_ADMIN']);

/*
|--------------------------------------------------------------------------
| Application Auth Encoder Algorythm "default = sha256"
|--------------------------------------------------------------------------
*/
Config::set('app.auth_encoder', 'sha256');

/*
|--------------------------------------------------------------------------
| Application Auth Encryption Key
|--------------------------------------------------------------------------
*/
Config::set('app.auth_key', 'DYsfqqghshh5+45sh4h=4h6hykyk--466FFqD8çCOy9Uuçgggf675FHFHxfs2+=ni0FgaC9mi');

//-------------------------------------------------------------------------
