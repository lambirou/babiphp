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

namespace BabiPHP\Misc;

class Cookie
{
	private $expire = array(
		'Session' => null,
		'OneDay' => 86400,
		'ThreeDay' => 86400,
		'SevenDays' => 604800,
		'ThirtyDays' => 2592000,
		'SixMonths' => 15811200,
		'OneYear' => 31536000,
		'Lifetime' => -1
	);

	private $cookiePath;
	private static $_instance;

	public function __construct($path = null)
	{
		$this->cookiePath = $path;
		self::$_instance = $this;
	}

	/**
	 * GetInstance
	 */
	public static function getInstance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new Cookie();
		}

		return self::$_instance;
	}

	/**
	 * Returns true if there is a cookie with this name.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function check($name)
	{
		return isset($_COOKIE[$name]);
	}

	/**
	 * Returns true if there no cookie with this name or it's empty, or 0,
	 * or a few other things. Check http://php.net/empty for a full list.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function isEmpty($name)
	{
		return empty($_COOKIE[$name]);
	}

	/**
	 * Get the value of the given cookie. If the cookie does not exist the value
	 * of $default will be returned.
	 *
	 * @param string $name
	 * @param string $default
	 * @return mixed
	 */
	public function get($name, $default = null)
	{
		return (isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default);
	}

	/**
	 * Set a cookie. Silently does nothing if headers have already been sent.
	 *
	 * @param string $name
	 * @param string $value
	 * @param mixed $expiry
	 * @param string $path
	 * @param string $domain
	 * @param string $secure
	 * @param string $httpOnly
	 * @return bool
	 */
	public function set($name, $value = '', $expire = 0, $path = '', $domain = '', $secure = false, $httpOnly = true)
	{
		$expire = isset($this->expire[$expire]) ? $this->expire[$expire] : $expire;
		$retval = false;

		if (!headers_sent()) {
			if ($expire === -1)
				$expire = 1893456000;

			$retval = setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
		}

		return $retval;
	}

	/**
	 * Delete a cookie.
	 *
	 * @param string $name
	 * @param string $path
	 * @param string $domain
	 * @return bool
	 */
	public function delete($name, $path = '', $domain = '')
	{
		$retval = false;

		if (isset($_COOKIE[$name]))
			$retval = setcookie($name, "", time() - 3600, $path, $domain, false, true);

		return $retval;
	}
}
