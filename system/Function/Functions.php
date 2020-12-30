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

use \BabiPHP\Debug\Dumper;
use \BabiPHP\Core\Renderer;
use \BabiPHP\Misc\Utils;

/**
 * Permet de charger une vue
 *
 * @param string $view
 * @param array $data
 * @return string
 */
function loadView(string $view, array $data) {
	return Renderer::getView($view, $data);
}

/**
 * @see \BabiPHP\Misc\Utils::voidClass
 */
function void_class(array $array = [])
{
	return Utils::voidClass($array);
}

/**
 * @see \BabiPHP\Misc\Utils::arrayToObject
 */
function array_to_object($array)
{
	return Utils::arrayToObject($array);
}

/**
 * @see \BabiPHP\Misc\Utils::objectToArray
 */
function object_to_array($object)
{
	return Utils::arrayToObject($object);
}

/**
 * @see \BabiPHP\Misc\Utils::getIp
 */
function get_ip()
{
	return Utils::getIp();
}

/**
 * count_file [count number of file in a folder]
 * @param  string $dir [folder to count]
 * @return integer or false [number of file in this folder or false if the dir can't be opened]
 */
function count_folder_file($dir)
{
	if($folder = opendir($dir))
	{
		$nb = 0;
		$ds = DIRECTORY_SEPARATOR;

		while(false !== ($file = readdir($folder)))
		{
			if($file != '.' && $file != '..')
			{
				if(filetype($dir.$ds.$file) == 'dir') {
					$nb += count_folder_file($dir.$ds.$file);
				} else {
					$nb++;
				}
			}
		}

		closedir($folder);

		return $nb;
	}
	else {
		return false;
	}
}

function get_folder_file($folder, $nb_file = null, $recursive = false)
{
	if($dir = opendir($folder))
	{
		$ds = DIRECTORY_SEPARATOR;
		$files = array();
		$albums = array();

		while(false !== ($file = readdir($dir)))
		{
			if($file != '.' && $file != '..')
			{
				if(filetype($folder.$ds.$file) == 'dir')
				{
					$files[$file] = get_folder_file($folder.$ds.$file);
					$albums[] = $file;
				}
				else
					$files[] = $file;
					
				// limit nb file
				if($nb_file !== null && (int)$nb_file == count($files))
					break;
			}
		}

		closedir($dir);

		if($recursive)
			return array('albums' => $albums, 'files' => $files);
		else
			return $files;
	}
	else
		return false;
}

/**
 * Permet de tronquer une chaine de caractaire
 * 
 * @param  string $chaine
 * @param  int $max
 * @param  bool $exact
 * @return string
 */
function truncate($chaine, $max_length, $exact = true, $separ = '...')
{
	if(strlen($chaine) >= $max_length) {
		$chaine = substr($chaine, 0, $max_length);

		if($exact) {
			$chaine = substr($chaine, 0, strrpos($chaine, " "));
		}

		$chaine .= $separ;
	}

	return $chaine;
}

/**
 * cut_txt
 * @param  string $chaine
 * @param  interger $max
 * @param  boolean $exact
 * @return string
 */
function cut_txt($chaine, $max_length, $exact = true, $separ = '...')
{
	if(strlen($chaine) >= $max_length)
	{
		$chaine = substr($chaine, 0, $max_length);

		if($exact) {
			$chaine = substr($chaine, 0, strrpos($chaine, " "));
		}

		$chaine .= $separ;
	}
	return $chaine;
}

/**
 * A better alternative to print_r / var_dump
 * HTML formatted only if we're not in CLI mode
 *
 * @param mixed
 * @return mixed
 */
function debug()
{
	// arguments passed to this function
	$args = func_get_args();

	$dump = new Dumper();
	$stack = debug_backtrace();
	$trace = array_shift($stack);

	foreach ($args as $var) {
		$dump->make($var, $dump->varName($var), $trace);
	}

	return $dump;
}
