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
	
namespace BabiPHP\Exception;

class BpException extends \Exception
{
	public function __construct($message = '', $code = 0, Exception $previous = null)
	{
		$message = (empty($message)) ? 'A website error has occurred.' : $message;

		parent::__construct($message, $code, $previous);
	}
}