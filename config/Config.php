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

use BabiPHP\Config\Config;

/*
|--------------------------------------------------------------------------
| Application Title
|--------------------------------------------------------------------------
 */
Config::set('app.base_path', false);

/*
|--------------------------------------------------------------------------
| Application Title
|--------------------------------------------------------------------------
 */
Config::set('app.name', 'BabiPHP');

/*
|--------------------------------------------------------------------------
| Application Description
|--------------------------------------------------------------------------
 */
Config::set('app.description', 'The flexible PHP Framework');

/*
|--------------------------------------------------------------------------
| Site Language / Default = 'en_US'
|--------------------------------------------------------------------------
 */
Config::set('app.lang', 'en_US');

/*
|--------------------------------------------------------------------------
| Site Character Set / Default = 'utf-8'
|--------------------------------------------------------------------------
 */
Config::set('app.charset', 'utf-8');

/*
|--------------------------------------------------------------------------
| URL suffix : http://mydomain.com/blog to http://mydomain.com/blog.html
|--------------------------------------------------------------------------
 */
Config::set('system.suffix.enable', false);

Config::set('system.suffix.extension', '.html');

/*
|--------------------------------------------------------------------------
| Redirect to https and adds the Strict-Transport-Security header
|--------------------------------------------------------------------------
 */
Config::set('system.redirect.https', false);

/*
|--------------------------------------------------------------------------
| Add or remove the www subdomain
|--------------------------------------------------------------------------
 */
Config::set('system.redirect.www', false);

/*
|--------------------------------------------------------------------------
| Trailing-Slash
|--------------------------------------------------------------------------
 */
Config::set('system.trailingslash.enable', true);
Config::set('system.trailingslash.redirect', false);

/*
|--------------------------------------------------------------------------
| BabiPHP Application multilanguage support
|--------------------------------------------------------------------------
 */
Config::set('i18n.enable', true);

Config::set('i18n.default', 'en_US');

Config::set('i18n.supported', ['en', 'fr']);

Config::set('i18n.encoding', 'UTF-8');

/*
|--------------------------------------------------------------------------
| View Extension / Default = 'tpl'
|--------------------------------------------------------------------------
 */
Config::set('view.extensions', ['tpl', 'template.tpl']);

/*
|--------------------------------------------------------------------------
| Application default template Template
|--------------------------------------------------------------------------
 */
Config::set('view.default_template', 'default');

/*
|--------------------------------------------------------------------------
| BabiPHP Application Flash Templates
|--------------------------------------------------------------------------
 */
Config::set('view.flash.template', [
	'default' => '<div class="alert alert-{{type}} alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<div class="message"><i class="{{icon}}"></i>{{message}}</div>
		</div>'
]);

/*
|--------------------------------------------------------------------------
| BabiPHP Application error handler
|--------------------------------------------------------------------------
 */
Config::set('error.handler', true);

/*
|--------------------------------------------------------------------------
| BabiPHP Application display error details
|--------------------------------------------------------------------------
 */
Config::set('error.display_details', true);

/*
|--------------------------------------------------------------------------
| Error Handling & Templating
|--------------------------------------------------------------------------
 */
Config::set('custom_error.enable', true);

Config::set('custom_error.template', [
	'template' => 'errors',
	'view' => [
		400 => 'errors/400',
		403 => 'errors/403',
		404 => 'errors/404',
		410 => 'errors/410',
		500 => 'errors/500',
		503 => 'errors/503'
	]
]);

/*
|--------------------------------------------------------------------------
| Cache configuration
|--------------------------------------------------------------------------
 */
Config::set('cache.enable', false);

Config::set('cache.system', 'filesystem');

Config::set('cache.systems', [
	'filesystems' => [],
	'memcache' => []
]);

/*
|--------------------------------------------------------------------------
| Application IP filtering
|--------------------------------------------------------------------------
 */
Config::set('firewall.ip.enable', false);

Config::set('firewall.ip.default_status', true);

Config::set('firewall.ip.whitelist', []);

Config::set('firewall.ip.blacklist', []);

/*
|--------------------------------------------------------------------------
| BabiPHP Application enable debugbar
|--------------------------------------------------------------------------
 */
Config::set('debugbar.enable', true);

/*
|--------------------------------------------------------------------------
| To display a 503 maintenance page
|--------------------------------------------------------------------------
 */
Config::set('maintenance.enable', false);

Config::set('maintenance.retry_after', 604800); // 7 days

//-------------------------------------------------------------------------
