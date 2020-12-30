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
 * 
 * Not edit this file
 *
 */

    namespace BabiPHP\Exception;

    use RuntimeException;

    /**
     * DispatchHaltedException
     *
     * Exception used to halt a route callback from executing in a dispatch loop
     */
    class DispatchHaltedException extends RuntimeException implements BabiPHPExceptionInterface
    {

        /**
         * Constants
         */

        /**
         * Skip this current match/callback
         *
         * @type int
         */
        const SKIP_THIS = 1;

        /**
         * Skip the next match/callback
         *
         * @type int
         */
        const SKIP_NEXT = 2;

        /**
         * Skip the rest of the matches
         *
         * @type int
         */
        const SKIP_REMAINING = 0;


        /**
         * Properties
         */

        /**
         * The number of next matches to skip on a "next" skip
         *
         * @type int
         */
        protected $number_of_skips = 1;


        /**
         * Methods
         */

        /**
         * Gets the number of matches to skip on a "next" skip
         *
         * @return int
         */
        public function getNumberOfSkips()
        {
            return $this->number_of_skips;
        }

        /**
         * Sets the number of matches to skip on a "next" skip
         *
         * @param int $number_of_skips
         * @return DispatchHaltedException
         */
        public function setNumberOfSkips($number_of_skips)
        {
            $this->number_of_skips = (int) $number_of_skips;

            return $this;
        }
    }
