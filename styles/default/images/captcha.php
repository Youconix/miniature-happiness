<?php
define('NIV','../../../');
define('PROCESS','1');
require(NIV.'core/BaseClass.php');

class Captcha extends \core\BaseClass {
	public function __construct(){
		$this->init();
		$this->displayCaptcha();
	}
	
	private function displayCaptcha(){
		$helper_Captcha = \core\Memory::helpers('Captcha');
		$helper_Captcha->generateCapcha();
	}
}

$obj_Captcha	= new Captcha();
unset($obj_Captcha);