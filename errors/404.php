<?php
/**
 * Error 404 class
 *
 * @name                error.php
 * @package		error_docs
 * @lisence:		http://scripthulp.com/licence.php
 * @author:		REJ Scheijen    Scripthulp
 * @version		1.0
 * @since		1.0
 * @date made:		01-05-2010
 * @date last changed:	02-05-2010
 */
define('NIV','../');
require(NIV.'include/BaseLogicClass.php');

class Error404 extends BaseLogicClass {
	/**
	 * Starts the class Error504
	 */
	public function __construct(){
		$this->init();
		 
		$this->displayError();
		 
		$this->header();
		 
		$this->menu();
		 
		$this->footer();
	}

	/**
	 * Defines the level constant for the GUI
	 */
	protected function defineLevel(){
		$s_page = str_replace(Memory::getBase(), '', $_SERVER['REQUEST_URI']);
		if( substr($s_page,0,1) == '/' )	$s_page = substr($s_page,1);

		$i_number = count(explode('/', $s_page));

		if ($i_number == 1) {
			$s_level = './';
		} else {
			$s_level = '';
			for ($i = 1; $i < $i_number; $i++) {
				$s_level .= '../';
			}
		}

		define('LEVEL',$s_level);
	}

	private function displayError(){
		header("HTTP/1.1 404 Not found");

		$service_Language	= Memory::services('Language');

		$this->service_Template->set('title',$service_Language->get('language/errors/error404/notFound'));

		$this->service_Template->set('notice',$service_Language->get('language/errors/error404/pageMissing'));
	}
}

$obj_Error = new Error404();
unset($obj_Error);
