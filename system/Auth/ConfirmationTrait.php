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

use \Hashids\Hashids;

/**
 * Generate and verify confirmation tokens.
 * 
 * Uses the hashids library.
 * @link http://hashids.org/php/
 */
trait ConfirmationTrait
{
    /**
     * Fetch a user by ID
     * 
     * @param int|string $id
     * @return User|null
     */
    abstract public function fetchUserById($id);
    
    /**
     * Get secret for the confirmation hash
     * 
     * @return string
     */
    abstract protected function getConfirmationSecret();
    
    /**
     * Create a heashids interface
     * 
     * @param string $subject
     * @return Hashids
     */
    protected function createHashids(string $subject)
    {
        $salt = hash('sha256', $this->getConfirmationSecret() . $subject);
        return new Hashids($salt);
    }
    
    /**
     * Generate a confirm checksum based on a user id and secret.
     * 
     * @param int|string $id
     * @param int $len The number of characters of the hash (max 64)
     * @return int
     */
    protected function getConfirmationChecksum($id, int $len = 32)
    {
        $hash = hash('sha256', $id . $this->getConfirmationSecret());
        return substr($hash, 0, $len);
    }
    
    /**
     * Generate a confirmation token
     * 
     * @param int|string $id
     * @param string $subject What needs to be confirmed?
     * @return string
     */
    public function getConfirmationToken($id, string $subject)
    {
        $confirm = $this->getConfirmationChecksum($id);
        $hashids = $this->createHashids($subject);
        
        return $hashids->encodeHex($confirm . $id);
    }
    
    /**
     * Verify confirmation token
     * 
     * @param string $token Confirmation token
     * @param string $subject What needs to be confirmed?
     * @return array|bool
     */
    public function verifyConfirmationToken(string $token, string $subject)
    {
        $hashids = $this->createHashids($subject);
        $idAndConfirm = $hashids->decodeHex($token);
        
        if (empty($idAndConfirm)) {
            return false;
        }
        
        $len = strlen($this->getConfirmationChecksum(''));
        $confirm = substr($idAndConfirm, 0, $len);
        $id = substr($idAndConfirm, $len);
        $user = $this->fetchUserById($id);

        if (!$user) {
            return false;
        }
        
        if ($confirm !== $this->getConfirmationChecksum($id)) {
            return false;
        }
        
        return $user;
    }
}