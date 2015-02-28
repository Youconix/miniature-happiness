<?php
namespace core\services;

/**
 * Miniature-happiness is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Miniature-happiness is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 *
 * Hashing service
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 2.0
 */
class Hashing extends Service
{

    private $service_Random;

    private $obj_hashing = null;

    private $s_systemSalt;

    /**
     * PHP 5 constructor
     *
     * @param \core\services\Logs $service_Logs
     *            The log service
     * @param \core\services\Settings $service_Settings
     *            The settings service
     * @param \core\services\Random $service_Random
     *            The random generator
     */
    public function __construct(\core\services\Logs $service_Logs, \core\services\Settings $service_Settings, \core\services\Random $service_Random)
    {
        $this->service_Random = $service_Random;
        if (! function_exists('password_hash')) {
            if (CRYPT_BLOWFISH != 1) {
                /*
                 * Fallback
                 * Security warning
                 */
                $this->obj_hashing = new HashingFallback();
                $service_Logs->setLog('security', 'Missing bcrypt and CRYPT_BLOWFISH. Falling back to sha1 hashing. Upgrade your PHP-installation to min. 5.5 at ones!');
            } else {
                /*
                 * Legancy
                 * Security warning
                 */
                $this->obj_hashing = new HashLegancy();
                $service_Logs->setLog('security', 'Missing bcrypt. Falling back to crypt() with CRYPT_BLOWFISH hashing. Upgrade your PHP-installation to min. 5.5 as soon as possible!');
            }
        } else {
            $this->obj_hashing = new HashNormal();
        }
        
        $this->s_systemSalt = $service_Settings->get('settings/main/salt');
        
        $i_length = strlen($this->s_systemSalt);
        if ($i_length < 22) {
            $this->s_systemSalt .= substr($this->s_systemSalt, 0, (22 - $i_length));
        }
    }

    /**
     * Returns if the object schould be treated as singleton
     *
     * @return boolean True if the object is a singleton
     */
    public static function isSingleton()
    {
        return true;
    }

    public function hash($s_text, $s_salt)
    {
        return $this->obj_hashing->hash($s_text, $s_salt);
    }

    public function verify($s_text, $s_stored, $s_salt)
    {
        return $this->obj_hashing->verify($s_text, $s_stored, $s_salt);
    }

    public function hashUserPassword($s_password, $s_username)
    {
        return $this->obj_hashing->hashUserPassword($s_username, $s_password, $this->s_systemSalt);
    }

    public function verifyUserPassword($s_username, $s_password, $s_stored)
    {
        return $this->obj_hashing->verifyUserPassword($s_username, $s_password, $s_stored, $this->s_systemSalt);
    }

    public static function createSalt($service_Random)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes(30));
        }
        
        return $service_Random->randomAll(30);
    }
}

abstract class HashingParent
{

    abstract public function hash($s_text, $s_salt);

    public function verify($s_text, $s_stored, $s_salt)
    {
        $s_input = $this->hash($s_text, $s_salt);
        
        return $s_input === $s_stored;
    }

    public function hashUserPassword($s_username, $s_password, $s_salt)
    {
        $s_text = $this->createUserPassword($s_username, $s_password);
        $s_hash = $this->hash($s_text, $s_salt);
        
        $i_missing = (60 - strlen($s_hash));
        if ($i_missing > 0) {
            $s_hash = $s_hash .= substr($s_hash, 0, $i_missing);
        }
        
        return $s_hash;
    }

    public function verifyUserPassword($s_username, $s_password, $s_stored, $s_salt)
    {
        $s_text = $this->hashUserPassword($s_username, $s_password, $s_salt);
        return ($s_text === $s_text);
    }

    protected function createUserPassword($s_username, $s_password)
    {
        return substr(md5(strtolower($s_username)), 5, 30) . $s_password;
    }
}

class HashNormal extends HashingParent
{

    public function hash($s_text, $s_salt)
    {
        $a_options = array(
            'salt' => $s_salt
        );
        
        $s_hash = password_hash($s_text, PASSWORD_BCRYPT, $a_options);
        return $s_hash;
    }

    public function hashUserPassword($s_username, $s_password, $s_salt)
    {
        $a_options = array(
            'salt' => $s_salt
        );
        $s_text = $this->createUserPassword($s_username, $s_password);
        
        $s_hash = password_hash($s_text, PASSWORD_BCRYPT, $a_options);
        
        return $s_hash;
    }
}

class HashLegancy extends HashingParent
{

    public function hash($s_text, $s_salt)
    {
        return crypt($s_text, $s_salt);
    }
}

class HashingFallback extends HashingParent
{

    public function hash($s_text, $s_salt)
    {
        return sha1($s_text, $s_salt);
    }
}