<?php
/**
 * General landing page.
 *
 * @author:		Rachelle Scheijen <rachelle.scheijen@unixerius.nl>
 * @copyright	Rachelle Scheijen
 * @version	1.0
 * @since		1.0
 * @date made	24/09/12
 * @changed		24/09/12
 */
define('NIV','./');
use \core\Memory;

include(NIV.'core/BaseLogicClass.php');
class Index extends \core\BaseLogicClass  { 
    /**
     * PHP 5 constructor
     */
    public function __construct(){
        $this->init();

        $this->header();
        
        $this->menu();
        
        $this->content();

        $this->footer();
    }
    
    /**
     * Sets the index content
     */
    private function content(){
    	$this->service_Template->set('content',
			Memory::helpers('IndexInstall')
    	);
    }
}

$obj_index = new Index();
unset($obj_index);
?>