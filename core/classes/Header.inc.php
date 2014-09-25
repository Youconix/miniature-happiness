<?php

namespace core\classes;

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
class Header{

  private $service_Template;
  private $service_Language;
  private $model_User;

  /**
   * Starts the class header
   */
  public function __construct(\core\services\Template $service_Template, \core\services\Language $service_Language, \core\models\User $model_User){
    $this->service_Template = $service_Template;
    $this->service_Language = $service_Language;
    $this->model_User = $model_User;

    $this->createHeader();

    $this->displayLanguageFlags();
  }

  /**
   * Generates the header
   */
  protected function createHeader(){
    $this->service_Template->loadTemplate('header', 'header.tpl');
    
    $obj_User = $this->model_User->get();
    if( is_null($obj_User->getID()) ){
      return;
    }

    if( $obj_User->isAdmin(GROUP_SITE) ){
      $s_welcome = $this->service_Language->get('header/adminWelcome');
    }
    else {
      $s_welcome = $this->service_Language->get('header/userWelcome');
    }

    $this->service_Template->set('welcomeHeader', '<a href="' . NIV . 'profile.php?id=' . $obj_User->getID() . '" style="color:' . $obj_User->getColor() . '">' . $s_welcome . ' ' . $obj_User->getUsername() . '</a>');
  }

  /**
   * Displays the language change flags
   */
  protected function displayLanguageFlags(){
    $a_languages = $this->service_Language->getLanguages(); 
    $a_languagesCodes = $this->service_Language->getLanguageCodes();
    

    $s_url = $_SERVER[ 'PHP_SELF' ] . '?';
    if( !empty($_SERVER[ 'QUERY_STRING' ]) ){
      $s_url .= $_SERVER[ 'QUERY_STRING' ];
      $s_url = preg_replace("#lang=(" . implode('|', $a_keys) . ")*#si", '', $s_url);
      $s_url = str_replace(array( '&&', '?&' ), array( '&', '?' ), $s_url);

      $s_last = substr($s_url, -1);
      if( strpos($s_url, '?') === false ) $s_url .= '?';
      else if( $s_last != '&' ) $s_url .= '&';
    }
    $s_url = str_replace('&', '&amp;', $s_url);

    $s_flags = '';
    foreach( $a_languages AS $s_code ){
      $s_language = (array_key_exists($s_code, $a_languagesCodes)) ?  $a_languagesCodes[$s_code] : $s_code;
     
      $s_flags .= '<a href="'.$s_url.'lang='.$s_code.'">'.
        '<img src="{style_dir}/flags/'.$s_code.'.png" alt="'.$s_language.'" title="'.$s_language.
        '"></a>';
    }
    
    $this->service_Template->set('header_languages',$s_flags);
  }

}
?>