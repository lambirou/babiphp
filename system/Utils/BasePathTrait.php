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

/**
 * Utilities used by middlewares with basePath options.
 */
trait BasePathTrait
{
    private $basePath = '';

    /**
     * Set the basepath used in the request.
     *
     * @param string $basePath
     *
     * @return self
     */
    public function basePath($basePath)
    {
        if (strlen($basePath) > 1 && substr($basePath, -1) === '/') {
            $this->basePath = substr($basePath, 0, -1);
        } else {
            $this->basePath = $basePath;
        }

        return $this;
    }

    /**
     * Removes the basepath from a path.
     *
     * @param string $path
     *
     * @return string
     */
    private function getPath($path)
    {
        if ($this->basePath === '' || strpos($path, $this->basePath) === 0) {
            $path = substr($path, strlen($this->basePath)) ?: '';
        }

        return $path === '' ? '/' : $path;
    }
}
