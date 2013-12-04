<?php 
abstract class GeneralJS {
	protected $service_Language;
	
	public function __construct(){
		require(NIV.'include/Memory.php');
		Memory::startUp();
		
		$this->service_Language	= Memory::services('Language');
		
		$this->sendHeaders();
		
		$this->display();
	}
	
	protected function sendHeaders(){
		header('Content-Type: text/javascript');
		header('Expires: Thu, '.date('d-M-y',(time() + 604800)).' 00:00:01 GMT'); //cache for one week
	}
	
	abstract protected function display();
}
?>