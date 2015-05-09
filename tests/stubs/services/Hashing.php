<?php
namespace tests\stubs\services;

class Hashing extends \core\services\Hashing
{

    private $service_Random;
     /**
     * PHP 5 constructor
     *
     * @param \core\services\Random $service_Random
     *            The random generator
     */
    public function __construct( \core\services\Random $service_Random)
    {
        $this->service_Random = $service_Random;
    }

    public function hash($s_text, $s_salt)
    {
        return sha1($s_text, $s_salt);
    }

    public function verify($s_text, $s_stored, $s_salt)
    {
        return ($s_stored == $this->hash($s_text, $s_salt));
    }

    public function hashUserPassword($s_password, $s_username)
    {
        return $this->hash($s_password . ' ' . $s_username, $this->createSalt());
    }

    public function verifyUserPassword($s_username, $s_password, $s_stored)
    {
        return ($s_stored == $this->hashUserPassword($s_password, $s_username));
    }

    public static function createSalt()
    {
        return '2193ueoidkjsncv;dnjs8q0wpo9dhicnsd;lfk;dsljihadfdasf';
    }
}
?>
