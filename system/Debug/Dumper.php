<?php
/**
 * Copyright (c) BabiPHP. (http://babiphp.org)
 *
 * Licensed under The GNU General Public License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) BabiPHP. (http://babiphp.org)
 * @link          http://babiphp.org BabiPHP Project
 * @since         BabiPHP v 0.1
 * @license       MIT
 */

namespace BabiPHP\Debug;

class Dumper
{
    /**
     * @var boolean
     */
    private $die = false;
    
    public function varName(&$var, $scope = false, $prefix = 'UNIQUE', $suffix = 'VARIABLE')
    {
        $vals = ($scope) ? $scope : $GLOBALS;
        $old = $var;
        $var = $new = $prefix.rand().$suffix;
        $vname = false;
        foreach ($vals as $key => $val) {
            if ($val === $new) {
                $vname = $key;
            }
        }
        $var = $old;
        return $vname;
    }

    public function make($var, $var_name, $trace)
    {
        $type = gettype($var);

        if (is_string($var) === true) {
            $string = htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
            $print = '<span class="db---string">'.$string.'</span>';
        } elseif (is_numeric($var) === true || is_float($var) === true || is_int($var) === true) {
            $int = ($var == 0 )? '0' : $var;
            $print = '<span class="db---numeric" title="'.$type.'">'.$int.'</span>';
        } elseif (is_bool($var) === true) {
            if ($var === false) {
                $print = '<span class="db---badge false" title="'.$type.'">false</span>';
            } else {
                $print = '<span class="db---badge true" title="'.$type.'">true</span>';
            }
        } elseif (is_null($var) === true) {
            $print = '<span class="db---badge null">null</span>';
        } else {
            $code = print_r($var, true);

            if ((strstr($code, '<') !== false) || (strstr($code, '>') !== false)) {
                $code = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
            }

            $print = '';
            
            if (gettype($var) == 'resource') {
                $print .= '<span class="db---badge ressource">resource</span> ';
                $print .= $code;
            }

            if ($var instanceof \Traversable) {
                $print .= '<span class="db---mode iterable" title="Iterateable">X</span>';
            }

            if (is_object($var)) {
                $test = new \ReflectionClass($var);

                if ($test->isAbstract()) {
                    $print .= '<span class="db---mode abstract" title="Abstract">A</span>';
                }

                $print .= '<span class="db---mode cloneable" title="Cloneable">C</span>';
                $print .= '<span class="db---name">'.get_class($var).'</span>';
            }
            
            if (in_array(gettype($var), ['object', 'array'])) {
                $print .= $this->codeHighlight($code);
            }
        }

        ob_start();
        ?>
        <style>
            <?php readfile(__DIR__.'/Resources/debug.css'); ?>
        </style>
        <?php
        $output = preg_replace('/\s+/', ' ', trim(ob_get_clean()));
        $output .= '<div class="bp---debug">'.
            '<div class="db---input">'.
                '> '.$type.'</a>'.
                '<div style="float: right;">'.'@debug'.'</div>'.
            '</div>'.
            '<div class="db---output">'.($print).'</div>'.
            '<div class="db---info">'.
                '<div class="db---info-left">'.$this->filePath($trace['file']).':<strong>'.$trace['line'].'</strong></div>'.
                '<div class="db---info-right">'.date('Y/m/d H:i:s').'</div>'.
            '</div>'.
        '</div>';

        echo $output;
    }

    public function die($die = true)
    {
        if ($die === true) {
            die;
        }
    }
    
    private function codeHighlight($code)
    {
        return '<pre>'.
                '<div class="code-highlight">'.
                    '<code>'.$code.'</code>'.
                '</div>'.
            '</pre>';
    }
    
    private function filePath($file)
    {
        $ds = DIRECTORY_SEPARATOR;
        $array = explode($ds, $file);
        $prefix = '';
        
        if (count($array) > 4) {
            $array = array_slice(array_reverse($array), 0, 4);
            $array = array_reverse($array);
            $prefix = '...';
        }

        return $prefix.$ds.implode($ds, $array);
    }

    private function getReference(&$var)
    {
        if (is_object($var)) {
            $var->___uniqid = uniqid();
        } else {
            $var = serialize($var);
        }
        $name = $this->getReference_traverse($var, $GLOBALS);
        if (is_object($var)) {
            unset($var->___uniqid);
        } else {
            $var = unserialize($var);
        }
        return "\${$name}";
    }

    private function getReference_traverse(&$var, $arr)
    {
        if ($name = array_search($var, $arr, true)) {
            return "{$name}";
        }
        foreach ($arr as $key => $value) {
            if (is_object($value)) {
                if ($name = $this->getReference_traverse($var, get_object_vars($value))) {
                    return "{$key}->{$name}";
                }
            }
        }
    }
}