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

/**
 * BabiPHPExceptionInterface
 *
 * Exception interface that Klein's exceptions should implement
 *
 * This is mostly for having a simple, common Interface class/namespace
 * that can be type-hinted/instance-checked against, therefore making it
 * easier to handle Klein exceptions while still allowing the different
 * exception classes to properly extend the corresponding SPL Exception type
 */
interface BabiPHPExceptionInterface
{
}