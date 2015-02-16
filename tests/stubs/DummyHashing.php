<?php
if (! class_exists('\core\services\Hashing')) {
    require (NIV . 'include/services/Hashing.inc.php');
}

class DummyHashing extends \core\services\Hashing
{

    public function __construct()
    {}

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

    public function createSalt()
    {
        return '2193ueoidkjsncv;dnjs8q0wpo9dhicnsd;lfk;dsljihadfdasf';
    }
}
?>
