<?php

namespace core\models;

/**
 * Description of Privileges
 *
 * @author rachelle
 */
class Privileges{
  private $service_QueryBuilder;
  private $model_Groups;
  
  /**
   * PHP 5 constructor
   * 
   * @param core\services\QueryBuilder  $service_QueryBuilder The query builder
   * @parma core\models\Groups      $model_Groups       The groups model
   */
  public function __construct(\core\services\QueryBuilder $service_QueryBuilder,\core\models\Groups $model_Groups){
    $this->service_QueryBuilder = $service_QueryBuilder;
    $this->model_Groups = $model_Groups;
  }
  

  /**
   * Checks or the user is logged in and haves enough rights.
   * Define the groep and level to overwrite the default rights for the page
   * 
   * @param	int	$i_group		The group id, optional
   * @param	int	$i_level		The minimun level, optional
   * @throws MemoryException	If the page rights are not defined with arguments or database
   */
  public function checkLogin($i_group = -1, $i_level = -1){
    if( stripos(\core\Memory::getPage(), '/phpunit') !== false ){
      /* Unit test */
      return;
    }

    if( $i_group == -1 || $i_level == -1 ){
      $this->service_Database->query("SELECT groupID,minLevel FROM " . DB_PREFIX . "group_pages WHERE page = '" . \core\Memory::getPage() . "'");
      if( $this->service_Database->num_rows() == 0 ){
        throw new \MemoryException("Unable to get the rights from " . \core\Memory::getPage() . ". Are they defined?");
      }
      $i_level = ( int ) $this->service_Database->result(0, 'minLevel');
      $i_group = ( int ) $this->service_Database->result(0, 'groupID');
    }

    /* Get redict url */
    $s_base = \core\Memory::getBase();
    $s_page = str_replace($s_base, '', $_SERVER[ 'REQUEST_URI' ]);

    if( $i_level == Session::FORBIDDEN ){
      if( $this->exists('login') ){
        define('USERID', $this->get('userid'));
      }

      return;
    }

    if( !$this->exists('login') ){
      header('HTTP/1.1 401 Autorisation required');
      if( !isset($_REQUEST[ 'AJAX' ]) || $_REQUEST[ 'AJAX' ] != true ){
        $this->set('page', $s_page);
        header("location: " . NIV . 'login.php');
      }
      die();
    }

    /* Check fingerprint */
    if( !$this->exists('fingerprint') || ($this->get('fingerprint') != $this->getFingerprint()) ){
      $this->destroyLogin();

      $this->set('page', $s_page);
      header('HTTP/1.1 401 Autorisation required');
      header("location: " . NIV . 'login.php');
      die();
    }

    $i_userid = ( int ) $this->get('userid');
    $i_userLevel = $this->model_Groups->getLevelByGroupID($i_group, $i_userid);

    if( ($i_userLevel < $i_level ) ){
      /* Insuffient rights or no access too the group
       * No access */
      header("HTTP/1.1 403 Forbidden");
      header('location: ' . NIV . 'errors/403.php');
      exit();
    }

    define('USERID', $i_userid);
  }
}