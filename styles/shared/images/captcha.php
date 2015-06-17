<?php
namespace styles\shared\images;

define('NIV', '../../../');
require(NIV.'core/bootstrap.inc.php');

class Captcha extends \core\BaseClass
{

    protected function init()
    {
        parent::init();
        
        $this->displayCaptcha();
    }

    private function displayCaptcha()
    {
        $helper_Captcha = \Loader::inject('\core\helpers\Captcha');
        $helper_Captcha->generateCapcha();
    }
}

$obj_Captcha = \Loader::Inject('\styles\shared\images\Captcha');
unset($obj_Captcha);