<?php
define('NIV','./');
define('PROCESS','true');
define('DEBUG','true');

class Test {
	
	public function __construct(){
		require(NIV.'include/Memory.php');
		\core\Memory::startUp();
		
		$helper_Calender = \core\Memory::helpers('Calender');
		echo( $helper_Calender->generateCalender() );
	}
}

$obj_test = new Test();