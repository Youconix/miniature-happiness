<?php
/** 
 * Admin homepage                                                               
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 * @changed    25/09/10                                                         
 *                                                                              
 * Scripthulp framework is free software: you can redistribute it and/or modify 
 * it under the terms of the GNU Lesser General Public License as published by  
 * the Free Software Foundation, either version 3 of the License, or            
 * (at your option) any later version.                                          
 *                                                                              
 * Scripthulp framework is distributed in the hope that it will be useful,      
 * but WITHOUT ANY WARRANTY; without even the implied warranty of               
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                
 * GNU General Public License for more details.                                 
 *                                                                              
 * You should have received a copy of the GNU Lesser General Public License     
 * along with Scripthulp framework.  If not, see <http://www.gnu.org/licenses/>.
 */
 
define('NIV','../');

include(NIV.'include/AdminLogicClass.php');

class Index extends AdminLogicClass  {    
	private $service_Logs;
	
    /**
     * PHP 5 constructor
     */
    public function __construct(){
        $this->init();

        $this->title();
        
        $this->security();
        
        $this->errors();

        $this->header();

        $this->menu();

        $this->footer();
    }

    /**
     * Destructor
     */
    public function __destruct(){
    	$this->service_Logs	= null;
    	
        parent::__destruct();
    }

    /**
     * Inits the class Index
     */
    protected function init(){
        parent::init();

        $this->service_Logs	= Memory::services('Logs');
     }
    

    /**
     * Sets the title to the template-parser
     */
    private function title(){
        try {
            $s_title    = $this->service_Language->get('language/admin/index/title');

            $this->service_Template->set('title',$s_title);
        }
        catch(Exception $e){
            $this->service_ErrorHandler->error($e);
        }
    }
    
    /**
     * Displays the security log
     */
    private function security(){
        if( $this->service_Logs->isModifiedSince('security',0) ){
        	$this->service_Template->loadTemplate('securityView','admin/index/security.tpl');
        	
        	$this->service_Template->set('titleSecurity',$this->service_Language->get('language/admin/index/securityTitle'));
        	$this->service_Template->set('securityLog',nl2br($this->service_Logs->readLog('security')));
        }
    }
    
    /**
     * Displays the error log
     */
    private function errors(){
    	if( $this->service_Logs->isModifiedSince('error',0) ){
    		$this->service_Template->loadTemplate('errorView','admin/index/error.tpl');
    		
    		$this->service_Template->set('titleError',$this->service_Language->get('language/admin/index/errorTitle'));
    		$this->service_Template->set('errorLog',nl2br($this->service_Logs->readLog('error')));
    	}
    }
}

$obj_index = new Index();
unset($obj_index);
?>