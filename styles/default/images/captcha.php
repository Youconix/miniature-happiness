<?php
define('NIV','../../../');
define('PROCESS','1');
require(NIV.'include/BaseClass.php');

class Captcha extends BaseClass {
	public function __construct(){
		$this->init();
		$this->displayCaptcha();
	}
	
	private function displayCaptcha(){
		$helper_Captcha = Memory::helpers('Captcha');
		$helper_Captcha->generateCapcha();
	}
}

$obj_Captcha	= new Captcha();
unset($obj_Captcha);