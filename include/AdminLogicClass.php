<?php
/**
 * General admin GUI parent class
 * This class is abstract and should be inheritanced by every admin controller with a gui
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 		2012,2013,2014  Rachelle Scheijen
 * @author    		Rachelle Scheijen
 * @version		1.0
 * @since		    1.0
 * @date			12/01/2006
 * @changed   		03/03/2010
 * @see				include/BaseClass.php
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
include_once(NIV.'include/BaseClass.php');

abstract class AdminLogicClass extends BaseClass {
    protected $service_Security;
    protected $service_Session;
    protected $model_User;
    protected $s_language;

    /**
     * Destructor
     * 
     * @see BaseClass::__destruct()
     */
    public function __destruct(){
        $this->service_Session  = null;
        $this->model_User       = null;
        $this->s_language       = null;

        parent::__destruct();
    }
    
    /**
     * Inits the class AdminLogicClass
     * 
     * @see BaseClass::init()
     */
    protected function init(){
    	define('LAYOUT','admin');
    	
    	$this->forceSSL();
    	
        parent::init();

        $this->service_Session  = Memory::services('Session');
        $this->model_User       = Memory::models('User');
        
        $this->s_language   = $this->service_Language->getLanguage();        
        $this->service_Template->headerLink('<link rel="stylesheet" href="{style_dir}css/admin/cssAdmin.css"/>');
        $this->service_Template->headerLink('<script src="{NIV}js/admin/admin.php" type="text/javascript"></script>');
        if( !Memory::isAjax() )
        	$this->service_Template->set('noscript','<noscript>'.$this->service_Language->get('language/noscript').'</noscript>');
    }

    /**
     * Displays the site header
     */
    protected function header(){
        /* Call header */
        include(NIV.'include/Header.php');
        $obj_header = new Header();
        unset($obj_header);
    }

    /**
     * Displays the site and admin menu
     */
    protected function menu(){
        /* Call Menu's */
    	include(NIV.'include/Menu.php');
    	if( file_exists(NIV.'include/Menu_override.php') ){
    		include(NIV.'include/Menu_override.php');
    		$obj_menu   = new MenuOverride();
    	}
    	else {
    		$obj_menu   = new Menu();
    	}
    	
    	unset($obj_menu);
    	
        include(NIV.'include/MenuAdmin.php');
        $obj_menu   = new MenuAdmin();
        unset($obj_menu);
    }

    /**
     * Displays the site footer
     */
    protected function footer(){
        /* Call footer */
        include(NIV.'include/Footer.php');
        $obj_footer = new Footer();
        unset($obj_footer);
    }
}

?>
