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

include(NIV.'include/BaseLogicClass.php');
class Index extends BaseLogicClass  { 
    /**
     * PHP 5 constructor
     */
    public function __construct(){
        $this->init();

        $this->title();

        $this->header();
        
        $this->menu();
        
        $this->content();

        $this->footer();
    }

    /**
     * Sets the title to the template-parser
     */
    private function title(){
        $s_title    = $this->service_Language->get('language/index/title');

        $this->service_Template->set('title',$s_title);
    }
    
    /**
     * Sets the index content
     */
    private function content(){
    	
    }
}

$obj_index = new Index();
unset($obj_index);
?>