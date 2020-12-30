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
* @package       system.BASEPATH.database.graphs
* @since         BabiPHP v 0.8.8
* @license       http://www.gnu.org/licenses/ GNU License
*
* 
* Not edit this file
*
*/

namespace BabiPHP\Database;

/**
* GraphResponse
*/
class GraphResponse
{
	private $data;

	function __construct($data)
	{
		$this->data = $data;
	}

	/**
	 * Returns true if the request is success or false if fail.
	 *
	 * @return boolean
	 */
	public function success()
	{
		return $this->data->success;
	}

	/**
	 * Returns the result of the request.
	 *
	 * @return mixed
	 */
	public function response()
	{
		return $this->data->response;
	}

	/**
	 * Returns an error if present.
	 *
	 * @return object|null
	 */
	public function error()
	{
		return $this->data->error;
	}

	/**
	 * Returns the sql request info
	 *
	 * @return object
	 */
	public function request()
	{
		return $this->data->request;
	}

	/**
	 * To edit response
	 * @param mixed $data
	 */
	public function addResponse($data = null)
	{
		if ($data) $this->data->response = $data;
	}
}