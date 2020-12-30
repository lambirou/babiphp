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

namespace BabiPHP\Misc;

use BabiPHP\Config\Config;
use \DateTime;
use \stdClass;

class Utils
{
    /**
     * Permet de créer une class et de la remplir avec un tableau associatif
     *
     * @param array
     * @return stdClass
     */
    public static function voidClass(array $array = [])
    {
        $class = new stdClass;

        foreach ($array as $key => $value) {
            $class->$key = $value;
        }

        return $class;
    }

    /**
     * Permet de convertir un objet en tableau
     *
     * @param $array
     * @return stdClass
     */
    public static function arrayToObject(array $array)
    {
        if (is_array($array) && !empty($array)) {
            $d = new stdClass();

            foreach ($array as $k => $v) {
                if (!empty($v) && is_array($v)) {
                    $v = self::arrayToObject($v);
                }

                $d->$k = $v;
            }

            return $d;
        }
    }

    /**
     * Permet de convertir un objet en tableau
     *
     * @param $object
     * @return array|null
     */
    public static function objectToArray($object)
    {
        if (is_object($object)) {
            return get_object_vars($object);
        }

        return null;
    }

    /**
     * Get browser language, given an array of avalaible languages.
     *
     * @param string  $default Default language for the site
     * @param array   $availableLanguages  Avalaible languages for the site
     * @return string Language code/prefix
     */
    public static function getBrowserLanguage($default = 'en', $available = [])
    {
        if (empty($available)) {
            $available = Config::get('i18n.supported');
        }

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

            if (empty($available)) {
                return $langs[0];
            }

            foreach ($langs as $lang) {
                $lang = substr($lang, 0, 2);

                if (in_array($lang, $available)) {
                    return $lang;
                }
            }
        }

        return $default;
    }

    /**
     * Returns a DateTime object initialized at the $time param and using UTC
     * as timezone
     *
     * @param string|integer|DateTime $time
     * @return DateTime
     */
    public static function getUTCDate($time = null)
    {
        if ($time instanceof DateTime) {
            $result = clone $time;
        } elseif (is_int($time)) {
            $result = new DateTime(date('Y-m-d H:i:s', $time));
        } else {
            $result = new DateTime($time);
        }

        $result->setTimeZone(new DateTimeZone('UTC'));

        return $result;
    }

    /**
     * Permet de convertir une date en timestamp
     *
     * @param string $datetime
     * @param string $type
     * @return int
     */
    public static function dateToTime(string $datetime, string $type = 'datetime')
    {
        $datetime = ($type == 'date') ? $datetime . ' 00:00:00' : $datetime;

        list($date, $time) = explode(' ', $datetime);
        list($year, $month, $day) = explode('-', $date);
        list($hour, $minute, $second) = explode(':', $time);

        return mktime($hour, $minute, $second, $month, $day, $year);
    }

    /**
     * Retourne l'adresse IP du client
     *
     * @return string
     */
    public static function getIp()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        }
    }

    /**
     * Hash encode a string
     * @param   string
     * @return  string
     */
    public static function hash($data)
    {
        $hash_key = Config::get('app.auth_key');
        $algo = Config::get('app.auth_encoder');

        return hash_hmac($algo, $data, $hash_key);
    }

    /**
     * Encrypt
     * @param   $pure_string
     * @param   $encryption_key
     * @return  string encrypted
     */
    public static function encrypt($pure_string, $encryption_key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryption_key, utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);

        return $encrypted_string;
    }

    /**
     * Decrypt
     *
     * @param   $encrypted_string
     * @param   $encryption_key
     * @return  string decrypted
     */
    public static function decrypt($encrypted_string, $encryption_key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $encrypted_string, MCRYPT_MODE_ECB, $iv);

        return $decrypted_string;
    }

    /**
     * _salt 
     * 
     * @param  integer $length [description]
     * @return string          [description]
     */
    protected static function _salt($length = 22)
    {
        $salt = str_replace(
            array('+', '='),
            '.',
            base64_encode(sha1(uniqid(Config::get('app.auth_key'), true), true))
        );

        return substr($salt, 0, $length);
    }

    /**
     * getArrayKeys
     * 
     * @param array $array
     */
    public static function getArrayKeys($array)
    {
        foreach ($array as $k => $v) {
            if (is_array($v))
                $k = array_keys($v);
            $d[] = $k;
        }

        return $d;
    }
}
