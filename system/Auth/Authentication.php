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

namespace BabiPHP\Auth;

use \BabiPHP\Http\Message\ServerRequestInterface;
use \BabiPHP\Http\ServerRequest;
use \BabiPHP\Config\Config;
use \BabiPHP\Session\Session;
use \BabiPHP\Helper\Utils;
use \BabiPHP\Auth\JsonWebToken as JWT;
use \BabiPHP\Auth\Exception\SignatureInvalidException;

/**
 * Gestion d'authentification sécurisée et complet
 */
class Authentication
{
	use ConfirmationTrait;

	/**
	 * @var ServerRequestInterface
	 */
	private $request;

	/**
	 * @var string
	 */
	private $hash_key;

	/**
	 * @var string
	 */
	private $encoder;

	/**
	 * @var string
	 */
	private $token_encoder = "HS256";

	/**
	 * @var string
	 */
	private $app_provider;

	/**
	 * @var array
	 */
	private $roles = array(
		'anonym' => 'ROLE_ANONYM',
		'user' => 'ROLE_USER',
		'admin' => 'ROLE_ADMIN',
		'super_admin' => 'ROLE_SUPER_ADMIN'
	);

	/**
	 * authenticated user
	 *
	 * @var User
	 */
	private $user;

	/**
	 * @var Authentication
	 */
	private static $_instance;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->request = ServerRequest::getInstance();
		$this->session = Session::getInstance();
		$this->encoder = Config::get('app.auth_encoder');
		$this->hash_key = Config::get('app.auth_key');

		if (Config::get('app.auth_roles')) {
			$this->roles = Config::get('app.auth_roles');
		}

		$auth_name = '__bp_auth_user';

		if (Config::get('app.auth_name')) {
			$auth_name = Config::get('app.auth_name');
		}

		$this->setAuthName($auth_name);

