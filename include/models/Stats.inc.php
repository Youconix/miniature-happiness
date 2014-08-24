<?php

namespace core\models;

/**
 * Stats model.    Contains the site statistics
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   05/05/2014
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
class Stats extends Model{

  private $i_date;

  /**
   * PHP5 constructor
   * 
   * @param \core\services\QueryBuilder $service_QueryBuilder The query builder
   * @param \core\services\Security $service_Security The security service
   */
  public function __construct(\core\services\QueryBuilder $service_QueryBuilder, \core\services\Security $service_Security){
    parent::__construct($service_QueryBuilder, $service_Security);

    $this->i_date = mktime(0, 0, 0, date("n"), 1, date("Y"));
  }

  /**
   * Counts the visitor hit
   *
   * @param   String  $s_ip   The IP address
   * @param   String  $s_page	The current page
   * @return  Boolean True if the visitor is unique, otherwise false
   */
  public function saveIP($s_ip, $s_page){
    \core\Memory::type('string', $s_ip);
    \core\Memory::type('string', $s_page);

    $i_datetime = mktime(date("H"), 0, 0, date("n"), date('j'), date("Y"));
    $this->service_QueryBuilder->update('stats_hits', 'amount', 'l', 'amount + 1')->getWhere()->addAnd('datetime', 'i', $i_datetime);

    if( $this->service_QueryBuilder->getResult()->affected_rows() == 0 ){
      $this->service_QueryBuilder->insert('stats_hits', array( 'amount', 'datetime' ), array( 'i', 'i' ), array( 1, $i_datetime ))->getResult();
    }

    $this->service_QueryBuilder->update('stats_pages', 'amount', 'l', 'amount = amount + 1');
    $this->service_QueryBuilder->getWhere()->addAnd(array( 'datetime', 'name' ), array( 'i', 's' ), array( $i_datetime, $s_page ));
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->affected_rows() == 0 ){
      $this->service_QueryBuilder->insert('stats_pages', array( 'amount', 'datetime', 'name' ), array( 'i', 'i', 's' ), array( 1, $i_datetime, $s_page ))->getResult();
    }

    /* Check unique */
    $i_begin = mktime(0, 0, 0, date("n"), date('j'), date("Y"));
    $i_end = mktime(23, 59, 59, date("n"), date('j'), date("Y"));
    $this->service_QueryBuilder->select('stats_unique', 'id')->getWhere()->addAnd(array( 'ip', 'datetime' ), array( 's', 'i', 'i' ), array( $s_ip, $i_begin, $i_end ), array( '=', 'BETWEEN' ));

    $service_Database = $this->service_QueryBuilder->getResult();
    if( $service_Database->num_rows() != 0 ){
      return false;
    }

    /* Unique visitor */
    $this->service_QueryBuilder->insert('stats_unique', array( 'ip', 'datetime' ), array( 's', 'i' ), array( $s_ip, time() ))->getResult();

    return true;
  }

  /**
   * Saves the visitors OS
   *
   * @param   String   $s_os   	The OS
   * @param   String   $s_osType	The OS family
   */
  public function saveOS($s_os, $s_osType){
    \core\Memory::type('string', $s_os);
    \core\Memory::type('string', $s_osType);

    $this->service_QueryBuilder->update('stats_OS', 'amount', 'l', 'amount + 1')->getWhere()->addAnd(array( 'datetime', 'name', 'type' ), array( 'i', 's', 's' ), array( $this->i_date, $s_os, $s_osType ));
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->affected_rows() == 0 ){
      $this->service_QueryBuilder->insert('stats_OS', array( 'name', 'amount', 'datetime', 'type' ), array( 's', 'i', 'i', 's' ), array( $s_os, 1, $this->i_date, $s_osType ))->getResult();
    }
  }

  /**
   * Saves the visitors browser
   *
   * @param   String   $s_browser  The browser
   * @param   String   $s_version	The browser version
   */
  public function saveBrowser($s_browser, $s_version){
    \core\Memory::type('string', $s_browser);
    \core\Memory::type('string', $s_version);

    $this->service_QueryBuilder->update('stats_browser', 'amount', 'l', 'amount + 1')->getWhere()->addAnd(array( 'datetime', 'name', 'version' ), array( 'i', 's', 's' ), array( $this->i_date, $s_browser, $s_version ));

    $service_Database = $this->service_QueryBuilder->getResult();
    if( $service_Database->affected_rows() == 0 ){
      $this->service_QueryBuilder->insert('stats_browser', array( 'name', 'amount', 'datetime', 'version' ), array( 's', 'i', 'i', 's' ), array( $s_browser, 1, $this->i_date, $s_version ))->getResult();
    }
  }

  /**
   * Saves the visitors reference
   *
   * @param   String  $s_reference    The reference
   */
  public function saveReference($s_reference){
    \core\Memory::type('string', $s_reference);

    $s_reference = str_replace(array( '\\', 'http://', 'https://' ), array( '/', '', '' ), $s_reference);
    $a_reference = explode('/', $s_reference);
    $s_reference = $a_reference[ 0 ];

    $this->service_QueryBuilder->update('stats_reference', 'amount', 'l', 'amount + 1')->getWhere()->addAnd(array( 'datetime', 'name' ), array( 'i', 's' ), array( $this->i_date, $s_reference ));
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->affected_rows() == 0 ){
      $this->service_QueryBuilder->insert('stats_reference', array( 'name', 'amount', 'datetime' ), array( 's', 'i', 'i' ), array( $s_reference, 1, $this->i_date ))->getResult();
    }
  }

  /**
   * Saves the visitors screen size
   *
   * @param  	int	$i_width	The screen width
   * @param	int	$i_height	The screen height
   */
  public function saveScreenSize($i_width, $i_height){
    \core\Memory::type('int', $i_width);
    \core\Memory::type('int', $i_height);

    $this->service_QueryBuilder->update('stats_screenSizes', 'amount', 'l', 'amount + 1')->getWhere()->addAnd(array( 'datetime', 'width', 'height' ), array( 'i', 'i', 'i' ), array( $this->i_date, $i_width, $i_height ));
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->affected_rows() == 0 ){
      $this->service_QueryBuilder->insert('stats_screenSizes', array( 'width', 'height', 'amount', 'datetime' ), array( 'i', 'i', 'i', 'i' ), array( $i_width, $i_height, 1, $this->i_date ))->getResult();
    }
  }

  /**
   * Saves the visitors screen colors
   *
   * @param   String  $s_screenColors The screen colors
   */
  public function saveScreenColors($s_screenColors){
    \core\Memory::type('string', $s_screenColors);

    $this->service_QueryBuilder->update('stats_screenColors', 'amount', 'l', 'amount + 1')->getWhere()->addAnd(array( 'datetime', 'name' ), array( 'i', 's' ), array( $this->i_date, $s_screenColors ));
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->affected_rows() == 0 ){
      $this->service_QueryBuilder->insert('stats_screenColors', array( 'name', 'amount', 'datetime' ), array( 's', 'i', 'i' ), array( $s_screenColors, 1, $this->i_date ))->getResult();
    }
  }

  /**
   * Returns the hits from the given month
   *
   * @param   int $i_date The month as timestamp
   * @return  array   The hits
   */
  public function getHits($i_date){
    \core\Memory::type('int', $i_date);

    $i_end = mktime(0, 0, 0, (date('n', $i_date) + 1), 1, date('Y', $i_date));

    $a_hits = array();
    $this->service_QueryBuilder->select('stats_hits', 'amount,datetime')->group('datetime')->getWhere()->addAnd('datetime', array( 'i', 'i' ), array( $i_date, $i_end ), 'BETWEEN');
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() > 0 ){
      $a_hitsPre = $service_Database->fetch_assoc();

      foreach( $a_hitsPre AS $a_hit ){
        $i_day = date('j', $a_hit[ 'datetime' ]);
        $i_hours = date('G', $a_hit[ 'datetime' ]);

        if( !array_key_exists($i_day, $a_hits) ){
          $a_hits[ $i_day ] = array();
        }

        if( !array_key_exists($i_hours, $a_hits[ $i_day ]) ){
          $a_hits[ $i_day ][ $i_hours ] = $a_hit;
        }
        else {
          $a_hits[ $i_day ][ $i_hours ][ 'amount' ] += $a_hit[ 'amount' ];
        }
      }
    }

    return $a_hits;
  }

  /**
   * Returns the pages from the given month
   *
   * @param   int $i_date The month as timestamp
   * @return  array   The pages
   */
  public function getPages($i_date){
    \core\Memory::type('int', $i_date);

    $i_end = mktime(0, 0, 0, (date('n', $i_date) + 1), 1, date('Y', $i_date));

    $a_pages = array();
    $this->service_QueryBuilder->select('stats_pages', 'name,amount')->group('name')->order('amount', 'DESC');
    $this->service_QueryBuilder->getWhere()->addAnd('datetime', array( 'i', 'i' ), array( $i_date, $i_end ), 'BETWEEN');
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() > 0 ){
      $a_data = $this->service_Database->fetch_assoc();

      foreach( $a_data AS $a_page ){
        if( array_key_exists($a_page[ 'name' ], $a_pages) ){
          $a_pages[ $a_page[ 'name' ] ][ 'amount' ] += $a_page[ 'amount' ];
        }
        else {
          $a_pages[ $a_page[ 'name' ] ] = $a_page;
        }
      }
    }

    return $a_pages;
  }

  /**
   * Returns the unique visitors from the given month
   *
   * @param   int $i_date The month as timestamp
   * @return  array   The unique visitors
   */
  public function getUnique($i_date){
    \core\Memory::type('int', $i_date);

    $i_end = mktime(0, 0, 0, (date('n', $i_date) + 1), 1, date('Y', $i_date));

    $a_unique = array();
    $this->service_QueryBuilder->select('stats_unique', 'id AS amount,datetime')->group('datetime')->getWhere()->addAnd('datetime', array( 'i', 'i' ), array( $i_date, $i_end ), 'BETWEEN');
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() > 0 ){
      $a_uniquePre = $service_Database->fetch_assoc();

      foreach( $a_uniquePre AS $a_hit ){
        $i_day = date('j', $a_hit[ 'datetime' ]);
        if( !array_key_exists($i_day, $a_unique) ){
          $a_unique[ $i_day ] = $a_hit;
        }
        else {
          $a_unique[ $i_day ][ 'amount' ] += $a_hit[ 'amount' ];
        }
      }
    }

    return $a_unique;
  }

  /**
   * Returns the operating systems from the given month
   * Grouped by operating system
   *
   * @param   int $i_month The month as timestamp
   * @return  array   The operating systems
   */
  public function getOS($i_month){
    \core\Memory::type('int', $i_month);

    $i_end = mktime(0, 0, 0, (date('n', $i_month) + 1), 1, date('Y', $i_month));

    $a_OS = array();
    $this->service_QueryBuilder->select('stats_OS', 'name,' . $this->service_QueryBuilder->getSum('amount', 'amount') . ',type')->group('type')->order('amount', 'DESC');
    $this->service_QueryBuilder->getWhere('datetime', array( 'i', 'i' ), array( $i_month, $i_end ), 'BETWEEN');
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() > 0 ){
      $a_OS = $service_Database->fetch_assoc_key('name');
    }

    return $a_OS;
  }

  /**
   * Returns the operating systems from the given month
   *
   * @param   int $i_month The month as timestamp
   * @return  array   The operating systems
   */
  public function getOSLong($i_month){
    \core\Memory::type('int', $i_month);

    $i_end = mktime(0, 0, 0, (date('n', $i_month) + 1), 1, date('Y', $i_month));

    $a_OS = array();
    $this->service_QueryBuilder->select('stats_OS', 'name,amount,type')->order('amount', 'DESC')->getWhere()->addAnd('datetime', array( 'i', 'i' ), array( $i_month, $i_end ), 'BETWEEN');
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() > 0 ){
      $a_OS = $service_Database->fetch_assoc();
    }

    return $a_OS;
  }

  /**
   * Returns the browsers from the given month.
   * Grouped by browser
   *
   * @param   int $i_date The month as timestamp
   * @return  array   The browsers
   */
  public function getBrowsers($i_month){
    \core\Memory::type('int', $i_month);

    $i_end = mktime(0, 0, 0, (date('n', $i_month) + 1), 1, date('Y', $i_month));

    $a_browsers = array();
    $this->service_QueryBuilder->select('stats_browser', 'name,' . $this->service_QueryBuilder->getSum('amount', 'amount') . ',version')->group('name')->order('amount', 'DESC');
    $this->service_QueryBuilder->getWhere()->addAnd('datetime', array( 'i', 'i' ), array( $i_month, $i_end ), 'BETWEEN');
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() > 0 ){
      $a_browsers = $service_Database->fetch_assoc_key('name');
    }

    return $a_browsers;
  }

  /**
   * Returns the browsers from the given month
   *
   * @param   int $i_date The month as timestamp
   * @return  array   The browsers
   */
  public function getBrowsersLong($i_month){
    \core\Memory::type('int', $i_month);

    $i_end = mktime(0, 0, 0, (date('n', $i_month) + 1), 1, date('Y', $i_month));

    $a_browsers = array();
    $this->service_QueryBuilder->select('stats_browser', 'name,amount,version')->order('amount', 'DESC');
    $this->service_QueryBuilder->getWhere()->addAnd('datetime', array( 'i', 'i' ), array( $i_month, $i_end ), 'BETWEEN');
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() > 0 ){
      $a_browsers = $service_Database->fetch_assoc();
    }

    return $a_browsers;
  }

  /**
   * Returns the screen colors from the given month
   *
   * @param   int $i_month The month as timestamp
   * @param   int $i_limit	Set for limiting the amount of data
   * @return  array   The screen colors
   */
  public function getScreenColors($i_month, $i_limit = -1){
    \core\Memory::type('int', $i_month);
    \core\Memory::type('int', $i_limit);

    $i_end = mktime(0, 0, 0, (date('n', $i_month) + 1), 1, date('Y', $i_month));

    $a_screenColors = array();
    $this->service_QueryBuilder->select('stats_screenColors', 'name,amount')->getWhere()->addAnd('datetime', array( 'i', 'i' ), array( $i_month, $i_end ), 'BETWEEN');
    if( $i_limit > 0 ){
      $this->service_QueryBuilder->limit($i_limit);
    }
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() > 0 ){
      $a_screenColors = $service_Database->fetch_assoc_key('name');
    }

    return $a_screenColors;
  }

  /**
   * Returns the screen sizes from the given month
   *
   * @param   int $i_month The month as timestamp
   * @param   int $i_limit	Set for limiting the amount of data
   * @return  array   The screen sizes
   */
  public function getScreenSizes($i_month, $i_limit = -1){
    \core\Memory::type('int', $i_month);
    \core\Memory::type('int', $i_limit);

    $i_end = mktime(0, 0, 0, (date('n', $i_month) + 1), 1, date('Y', $i_month));

    $a_screenSizes = array();
    $this->service_QueryBuilder->select('stats_screenSizes', 'width,height,amount')->order('width', 'DESC', 'height', 'DESC');
    $this->service_QueryBuilder->getWhere()->addAnd('datetime', array( 'i', 'i' ), array( $i_month, $i_end ), 'BETWEEN');
    if( $i_limit > 0 ){
      $this->service_QueryBuilder->limit($i_limit);
    }
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() > 0 ){
      $a_screenSizes = $service_Database->fetch_assoc();
    }

    return $a_screenSizes;
  }

  /**
   * Returns the references from the given month
   *
   * @param   int $i_month The month as timestamp
   * @return  array   The references
   */
  public function getReferences($i_month){
    \core\Memory::type('int', $i_month);

    $i_end = mktime(0, 0, 0, (date('n', $i_month) + 1), 1, date('Y', $i_month));

    $a_references = array();
    $this->service_QueryBuilder->select('stats_reference', $this->service_QueryBuilder->getSum('amount', 'amount') . ',name')->order('amount', 'DESC');
    $this->service_QueryBuilder->getWhere()->addAnd('datetime', array( 'i', 'i' ), array( $i_month, $i_end ), 'BETWEEN');
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() > 0 ){
      $a_references = $service_Database->fetch_assoc_key('name');
    }

    return $a_references;
  }

  /**
   * Returns the lowest date saved as a timestamp
   *
   * @return  int the lowest date
   */
  public function getLowestDate(){
    $i_date = -1;
    $this->service_QueryBuilder->select('stats_hits', $this->service_QueryBuilder->getMinimun('datetime', 'date'));
    $service_Database = $this->service_QueryBuilder->getResult();

    if( $service_Database->num_rows() > 0 ){
      $i_date = ( int ) $service_Database->result(0, 'date');
    }

    return $i_date;
  }

  /**
   * Cleans the stats from a year old
   *
   * @throws    DBException     If the clearing failes
   */
  public function cleanStatsYear(){
    $i_time = mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y") - 1);
    $this->cleanStats($i_time);
  }

  /**
   * Cleans the stats from a month old
   *
   * @throws    DBException     If the clearing failes
   */
  public function cleanStatsMonth(){
    $i_time = mktime(date("H"), date("i"), date("s"), date("n") - 1, date("j"), date("Y"));
    $this->cleanStats($i_time);
  }

  /**
   * Deletes the stats older than the given timestamp
   *
   * @param int $i_maxDate	The minimun timestamp to keep data
   * @throws    DBException     If the clearing failes
   */
  private function cleanStats($i_maxDate){
    \core\Memory::type('int', $i_maxDate);
      
    try{
      $this->service_QueryBuilder->transaction();

      $this->service_QueryBuilder->delete('stats_hits')->getWhere()->addAnd('datetime', 'i', $i_maxDate, '<');
      $this->service_QueryBuilder->getResult();
      $this->service_QueryBuilder->delete('stats_pages')->getWhere()->addAnd('datetime', 'i', $i_maxDate, '<');
      $this->service_QueryBuilder->getResult();
      $this->service_QueryBuilder->delete('stats_unique')->getWhere()->addAnd('datetime', 'i', $i_maxDate, '<');
      $this->service_QueryBuilder->getResult();
      $this->service_QueryBuilder->delete('stats_screenSizes')->getWhere()->addAnd('datetime', 'i', $i_maxDate, '<');
      $this->service_QueryBuilder->getResult();
      $this->service_QueryBuilder->delete('stats_screenColors')->getWhere()->addAnd('datetime', 'i', $i_maxDate, '<');
      $this->service_QueryBuilder->getResult();
      $this->service_QueryBuilder->delete('stats_browser')->getWhere()->addAnd('datetime', 'i', $i_maxDate, '<');
      $this->service_QueryBuilder->getResult();
      $this->service_QueryBuilder->delete('stats_reference')->getWhere()->addAnd('datetime', 'i', $i_maxDate, '<');
      $this->service_QueryBuilder->getResult();
      $this->service_QueryBuilder->delete('stats_OS')->getWhere()->addAnd('datetime', 'i', $i_maxDate, '<');
      $this->service_QueryBuilder->getResult();

      $this->service_QueryBuilder->commit();
    }
    catch( \DBException $e ){
      $this->service_QueryBuilder->rollback();
      throw $e;
    }
  }
}
?>
