<?php
namespace core;

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
include_once(NIV.'core/BaseClass.php');

abstract class AdminLogicClass extends BaseClass  {
    protected $service_Security;
    protected $service_Session;
    protected $model_User;
    protected $s_language;
    
    public function __construct(){
     $this->init();
     
     $this->checkAjax();
    }
    
    protected function checkAjax(){
     if( !\Core\Memory::models('Config')->isAjax() ){
      exit();
     }
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
        if( !Memory::isAjax() )
        	$this->service_Template->set('noscript','<noscript>'.$this->service_Language->get('language/noscript').'</noscript>');
    }
}

?>
