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
if( !defined('NIV') ){
  define('NIV','../');
}
define('TEMPLATE','errors/404/index');
require(NIV.'includes/BaseLogicClass.php');

class Error404 extends \includes\BaseLogicClass {
	/**
	 * Starts the class Error504
	 */
	public function __construct(){
		parent::__construct();
		
		$this->displayError();
	}

	private function displayError(){
		header("HTTP/1.1 404 Not found");

		$service_Language	= \core\Memory::services('Language');

		$this->service_Template->set('title',$service_Language->get('language/errors/error404/notFound'));

		$this->service_Template->set('notice',$service_Language->get('language/errors/error404/pageMissing'));
		
		if( defined('DEBUG') ){
			echo('tester');
			print_r($_SESSION);
		}
		if( defined('DEBUG') && isset($_SESSION['error']) ){
			$this->service_Template->set('debug_notice',$_SESSION['error']);
		}
	}
}

$obj_Error = new Error404();
unset($obj_Error);
