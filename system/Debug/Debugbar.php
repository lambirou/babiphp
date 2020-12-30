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

namespace BabiPHP\Debug;

use \Psr\Http\Message\ServerRequestInterface;
use \BabiPHP\Misc\Utils;
use \BabiPHP\Session\Session;
use \BabiPHP\Config\Config;
use \BabiPHP\Exception\BpException;

class Debugbar
{
	/**
	 * @var int
	 */
	private $startTime;

	/**
	 * @var \Psr\Http\Message\ServerRequestInterface
	 */
	private $request;

	/**
	 * @var string
	 */
	private $assets_path;

	/**
	 * @var int
	 */
	private static $Errors = 0;

	/**
	 * @var array
	 */
	private static $messages = [];

	/**
	 * @var array
	 */
	private static $exceptions = [];

	/**
	 * @var array
	 */
	private static $queries = [];

	/**
	 * @var boolean
	 */
	private static $activate = false;

	/**
	 * @var string
	 */
	private static $auth_name = '__bp_auth_user';

	/**
	 * @var self
	 */
	private static $instance;

	/**
	 * constructor
	 */
	public function __construct(ServerRequestInterface $request)
	{
		$this->request = $request;
		$this->setActivation();
		$this->assets_path = RESOURCES . 'assets/';

		$server = $this->request->getServerParams();
		//$this->startTime = isset($server['REQUEST_TIME_FLOAT']) ? $server['REQUEST_TIME_FLOAT'] : microtime(true);

		self::$instance = $this;
	}

	/**
	 * getInstance
	 */
	public static function getInstance(ServerRequestInterface $request)
	{
		if (is_null(self::$instance)) {
			self::$instance = new Debugbar($request);
		}

		return self::$instance;
	}

	/**
	 * Activate
	 * @param boolean $value
	 */
	public static function Activate($value)
	{
		if (is_bool($value)) {
			static::$activate = $value;
		} else {
			throw new BpException('The activation must be a boolean');
		}
	}

	/**
	 * Permet de retourner l'état de la debugbar
	 *
	 * @return bool
	 */
	public static function getActivate()
	{
		return static::$activate;
	}

	/**
	 * Renders the html to include needed assets
	 *
	 * Only useful if Assetic is not used
	 *
	 * @return string
	 */
	public function renderHead()
	{
		$html = '';

		if (static::$activate) {
			$inlineCss = ['simptip.min.css', 'debugbar.min.css'];
			$inlineJs = ['debugbar.min.js'];

			foreach ($inlineCss as $file) {
				$content = trim(file_get_contents($this->assets_path . 'css/' . $file));
				$html .= sprintf('<style type="text/css">%s</style>' . "\n", $content);
			}
			foreach ($inlineJs as $file) {
				$content = trim(file_get_contents($this->assets_path . 'js/' . $file));
				$html .= sprintf('<script type="text/javascript">%s</script>' . "\n", $content);
			}

			$html .= '<script type="text/javascript">jQuery.noConflict(true);</script>' . "\n";
		}

		return $html;
	}

	/**
	 * Returns the code needed to display the debug bar
	 * AJAX request should not render the initialization code.
	 *
	 * @return string
	 */
	public function render()
	{
		$html = '';

		if (static::$activate) {
			$html .= $this->buildRender();
		}

		return $html;
	}

	/**
	 * Permet de collecter les messages
	 * 
	 * @param string $message
	 * @param string $label
	 */
	public static function addMessages(string $message, string $label = 'info')
	{
		self::$messages[] = '<p class="_bp-debubar-message _message-' . $label . '">' . strip_tags($message) . '</p>';
	}

	/**
	 * Permet de collecter les messages de type info
	 * 
	 * @param string $info
	 */
	public static function info(string $info)
	{
		self::addMessages($info, 'info');
	}

	/**
	 * Permet de collecter les messages de type warning
	 * 
	 * @param string $warning
	 */
	public static function warning(string $warning)
	{
		self::addMessages($warning, 'warning');
	}

	/**
	 * Permet de collecter les messages de type error
	 * 
	 * @param string $error
	 */
	public static function error(string $error)
	{
		self::addMessages($error, 'error');
	}

	/**
	 * Permet de collecter des exceptions
	 *
	 * @param \Exception $e
	 * @return void
	 */
	public static function addException($e)
	{
		self::$exceptions[] = $e;
	}

	/**
	 * Permet de collecter les requêtes SQL
	 *
	 * @param \Exception $e
	 * @return void
	 */
	public static function addQuery($q)
	{
		self::$queries[] = $q;
	}

	/**
	 * Permet d'activer la Debugbar pendant l'exécution.
	 */
	public static function enable()
	{
		static::$activate = true;
	}

	/**
	 * Permet de désactiver la Debugbar pendant l'exécution.
	 */
	public static function disable()
	{
		static::$activate = false;
	}

