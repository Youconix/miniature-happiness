<?php
/** 
 * Site menu
 *                                                                              
 * This file is part of Scripthulp framework  
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   12/07/12
 * 
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

class Menu{
    private $service_Template;
    private $service_Language;
    private $obj_User;
    private $model_Groups;

    /**
     * Starts the class menu
     */
    public function __construct(){
        $this->init();

        $this->generateMenu();
    }

    /**
     * Destructor
     */
    public function __destruct(){
        $this->service_Template = null;
        $this->service_Language	= null;
        $this->obj_User         = null;
        $this->model_Groups     = null;
    }

    /**
     * Inits the class menu
     */
    private function init(){
        $this->service_Template = Memory::services('Template');
        $this->service_Language = Memory::services('Language');
        $this->obj_User         = Memory::models('User')->get();
        $this->model_Groups     = Memory::models('Groups');
    }

    /**
     * Generates the menu 
     */
    private function generateMenu(){
        $s_language = Memory::services('Language')->getLanguage();
        
        if( defined('USERID') ){
        	$this->loggedIn();
        	
        	if( $this->obj_User->isAdmin(GROUP_ADMIN) ){
        		$this->service_Template->loadTemplate('menu','menu_admin.tpl');
        			
        		$this->service_Template->set('adminPanel','<a href="{NIV}admin/"  class="subadmin">'.$this->service_Language->get('language/menu/adminPanel').'</a>');
    		}
			else {
				$this->service_Template->loadTemplate('menu','menu.tpl');
			}
        }
        else {
			$this->service_Template->loadTemplate('menu','menu.tpl');

        	$this->loggedout();
        }
	}
    
    /**
     * Displays the logged out items
     */
    private function loggedout(){
    	$this->service_Template->set('home',$this->service_Language->get('language/menu/home'));
    	$this->service_Template->set('login','<a href="{NIV}login.php">'.$this->service_Language->get('language/menu/login').'</a>');
    	$this->service_Template->set('registration',$this->service_Language->get('language/login/registration'));
    }
    
    /**
     * Displays the logged in items
     */
    private function loggedIn(){
    	$this->service_Template->set('home',$this->service_Language->get('language/menu/home'));
    	$this->service_Template->set('logout','<a href="{NIV}logout.php">'.$this->service_Language->get('language/menu/logout').'</a>');
    }
}
?>
