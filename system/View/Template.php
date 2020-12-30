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
* @link          http://babiphp.org BabiPHP Project
* @since         BabiPHP v 0.8.9
* @license       http://www.gnu.org/licenses/ GNU License
*
* 
* Not edit this file
*
*/

namespace BabiPHP\View;

class Template
{
    /**
    * Enregistre la liste des contenus
    * @var array
    */
    private static $contents = [
        'view_content' => ''
    ];

    /**
    * 
    * @var BabiPHP\View\Template
    */
    private static $_instance;

	/**
	* getInstance
	*/
	public static function getInstance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

    public static function setOutput(string $type, string $content = '')
    {
        self::$contents[$type] = $content;
    }

    public static function getOutput(string $type)
    {
        return self::$contents[$type];
    }
}