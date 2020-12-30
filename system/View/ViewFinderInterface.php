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

interface ViewFinderInterface
{
    /**
     * Hint path delimiter value.
     *
     * @var string
     */
    const HINT_PATH_DELIMITER = '::';

    /**
     * Get the fully qualified location of the view.
     *
     * @param  string  $view
     * @return string
     */
    public function find($view);

    /**
     * Add a location to the finder.
     *
     * @param  string  $location
     * @return void
     */
    public function addLocation($location);

    /**
     * Add a namespace hint to the finder.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function addNamespace($namespace, $hints);

    /**
     * Prepend a namespace hint to the finder.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function prependNamespace($namespace, $hints);

    /**
     * Add a valid view extension to the finder.
     *
     * @param  string  $extension
     * @return void
     */
    public function addExtension($extension);
}
