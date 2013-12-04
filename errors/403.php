<?php
/**
 * Error 403 class
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

class Error403 extends BaseLogicClass { 	
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
		if( $_SERVER['REQUEST_URI'] == 'errors/403.php' ){
			/* PHP redirect */
			define('LEVEL',NIV);
			return;
		}
		
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
        header("HTTP/1.1 403 Forbidden");
        
        $service_Language	= Memory::services('Language');

        $this->service_Template->set('title',$service_Language->get('language/errors/error403/accessDenied'));

        $this->service_Template->set('notice',$service_Language->get('language/errors/error403/noRights'));        
    }
}

$obj_Error = new Error403();
unset($obj_Error);
