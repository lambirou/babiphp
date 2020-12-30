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

namespace BabiPHP\Core;

use \BabiPHP\Config\Config;
use \BabiPHP\Http\ServerRequest;
use \BabiPHP\Core\Renderer;
use \BabiPHP\Database\ConnectionManager;

use function BabiPHP\Http\send;

class Application
{
	/**
	 * @const string BabiPHP Version
	 */
	const VERSION = '1.3.1';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Framework version
		define('BP_VERSION', self::VERSION);

		// Autoload de fichier insdispensable
		Config::init();

		require CONFIG . 'Config.php';
		require CONFIG . 'Auth.php';
		require CONFIG . 'Database.php';

		// Definition des configurations de base pour la connexion à la base de donnée
		$manager = ConnectionManager::getInstance();
		$manager->addMultiConfiguration(Config::get('database.databases'));
		$manager->setCurrentConfigName(Config::get('database.default'));

		$this->run();
	}

	/**
	 * Lancer l'application
	 */
	public function run()
	{
		Renderer::initialize();

		$dispatcher = new Dispatcher();

		if (Config::get('error.handler')) {
			$dispatcher->pipe(new \BabiPHP\Middleware\ErrorHandler());
		}

		if (Config::get('firewall.ip.enable')) {
			$white_list = Config::get('firewall.ip.whitelist');
			$black_list = Config::get('firewall.ip.blacklist');
			$allowed_all = Config::get('firewall.ip.default_status');

			$dispatcher->pipe(
				(new \BabiPHP\Middleware\Firewall($allowed_all))
					->whitelist($white_list)
					->blacklist($black_list)
			);
		}

		if (Config::get('system.redirect.https')) {
			$dispatcher->pipe((new \BabiPHP\Middleware\Https())->includeSubdomains());
		}

		if (Config::get('system.redirect.www')) {
			$dispatcher->pipe(new \BabiPHP\Middleware\Www(true));
		}

		$dispatcher->pipe((new \BabiPHP\Middleware\TrailingSlash(true))->redirect(true));

		// Format Negotiator Middleware
		$dispatcher->pipe(new \BabiPHP\Middleware\ContentType());

		// BabiPHP Debugbar Middleware
		$dispatcher->pipe(new \BabiPHP\Middleware\Debugbar());

		if (Config::get('maintenance.enable')) {
			$retry_after = Config::get('maintenance.retry_after');
			$dispatcher->pipe((new \BabiPHP\Middleware\Shutdown())->retryAfter($retry_after));
		}

		// BabiPHP App Middleware
		$dispatcher->pipe(new \BabiPHP\Middleware\App());

		$request = ServerRequest::getInstance();
		$response = $dispatcher->dispatch($request);

		send($response);
	}
}
