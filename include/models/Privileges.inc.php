<?php

namespace core\models;

/**
 * Checks the access privileges from the current page
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     2.0
 * @changed   02/08/2014
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
class Privileges{
  private $model_Config;
  private $service_QueryBuilder;
  private $service_Session;
  private $model_Groups;
  
  /**
   * PHP 5 constructor
   * 
   * @param core\services\QueryBuilder  $service_QueryBuilder The query builder
   * @param core\models\Groups      $model_Groups       The groups model
   * @param core\services\Session   $service_Session    The session service
   * @param core\models\Config  $model_Config       The config model
   */
  public function __construct(\core\services\QueryBuilder $service_QueryBuilder,\core\models\Groups $model_Groups,
          \core\services\Session   $service_Session, \core\models\Config $model_Config){
    $this->service_QueryBuilder = $service_QueryBuilder->createBuilder();
    $this->model_Groups = $model_Groups;
    $this->service_Session  = $service_Session;
    $this->model_Config = $model_Config;
  }
  

  /**
   * Checks or the user is logged in and haves enough rights.
   * Define the groep and level to overwrite the default rights for the page
   * 
   * @param	int	$i_group		The group id, optional
   * @param	int	$i_level		The minimun level, optional
   * @param     int     $i_commandLevel         The minimun level for the command, optional
   * @throws MemoryException	If the page rights are not defined with arguments or database
   */
  public function checkLogin($i_group = -1, $i_level = -1,$i_commandLevel = -1){
    \core\Memory::type('int',$i_group);
    \core\Memory::type('int',$i_level);
    \core\Memory::type('int',$i_commandLevel);
      
    if( stripos($this->model_Config->getPage(), '/phpunit') !== false ){
      /* Unit test */
      return;
    }

    if( $i_group == -1 || $i_level == -1 ){
      $this->service_QueryBuilder->select('group_pages','groupID,minLevel')->getWhere()->addAnd('page','s',$this->model_Config->getPage());
      $service_Database = $this->service_QueryBuilder->getResult();
      
      if( $service_Database->num_rows() == 0 ){
        throw new \MemoryException("Unable to get the rights from " . $this->model_Config->getPage() . ". Are they defined?");
      }
      $i_level = ( int ) $service_Database->result(0, 'minLevel');
      $i_group = ( int ) $service_Database->result(0, 'groupID');
    }
    
    if( $i_level == \core\services\Session::FORBIDDEN ){
      if( $this->service_Session->exists('login') ){
        define('USERID', $this->get('userid'));
      }

      return;
    }

    /* Get redict url */
    $s_base = $this->model_Config->getBase();
    $s_page = str_replace($s_base, '', $_SERVER[ 'REQUEST_URI' ]);

    $this->checkloginStatus($s_page); 
    
    $this->checkFingerprint($s_page);

    /* Check fingerprint */
    $i_userid = ( int ) $this->service_Session->get('userid');
    $i_userLevel = $this->model_Groups->getLevelByGroupID($i_group, $i_userid);

    if( ($i_userLevel < $i_level ) ){
      /* Insuffient rights or no access too the group
       * No access */
      header("HTTP/1.1 403 Forbidden");
      \core\Memory::redirect('errors/403.php');
    }
    
    $this->checkCommand($i_commandLevel,$i_userid);

    define('USERID', $i_userid);
  }
  
  /**
   * Checks the login status
   * 
   * @param String $s_page  The current page
   */
  private function checkloginStatus($s_page){
    if( !$this->service_Session->exists('login') ){
      header('HTTP/1.1 401 Autorisation required');
      if( !isset($_REQUEST[ 'AJAX' ]) || $_REQUEST[ 'AJAX' ] != true ){
        $this->set('page', $s_page);
        \core\Memory::redirect('login');
      }
      die();
    }
  }
  
  /**
   * Checks the fingerprint
   * 
   * @param String $s_page  The current page
   */
  private function checkFingerprint($s_page){
    if( !$this->service_Session->exists('fingerprint') || ($this->service_Session->get('fingerprint') != $this->service_Session->getFingerprint()) ){
      $this->service_Session->destroyLogin();

      $this->set('page', $s_page);
      header('HTTP/1.1 401 Autorisation required');
      \core\Memory::redirect('login');
    }
  }
  
  /**
   * Checks the command privaliges
   * 
   * @param int $i_commandLevel     The minimun command access level, -1 for auto detect
   * @param int $i_userid           The userid
   */
  private function checkCommand($i_commandLevel,$i_userid){
    if( $i_commandLevel != -1 ){
        $this->service_QueryBuilder->select('group_pages_command','groupID,minLevel')->getWhere()->addAnd(
            array('page','command'),array('s','s'),array($this->model_Config->getPage(),$this->model_Config->getCommand()));
        $service_Database = $this->service_QueryBuilder->getResult();
        if( $service_Database->num_rows() > 0 ){
            $i_level = ( int ) $service_Database->result(0, 'minLevel');
            $i_group = ( int ) $service_Database->result(0, 'groupID');
            
            $i_commandLevel = $this->model_Groups->getLevelByGroupID($i_group, $i_userid);
        }
    }
    
    if( $i_commandLevel < $i_level ){
        /* Insuffient rights
       * No access */
      header("HTTP/1.1 403 Forbidden");
      \core\Memory::redirect('errors/403.php');
    }
  }
}