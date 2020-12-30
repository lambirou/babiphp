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
|--------------------------------------------------------------------------------------------|
| Database Configuration : Réglages SQL - Votre hébergeur doit vous fournir ces informations |
|--------------------------------------------------------------------------------------------|
*/

// Databases availables
Config::set('database.databases', array(
		'local' => array(
			'driver' 		=> 'mysql',
			'persistent' 	=> true,
			'host' 			=> 'localhost',
			'port' 			=> '3306',
			'name' 			=> '',
			'user' 			=> '',
			'pass' 			=> '',
			'charset'		=> 'utf8',
			'prefix' 		=> ''
		)
	)
);

// Default Database
Config::set('database.default', 'local');