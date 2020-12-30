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

namespace BabiPHP\Auth;

/**
 * Entity used for authentication
 */
interface UserInterface
{
    /**
     * Get user id
     * 
     * @return int|string
     */
    public function getId();
    
    /**
     * Get user's username
     * 
     * @return string
     */
    public function getUsername();
    
    /**
     * Get user's email
     * 
     * @return string
     */
    public function getEmail();
    
    /**
     * Get user's hashed password
     * 
     * @return string
     */
    public function getHashedPassword();
    
    /**
     * Event called on login.
     * 
     * @return boolean  false cancels the login
     */
    public function signup();
    
    /**
     * Event called on login.
     * 
     * @return boolean  false cancels the login
     */
    public function login();
    
    /**
     * Event called on logout.
     * 
     * @return void
     */
    public function logout();
}