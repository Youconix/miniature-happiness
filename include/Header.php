<?php

/**
 * Site header
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
class Header {
    private $service_XmlSettings;
    private $service_Template;
    private $service_Language;
    private $model_User;

    /**
     * Starts the class header
     */
    public function __construct() {
        $this->init();

        $this->createHeader();
    }

    /**
     * Destructor
     */
    public function __destruct() {
        $this->service_XmlSettings = null;
        $this->service_Template = null;
        $this->service_Language = null;
        $this->model_User = null;
    }

    /**
     * Inits the class header
     */
    private function init() {
        $this->service_XmlSettings = Memory::services('XmlSettings');
        $this->service_Template = Memory::services('Template');
        $this->service_Language = Memory::services('Language');
        $this->model_User = Memory::models('User');
    }

    /**
     * Generates the header
     */
    private function createHeader() {
        $this->service_Template->loadTemplate('header','header.tpl');
        $this->service_Template->set('slogan',$this->service_Language->get('language/slogan'));
        
        $a_languages = array(
            'nl' => 'Nederlands',
            'en' => 'English'
        );

        $a_keys = array_keys($a_languages);

        $s_url	= $_SERVER['PHP_SELF'].'?';
        if( !empty($_SERVER['QUERY_STRING']) ){
        	$s_url .= $_SERVER['QUERY_STRING'];
        	$s_url = preg_replace("#lang=(" . implode('|', $a_keys) . ")*#si", '', $s_url);
        	$s_url = str_replace(array('&&','?&'),array('&','?'),$s_url);
        	
        	$s_last	= substr($s_url,-1);
        	if( strpos($s_url,'?') === false )
        		$s_url .= '?';
        	else if( $s_last != '&' )
        		$s_url .= '&';
        }
        $s_url	= str_replace('&', '&amp;', $s_url);
        
        foreach ($a_keys AS $s_key) {
        	$a_data	= array(
        		'url'=>$s_url.'lang='.$s_key,
        		'language'=>$a_languages[$s_key],
        		'betweenLanguage' => ' | '
        	);
        	$this->service_Template->setBlock('headerLanguage',$a_data);
        	
        	$bo_first	= false;
        }
        
        $obj_User = $this->model_User->get();
        if ( !is_null($obj_User->getID()) ) {
        	if( $obj_User->isAdmin(GROUP_SITE) ){
            	$s_welcome = $this->service_Language->get('language/header/adminWelcome');
        	}
        	else {
        		$s_welcome = $this->service_Language->get('language/header/userWelcome');
        	}
        	
            $this->service_Template->set('welcomeHeader','<a href="' .NIV.'profile.php?id=' . $obj_User->getID() . '" style="color:' . $obj_User->getColor() . '">' . $s_welcome . ' ' . $obj_User->getUsername() . '</a>');
        }
    }
}

?>
