<?php
/**
 * BabiPHP : The Simple and Fast Development Framework (http://babiphp.org)
 * Copyright (c) BabiPHP. (http://babiphp.org)
 *
 * Licensed under The GNU General Public License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) BabiPHP. (http://babiphp.org)
 * @author        Lambirou <lambirou225@gmail.com>
 * @link          http://babiphp.org BabiPHP Project
 * @license       MIT
 *
 * Not edit this file
 */

namespace BabiPHP\Session;

use \BabiPHP\Config\Config;
use \BabiPHP\Collection\DataCollection;

/**
 * Representation en objet de la variable superglobale $_SESSION
 * Fournis des méthodes pour manipuler simplement les session
 */
class Session implements SessionInterface
{
    /**
     * @var bool
     */
    private $started = false;

    /**
     * @var null
     */
    private $null = null;

    /**
     * @var string
     */
    protected $flash_name = 'default';

    /**
     * @var array
     */
    protected $flash_template = array();

    /**
     * @var string
     */
    protected $current_flash_template;

    /**
     * @var array
     */
    protected $flash_slug = array('type', 'icon', 'message');

    /**
     * @var DataCollection
     */
    private $collections;

    /**
     * @var Session
     */
    private static $_instance;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ensureStarted();
        $this->setFlashName('flash_'.md5(Config::get('app.name')));
        $this->flash_template = Config::get('view.flash.template');
        $this->current_flash_template = $this->flash_template['default'];
        $this->collections = new DataCollection($_SESSION);
    }

    /**
     * Retourne l'instance courant de Session
     *
     * @return Session
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Self();
        }

        return self::$_instance;
    }

    /**
     * Retourne l'instance de DataCollection de la session
     *
     * @return DataCollection
     */
    public function getSessionCollection()
    {
        return $this->collections;
    }
	
	/**
     * Permet de récupérer toutes les informations de la session.
     *
     * @param string $key
     */
    public function all()
    {
        return $this->collections->all();
    }

    /**
     * Permet de stocker une information en session.
     *
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value)
    {
        $this->ensureStarted();
        $this->collections->set($key, $value);
        $_SESSION[$key] = $value;
    }

    /**
     * Permet de récupérer une information en session.
     *
     * @param string $key
     */
    public function get(string $key)
    {
        $this->ensureStarted();

        if (!$this->exists($key)) {
            return $this->null;
        }

        return $this->collections->get($key);
    }

    /**
     * Permet de vérifier si une information existe en session.
     *
     * @param string $key
     * @param mixed $child
     */
    public function check(string $key, $child = null)
    {
        if ($child) {
            $parent = $this->collections->get($key);
            return isset($parent[$child]);
        }

        return $this->exists($key);
    }
    
    /**
     * Permet de vérifier si une information existe en session.
     *
     * @param string $key
     */
    public function exists(string $key)
    {
        return $this->collections->exists($key);
    }

    /**
     * Permet d'éffacer un élement de la session
	 * 
	 * @param string $key
     */
    public function delete(string $key)
    {
		if ($this->exists($key)) {
			unset($_SESSION[$key]);
			$this->collections->remove($key);
		}
    }

    /**
     * Permet d'éffacer un élement de la session
	 * 
	 * @param string $key
     */
    public function remove(string $key)
    {
		if ($this->exists($key)) {
			unset($_SESSION[$key]);
			$this->collections->remove($key);
		}
    }

    /**
     * Permet de réinitialiser la session.
     *
     * @return mixed
     */
    public function destroy()
    {
        if ($this->started === true) {
            session_destroy();
            $this->started = false;
        }
    }

    /**
     * Permet d'envoyer un message flash
     *
     * @param string $message
     * @param string|null $type
     * @param string|null $icon
     */
    public function flash(string $message, string $type = null, string $icon = null)
    {
        $flashs = $this->get($this->flash_name);

        if (is_null($flashs)) {
            $flashs = [];
        }

        $flashs[] = array(
            'message' => $message,
            'type' => $type,
            'icon' => $icon
        );

        $this->set($this->flash_name, $flashs);
    }

    /**
     * checkFlash
     */
    public function checkFlash()
    {
        return $this->exists($this->flash_name);
    }

    /**
     * Permet de retourner le message flash
     *
     * @param bool $readable
     * @return array
     */
    public function getFlash(bool $readable = false)
    {
        $flashs = $this->get($this->flash_name);

        if (!$flashs) {
            return [];
        }

        if ($readable) {
            $stack = [];

            foreach ($flashs as $key => $flash) {
                $stack[] = $this->parseFlashTemplate($flash);
            }

            $flashs = $stack;
        }

        $this->set($this->flash_name, null);

        return $flashs;
    }

    /**
     * addFlashTemplate
     *
     * @param string $tpl
     */
    public function addFlashTemplate($name, $tpl)
    {
        $this->flash_template[$name] = $tpl;
    }

    /**
     * setFlashTemplate
     *
     * @param string $tpl
     */
    public function setFlashTemplate($name)
    {
        $this->current_flash_template = $this->flash_template[$name];
    }

    /**
     * Parse les message flash
     *
     * @param array $flash
     * @return string
     */
    protected function parseFlashTemplate(array $flash)
    {
        $template = $this->current_flash_template;
        $slugs = $this->flash_slug;

        foreach ($slugs as $key => $slug) {
            $template = str_replace('{{'.$slug.'}}', $flash[$slug], $template);
        }

        return $template;
    }

    /**
     * Permet de s'assurer que la session est démarrée.
     */
    private function ensureStarted()
    {
        if ($this->started === false && session_status() === PHP_SESSION_NONE) {
            session_start();
            $this->started = true;
        }
    }

    /**
     * setFlashName
     *
     * @param string $app_name
     */
    private function setFlashName($app_name)
    {
        $app_name = strtolower($app_name);
        $app_name = str_replace(' ', '_', $app_name);

        $this->flash_name = $app_name.'_flash';
    }

    public function offsetExists($offset)
    {
        return $this->get($offset);
    }

    public function &offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }
}
