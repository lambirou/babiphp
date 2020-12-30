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

namespace BabiPHP\Config;

class Config
{
    /**
     * @var array
     */
    protected static $default = [
        'app.base_path' => false,
        'app.name' => 'BabiPHP',
        'app.description' => 'The flexible PHP Framework',
        'app.charset' => 'utf-8',
        'app.lang' => 'en_US',
        'app.access_control' => array(
            'ROLE_ANONYM' => '*',
            'ROLE_ADMIN' => 'controller#action'
        ),
        'app.admin_role' => ['ROLE_ADMIN'],

        'system.redirect.https' => false,
        'system.redirect.www' => false,
        'system.suffix.enable' => false,
        'system.suffix.extension' => '.html',
        'system.trailingslash.redirect' => false,

        'seo.robots.enable' => false,
        'seo.googleanalytics.enable' => false,
        'seo.googleanalytics.site_id' => 'UA-12345678-9',
        
        'error.handler' => true,
        'error.display_details' => false,

        'custom_error.enable' => true,
        'custom_error.template' => [
            'template' => 'errors', 
            'view' => [
                400 => 'errors/400',
                403 => 'errors/403',
                404 => 'errors/404',
                410 => 'errors/410',
                500 => 'errors/500',
                503 => 'errors/503'
            ]
        ],

        'view.extensions' => ['tpl', 'template.tpl'],
        'view.default_template' => 'default',
        'view.flash.template' => [],

        'debugbar.enable' => true,

        'maintenance.enable' => false,
        'maintenance.retry_after' => 300,

        // Database
        'database.databases' => [
            'local' => [
                'driver'        => 'mysql',
                'persistent'    => true,
                'host'          => 'localhost',
                'port'          => '3306',
                'name'          => '',
                'user'          => '',
                'pass'          => '',
                'charset'       => '',
                'prefix'        => ''
            ]
        ],
        'database.default' => 'local',

        // Firewall
        'firewall.ip.enable' => false,
        'firewall.ip.default_status' => true,
        'firewall.ip.whitelist' => [],
        'firewall.ip.blacklist' => [],

        // Security
        'app.auth.enable' => false,
        'app.auth_roles' => array(
            'anonym' => 'ROLE_ANONYM',
            'user' => 'ROLE_USER',
            'admin' => 'ROLE_ADMIN',
            'super_admin' => 'ROLE_SUPER_ADMIN'
        ),
        'app.auth_name' => '__bp_auth_user',
        'app.auth_encoder' => 'sha512',
        'app.auth_key' => 'DYsfqqghshh5+45sh4h=4h6hykyk--466FFqD8çCOy9Uuçgggf675FHFHxfs2+=ni0FgaC9mi',
    ];

    /**
     * @var array
     */
    protected static $configs;

    public static function init(array $configs = [])
    {
        self::$configs = array_merge(self::$default, $configs);
    }

    /**
    * Set
    */
    public static function set($key, $value)
    {
        self::$configs[self::normalizeKey($key)] = $value;
    }

    /**
    * Get
    */
    public static function get($key)
    {
        if (self::exists($key)) {
            return self::$configs[self::normalizeKey($key)];
        } else {
            throw new NotFoundConfigException('Could not find an config with id "'.$key.'"');
        }
    }

    /**
    * All
    */
    public static function all()
    {
        return self::$configs;
    }

    /**
    * exists
    */
    public static function exists($key)
    {
        return array_key_exists(self::normalizeKey($key), self::$configs);
    }

    /**
     * Countable
     */
    public static function count()
    {
        return count(self::$configs);
    }

    /**
     * NormalizeKey
     */
    protected static function normalizeKey($key)
    {
        return $key;
    }
}