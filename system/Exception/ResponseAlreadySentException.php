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
 * @package       system.BASEPATH.exception
 * @since         BabiPHP v 0.8.5
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * Not edit this file
 */

    namespace BabiPHP\Exception;

	use RuntimeException;

	/**
	 * ResponseAlreadySentException
	 *
	 * Exception used for when a response is attempted to be sent after its already been sent
	 */
	class ResponseAlreadySentException extends RuntimeException implements BabiPHPExceptionInterface
	{
	}
