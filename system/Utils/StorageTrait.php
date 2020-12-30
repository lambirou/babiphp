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

namespace BabiPHP\Utils;

use Psr\Http\Message\ServerRequestInterface;
use BabiPHP\Http\Middleware;
use RuntimeException;

/**
 * Trait to save middleware related things in the current session.
 */
trait StorageTrait
{
    use AttributeTrait;

    /**
     * Init the storage array.
     *
     * @param ServerRequestInterface $request
     * @param array                  $storage
     *
     * @return ServerRequestInterface
     */
    private static function startStorage(ServerRequestInterface $request, array $storage)
    {
        return self::setAttribute($request, Middleware::STORAGE_KEY, (object) $storage);
    }

    /**
     * Stop the storage.
     *
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    private static function stopStorage(ServerRequestInterface $request)
    {
        $storage = self::getAttribute($request, Middleware::STORAGE_KEY);
        self::setAttribute($request, Middleware::STORAGE_KEY, null);

        return (array) $storage;
    }

    /**
     * Returns the value of a storage array.
     *
     * @param ServerRequestInterface $request
     * @param string|null            $key
     *
     * @return array
     */
    private static function &getStorage(ServerRequestInterface $request, $key = null)
    {
        if (!self::hasAttribute($request, Middleware::STORAGE_KEY)) {
            throw new RuntimeException('No session storage initialized');
        }

        $storage = self::getAttribute($request, Middleware::STORAGE_KEY);

        if (!isset($storage->$key)) {
            $storage->$key = [];
        }

        return $storage->$key;
    }
}