		self::$_instance = $this;
	}

	/**
	 * normalizeRole
	 * 
	 * @param string $role
	 * @return string
	 */
	protected function normalizeRole(string $role)
	{
		$role = isset($this->roles[$role]) ? $this->roles[$role] : 'ROLE_ANONYM';
		return $role;
	}
	
	/**
     * Fetch a user
     * 
	 * @param int|string $id
     * @return array
     */
    protected function fetchUserById($id)
    {
        $token = $this->getToken();

		if ($token === null) {
			return false;
		}
		
		$user = (array) $this->decodeToken($token)->user;
		
		if ($id != $user['id']) {
			return false;
		}

		return $user;
    }
	
	/**
	 * Retourne le clef secrète de confirmation
	 *
	 * @return string|bool
	 */
	protected function getConfirmationSecret()
	{
		return $this->getSecretKey();
	}

	/**
	 * Retourne l'instance de la class
	 * 
	 * @return Self
	 */
	public static function getInstance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new Self();
		}

		return self::$_instance;
	}

	/**
	 * Permet de définir le nom de la session utilisateur
	 *
	 * @param string $app_name
	 * @return Authentication
	 */
	public function setAuthName(string $app_name)
	{
		$this->app_provider = str_replace(' ', '_', strtolower($app_name));
		return $this;
	}

	/**
	 * Permet de définir la clef secret d'authentification
	 *
	 * @param string $key
	 * @return Authentication
	 */
	public function setSecretKey(string $key)
	{
		$this->hash_key = $key;
		return $this;
	}

	/**
	 * Permet créer une nouvelle session utilisateur
	 * 
	 * @param string $role
	 * @param array $user
	 * @return Authentication
	 */
	public function create(string $role = 'anonym', array $user = [])
	{
		$user_session = null;

		if ($user) {
			$user_session = $this->encodeToken([
				'role' => $this->normalizeRole($role),
				'user' => $user
			]);
		}
		
		$this->session->set($this->app_provider, $user_session);

		return $this;
	}

	/**
	 * Permet vérifier si l'authentification existe
	 * 
	 * @return boolean
	 */
	public function check()
	{
		return $this->session->check($this->app_provider);
	}

	/**
	 * Permet de détruire la session de l'utilisateur
	 */
	public function destroy()
	{
		$this->session->delete($this->app_provider);
		return $this;
	}

	/**
	 * Permet d'ajouter un nouveau role
	 *
	 * @param string $slug
	 * @param string $role
	 */
	public function addRole(string $slug, string $role)
	{
		if (!isset($this->roles[$slug])) {
			$this->roles[$slug] = $role;
		}

		return $this;
	}

	/**
	 * Permet de définir le token du visiteur
	 * 
	 * @param string $token
	 * @return Authentication
	 */
	public function setToken(string $token)
	{
		$this->session->set($this->app_provider, $token);
		return $this;
	}

	/**
	 * Permet de stocker les données de l'utilisateur
	 *
	 * @param array $user
	 * @return Authentication
	 */
	public function setUser(array $user)
	{
		$token = $this->getToken();
		$sess = $this->decodeToken($token);

		$auth_session = $this->encodeToken([
			'role' => $sess->role,
			'user' => $user
		]);

		$this->session->set($this->app_provider, $auth_session);
		$this->user = new User($user);

		return $this;
	}

	/**
	 * Permet de définir le role du visiteur
	 *
	 * @param string $role
	 * @return Authentication
	 */
	public function setRole(string $role)
	{
		$token = $this->getToken();
		$sess = $this->decodeToken($token);

		$auth_session = $this->encodeToken([
			'role' => $this->normalizeRole($role),
			'user' => (array) $sess->user
		]);
		
		$this->session->set($this->app_provider, $auth_session);

		return $this;
	}

	/**
	 * Retourne la clef secrète d'authentification
	 *
	 * @return string
	 */
	public function getSecretKey()
	{
		return $this->hash_key;
	}

	/**
	 * Retourne le token de l'utilisateur
	 *
	 * @return string|null
	 */
	public function getToken()
	{
		return $this->session->get($this->app_provider);
	}

	/**
	 * Retourne les données de l'utilisateur
	 * 
	 * @return User|bool
	 */
	public function getUser()
	{
		if (is_null($this->user)) {
			$token = $this->getToken();

			if ($token === null) {
				return false;
			}
			
			$sess = $this->decodeToken($token);
			$this->user = new User((array) $sess->user);
		}

		return $this->user;
	}

	/**
	 * Retourne le role de l'utilisateur
	 *
	 * @return string|bool
	 */
	public function getRole()
	{
		$token = $this->getToken();

		if ($token === null) {
			return false;
		}
		
		return $this->decodeToken($token)->role;
	}

	/**
	 * Permet de vérifier le role de l'utilisateur
	 * 
	 * @param string $role
	 * @return boolean
	 */
	public function is(string $role)
	{
		$token = $this->getToken();

		if ($token === null) {
			return false;
		}

		if (!isset($this->roles[$role])) {
			return false;
		}
		
		$stored_role = $this->decodeToken($token)->role;
		$role = $this->roles[$role];

		return ($stored_role === $role);
	}

	/**
	 * Permet de vérifier si le visiteur est authentifier
	 *
	 * @return boolean
	 */
	public function isLogged()
	{
		$token = $this->getToken();

		if ($token !== null) {
			try {
				$data = $this->decodeToken($token, $this->hash_key, [$this->token_encoder]);
				return true;
			} catch (SignatureInvalidException $e) {
				return false;
			}
		}

		return false;
	}

	/**
	 * Permet de vérifier si le visiteur est authentifier
	 *
	 * @return boolean
	 */
	public function isAuthenticated()
	{
		return $this->isLogged();
	}

	/**
	 * Permet hasher une une chaine de caractère
	 * 
	 * @param $data
	 * @return string
	 */
	public function hash(string $data)
	{
		return Utils::hash($data);
	}

	/**
	 * Permet d'encoder le token de l'utilisateur
	 *
	 * @param array $data
	 * @param string $key
	 * @return string
	 */
	public function encodeToken(array $data, string $key = '')
	{
		if ($key) {
			$this->hash_key = $key;
		}

		return JWT::encode($data, $this->hash_key);
	}

	/**
	 * Permet de décoder le token de l'utilisateur
	 *
	 * @param string $token
	 * @param string $key
	 * @param array $allowed_algs
	 * @return object|null
	 */
	public function decodeToken(string $token, string $key = '', array $allowed_algs = [])
	{
		if ($key) {
			$this->hash_key = $key;
		}

		if (!$allowed_algs) {
			$allowed_algs = [$this->token_encoder];
		}

		return JWT::decode($token, $this->hash_key, $allowed_algs);
	}
}