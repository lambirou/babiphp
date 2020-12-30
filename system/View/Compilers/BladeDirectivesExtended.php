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

namespace BabiPHP\View\Compilers;

class BladeDirectivesExtended
{
    /**
     * @var BladeCompiler
     */
    private $compiler;

    /**
     * Constructor
     *
     * @param BladeCompiler $compiler
     */
    public function __construct(BladeCompiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * Checks whether the array is empty.
         *
         * Usage: @ifempty($array)
         */
        $this->compiler->directive('ifempty', function($expression)
        {
            return "<?php if(count($expression) == 0): ?>";
        });

        /*
         * Closes the ifempty statement.
         *
         * Usage: @endifempty
         */
        $this->compiler->directive('endifempty', function($expression)
        {
            return '<?php endif; ?>';
        });

        /*
         * Laravel dd() function.
         *
         * Usage: @dd($variable)
         */
        $this->compiler->directive('dd', function ($expression) {
            return "<?php dd(with({$expression})); ?>";
        });

        /*
         * php explode() function.
         *
         * Usage: @explode($delimiter, $string)
         */
        $this->compiler->directive('explode', function ($argumentString) {
            list($delimiter, $string) = $this->getArguments($argumentString);
            return "<?php echo explode({$delimiter}, {$string}); ?>";
        });

        /*
         * php implode() function.
         *
         * Usage: @implode($delimiter, $array)
         */
        $this->compiler->directive('implode', function ($argumentString) {
            list($delimiter, $array) = $this->getArguments($argumentString);
            return "<?php echo implode(\"{$delimiter}\", {$array}); ?>";
        });

        /*
         * php var_dump() function.
         *
         * Usage: @vardump($variable)
         */
        $this->compiler->directive('vardump', function ($expression) {
            return "<?php var_dump({$expression}); ?>";
        });

        /*
         * BabiPHP debug() function.
         *
         * Usage: @debug($variable)
         */
        $this->compiler->directive('debug', function ($expression) {
            return "<?php debug({$expression}); ?>";
        });

        /*
         * BabiPHP flash() function.
         *
         * Usage: @flash($variable)
         */
        $this->compiler->directive('flash', function ($expression) {
            return "<?php echo flash(); ?>";
        });

        /*
         * Sets a variable.
         *
         * Usage: @set($name, value)
         */
        $this->compiler->directive('set', function($expression) {
            list($variable, $value) = explode(',', $expression, 2);
            // Ensure variable has no spaces or apostrophes
            $variable = trim(str_replace('\'', '', $variable));
            // Make sure that the variable starts with $
            if (! starts_with($variable, '$')) {
                $variable = '$' . $variable;
            }
            $value = trim($value);
            return "<?php {$variable} = {$value}; ?>";
        });

        /*
         * Truncates a variable.
         *
         * Usage: @truncate('Your String' , 4)
         */
        $this->compiler->directive('truncate', function ($expression) {
            list($string, $length) = $this->getArguments($expression);
            return "<?php echo e(strlen('{$string}') > {$length} ? substr('{$string}',0,{$length}).'...' : '{$string}'); ?>";
        });

        /*
         * Sets the csrf token to the browser's window object.
         * The namespace is optional.
         *
         * Usage: @csrf($namespace)
         * Example: @csrf('Laracasts')
         */
        $this->compiler->directive('csrf', function ($expression) {
            list($namespace) = $this->getArguments($expression);
            $namespace = ($namespace !== '') ? $namespace : 'Laravel';
            $csrf      = csrf_token();
            $metaTag   = "<meta name=\"csrf-token\" content='{$csrf}'>";
            $scriptTag = "<script>window.{$namespace} = {'csrfToken': '{$csrf}'}</script>";
            return $metaTag . $scriptTag;
        });

        /*
         * Passes a variable to javascript, adding it to window.$variableName.
         *
         * Usage: @js(users, $users)
         *        @js(users, 1234)
         *        @js(users, [$users])
         */
        $this->compiler->directive('js', function ($arguments) {
            list($var, $data) = explode(',', str_replace(['(', ')', ' ', "'"], '', $arguments));
            return  "<?php echo \"<script>window['{$var}']= {$data};</script>\" ?>";
        });
    }

    /**
     * Get argument array from argument string.
     *
     * @param string $arguments
     * @param integer $count
     *
     * @return array
     */
    private function getArguments($arguments, $count = 0)
    {
        $argumentArray = explode(', ', str_replace(['(', ')'], '', $arguments));
        $result = [];
        foreach($argumentArray as $value) {
            array_push($result, trim($value, '\''));
        }
        // Forces the array to have at least $count values.
        while(count($result) < $count) {
            array_push($result, '');
        }
        return $result;
    }
}