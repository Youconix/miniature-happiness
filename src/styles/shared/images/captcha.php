<?php
namespace styles\shared\images;

ini_set('display_errors','on');
error_reporting(E_ALL);
define('NIV', '../../../');
require(NIV.'vendor/youconix/core/bootstrap.php');

class Captcha 
{
    public function __construct(){        
        $this->displayCaptcha();
    }

    private function displayCaptcha()
    {
        $helper_Captcha = \Loader::inject('\youconix\core\helpers\Captcha');
        $helper_Captcha->generateCapcha();
    }
}

$obj_Captcha = \Loader::Inject('\styles\shared\images\Captcha');
unset($obj_Captcha);