	/**
	 * Permet d'activer la Debugbar
	 */
	private function setActivation()
	{
		$enable = Config::get('debugbar.enable');
		static::$activate = ($enable && !$this->request->isAjax()) ? true : false;
	}

	/**
	 * getAuth
	 * 
	 * @return string
	 */
	private function getAuth()
	{
		$auth_name = self::$auth_name;

		if (Config::get('app.auth_name')) {
			$auth_name = Config::get('app.auth_name');
		}

		$config = Config::get('app.auth_roles');
		$roles = array_flip($config);

		$session = Session::getInstance();
		$data = $session->get($auth_name);
		//debug($data)->die();
		$_role = $roles[$data['role']];

		$html = '<span class="simptip-position-top simptip-movable simptip-smooth" data-tooltip="Logged as ' . $_role . '">';
		$html .= '<span class="_bp-bar-badge">' . $_role . '</span></span>';

		return $html;
	}

	/**
	 * getResponseTime
	 *
	 * @return int
	 */
	public function getResponseTime()
	{
		return round(microtime(true) - BABIPHP_START, 3) * 1000;
	}

	private function getMemoryUsage()
	{
		$size = memory_get_usage(false);
		$unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
		$msg = '<span class="simptip-position-top simptip-movable simptip-smooth" data-tooltip="Memory usage">';
		$msg .= @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $unit[$i];
		$msg .= '</span>';

		return $msg;
	}

	private function getPhpVersion()
	{
		$version = explode('-', PHP_VERSION);
		return $version[0];
	}

	/**
	 * build debugbar output
	 * 
	 * @return string
	 */
	private function buildRender()
	{
		$icons = [];
		$icons_name = ['babiphp', 'cogs', 'clock', 'warning', 'user'];

		foreach ($icons_name as $ico) {
			$icons[$ico] = file_get_contents($this->assets_path . 'icons/' . $ico . '.svg');
		}

		$d = array(
			'version' => [
				'babiphp' => BP_VERSION,
				'php' => $this->getPhpVersion()
			],
			'request' => array(
				'method' => $this->request->getMethod(),
				'controller' => strtolower($this->request->getController()),
				'action' => $this->request->getAction(),
				'route_name' => $this->request->getRouteName()
			),
			'counter' => [
				'messages' => count(self::$messages),
				'exceptions' => count(self::$exceptions),
				'queries' => count(self::$queries),
			],
			'auth' => '',
			'memory_usage' => $this->getMemoryUsage(),
			'duration' => $this->getResponseTime() . 'ms',
			'icons' => $icons
		);

		$auth_name = (Config::get('app.auth_name')) ? Config::get('app.auth_name') : self::$auth_name;
		$session = Session::getInstance();

		if (Config::get('app.auth.enable') && $session->exists($auth_name)) {
			$d['auth'] = $this->getAuth();
		} else {
			$html = '<span class="simptip-position-top">';
			$html .= '<span class="_bp-bar-badge">not logged</span></span>';
			$d['auth'] = $html;
		}

		$d = Utils::arrayToObject($d);

		$output = <<<GA

<?-- Start Debugbar -->
<div class="_bp-debug-bar" id="bp-debug-bar">
	<div class="_item">
		BabiPHP 
		<span class="_bp-bar-badge _bp-bar-badge-info">{$d->version->babiphp} / PHP {$d->version->php}</span>
	</div>
	<span class="simptip-position-top simptip-movable simptip-smooth" data-tooltip="Request method">
		<span class="_bp-bar-badge">{$d->request->method}</span>
	</span>
	<span class="simptip-position-top simptip-movable simptip-smooth" data-tooltip="Route">
		<span class="_bp-bar-badge">{$d->request->controller}@{$d->request->action}</span>
	</span> On: 
	<span class="simptip-position-top simptip-movable simptip-smooth" data-tooltip="Route name">
		<span class="_bp-bar-it">{$d->request->route_name}</span>
	</span>
		
	<div style="float: right;">
		<div class="_item first">{$d->icons->user} {$d->auth}</div>
		<div class="_item clickable">Messages <span class="_bp-bar-badge">{$d->counter->messages}</span></div>
		<div class="_item clickable">Exceptions <span class="_bp-bar-badge">{$d->counter->exceptions}</span></div>
		<div class="_item clickable">Databases <span class="_bp-bar-badge">{$d->counter->queries}</span></div>
		<div class="_item"> {$d->icons->cogs} {$d->memory_usage}</div>
		<div class="_item">{$d->icons->clock} 
			<span class="simptip-position-left simptip-movable simptip-smooth" data-tooltip="Request Duration">{$d->duration}</span>
		</div>
		<div class="_item last">
			<div class="_bp-toggle-btn" id="bp-toggle-btn">
				<span></span>
			</div>
		</div>
	</div>
</div>
<div class="_bp-debug-content"></div>
<?-- End Debugbar -->


GA;

		return $output;
	}
}