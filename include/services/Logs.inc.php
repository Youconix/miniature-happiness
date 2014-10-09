<?php

namespace core\services;

/**
 * Log-handler for manipulating log files
 * The log files are written in de logs directory in de data dir (default admin/data)
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 		2014,2015,2016  Rachelle Scheijen
 * @author    		Rachelle Scheijen
 * @version       1.0
 * @since         1.0
 * @date          12/01/2006
 * @changed   		30/03/2014
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
class Logs extends Service{

  protected $service_File;
  protected $service_FileData;
  protected $s_directory;
  protected $s_errorLog;

  /**
   * PHP 5 constructor
   * 
   * @param core\services\File      $service_File     The file service
   * @param core\services\FileData  $service_FileData The file details service
   */
  public function __construct(\core\services\File $service_File, \core\services\FileData $service_FileData){
    $this->service_File = $service_File;
    $this->service_FileData = $service_FileData;

    $this->s_directory = DATA_DIR . 'logs/';

    /* Get error log location */
    $s_address = $this->makeAddress('error');
    $this->s_errorLog = realpath($s_address);
    if( !$this->s_errorLog ){
      touch($s_address);
      $this->s_errorLog = realpath($s_address);
    }
  }

  /**
   * Writes the data to the login log or makes a new one
   *
   * @param	String  $s_username	The username
   * @param	String	$s_status	The login status (failed|success)
   * @param	int		$i_tries	The number of login tries
   * @param	String	$s_openID	openID type, default empty
   * @throws	Exception when the log can not be written
   */
  public function loginLog($s_username, $s_status, $i_tries, $s_openID = ''){
    if( empty($s_openID) ){
      $s_log = 'Login to account ' . $s_username . ' from IP : ' . $_SERVER[ 'REMOTE_ADDR' ] . ' for ' . $i_tries . ' tries. Status : ' . $s_status . "\n";
    }
    else {
      $s_log = 'Login to account ' . $s_username . ' from IP : ' . $_SERVER[ 'REMOTE_ADDR' ] . ' with openID ' . $s_openID . '. Status : ' . $s_status . "\n";
    }

    $this->setLog('login', $s_log, date('Ym'));
  }

  /**
   * Writes the data to the security log or makes a new one
   *
   * @param  String  $s_log  The content of the entry
   * @throws	Exception when the log can not be written
   */
  public function securityLog($s_log){
    $s_log .= '  IP : ' . $_SERVER[ 'REMOTE_ADDR' ] . "\n";
    $this->setLog('security', $s_log, date('Ym'));
  }

  /**
   * Writes the data to the error log or makes a new one
   *
   * @param   String  $s_log  The content of the entry
   * @throws	Exception when the log can not be written
   */
  public function errorLog($s_log){
    $this->service_File->writeFirstFile($this->s_errorLog, $s_log, 0666);
  }
  
  public function accountBlockLog($s_username,$i_attemps){
    $s_log = 'The account ' . $s_username . ' is disabled on ' . date('d-m-Y H:i:s') . ' after '.$i_attemps.' failed login attempts.\n\n System';
    
    $this->setLog('accountBlock', $s_log, date('Ym'));
  }
  
  public function ipBlockLog($i_attemps){
    $s_log = 'The IP ' . $_SERVER[ 'REMOTE_ADDR' ] . ' is blocked on ' . date('d-m-Y H:i:s') . ' after '.$i_attemps.' failed login attempts. \n\n System';
    
    $this->setLog('accountBlock', $s_log, date('Ym'));
  }

  /**
   * Writes the data to the log or makes a new one
   *
   * @param  String  $s_name The name of the log
   * @param  String  $s_log  The content of the log
   * @param  String  $s_date The date the log is made. In format YYYYmm, optional default this month
   * @throws	Exception when the log can not be written
   */
  public function setLog($s_name, $s_log, $s_date = ''){
    Memory::type('string', $s_name);
    Memory::type('string', $s_log);

    if( $s_name == 'error' ){
      $this->errorLog($s_log);
      return;
    }

    $s_address = $this->makeAddress($s_name);

    if( !$this->service_File->exists($s_address) ){
      /* Log does not exist */
      $this->service_File->writeFile($s_address, $s_log, 0666);
    }
    else {
      /* Write data to begin of log */
      $this->service_File->writeFirstFile($s_address, $s_log, 0666);
    }
  }

  /**
   * Reads the given log
   *
   * @param  String  $s_name The name of the log
   * @param  String  $s_date The date the log is made. In format YYYYmm, optional default this month
   * @return String  The content of the log
   * @throws IOException when the log does not exist or is not readable
   */
  public function readLog($s_name, $s_date = ''){
    Memory::type('string', $s_name);
    Memory::type('string', $s_date);

    $s_address = $this->makeAddress($s_name, $s_date);

    if( !$this->service_File->exists($s_address) ){
      throw new \IOException("Log " . $s_name . ' does not exist');
    }

    return $this->service_File->readFile($s_address);
  }

  /**
   * Deletes the given log
   *
   * @param  String  $s_name The name of the log
   * @param  String  $s_date The date the log is made. In format YYYYmm, optional default this month
   * @throws IOException when the log does not exist
   */
  public function deleteLog($s_name, $s_date = ''){
    Memory::type('string', $s_name);
    Memory::type('string', $s_date);

    $s_address = $this->makeAddress($s_name, $s_date);

    if( !$this->service_File->exists($s_address) ){
      throw new \IOException("Log " . $s_name . ' does not exist');
    }

    $this->service_File->deleteFile($s_address);
  }

  /**
   * Downloads the given log through a forced download (download dialog in browser)
   *
   * @param  String  $s_name The name of the log
   * @param  String  $s_date The date the log is made. In format YYYYmm, optional default this month
   * @throws IOException when the log does not exist
   */
  public function downloadLog($s_name, $s_date = ''){
    Memory::type('string', $s_name);
    Memory::type('string', $s_date);

    $s_address = $this->makeAddress($s_name, $s_date);

    if( !$this->service_File->exists($s_address) ){
      throw new \IOException("Log " . $s_name . ' does not exist');
    }

    $s_name = $s_name . '_' . $s_date . '.log';

    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$s_name");
    header("Content-Type: text/plain");
    header("Content-Transfer-Encoding: binary");
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($s_address));
    readfile($s_address);
  }

  /**
   * Displays the names of the logs (index-page)
   *
   * @return array   The logs
   */
  public function indexLogs(){
    $a_logsPre = $this->service_File->readDirectory($this->s_directory);
    $a_logs = array();
    foreach( $a_logsPre AS $s_item ){
      if( stripos($s_item, '.log') === false ) continue;

      $a_logs[] = $s_item;
    }

    return $a_logs;
  }

  /**
   * Checks or the given log exists
   *
   * @param  String  $s_name The name of the log
   * @param  String  $s_date The date the log is made. In format YYYYmm, optional default this month
   * @return boolean True if the log exists, otherwise false
   */
  public function checkLog($s_name, $s_date = ''){
    Memory::type('string', $s_name);
    Memory::type('string', $s_date);

    $s_address = $this->makeAddress($s_name, $s_date);

    return $this->service_File->exists($s_address);
  }

  /**
   * Generates the log url
   *
   * @param  String  $s_name The name of the log
   * @param  String  $s_date The date the log is made. In format YYYYmm, optional default this month
   * @return String  The generated url
   */
  protected function makeAddress($s_name, $s_date = ''){
    if( $s_date == '' ) $s_date = date("Ym");

    return $this->s_directory . $s_name . '_' . $s_date . '.log';
  }

  /**
   * Removes all the old logs
   * 
   * @return boolean	True if the logs are removed
   */
  public function clean(){
    try{
      $a_logs = $this->indexLogs();
      $i_year = date('Y');
      $i_month = date('m');

      foreach( $a_logs AS $s_log ){
        $s_date = substr($s_log, (strpos($s_log, '_') + 1), strlen($s_log));
        $a_date = array( 'month' => substr($s_date, 4, 2), 'year' => substr($s_date, 0, 4) );

        if( $a_date[ 'year' ] < $i_year || $a_date[ 'month' ] < $i_month ){
          $this->service_File->deleteFile($this->s_directory . $s_log);
        }
      }
      return true;
    }
    catch( \Exception $e ){
      \core\Memory::services('ErrorHandler')->error($e);
      return false;
    }
  }

  /**
   * Checks if the log is modified since the given time
   * 
   * @param	String  $s_name The name of the log
   * @param	int	    $i_timestamp	The timestamp to check
   * @return boolean	True if the log is modified	
   */
  public function isModifiedSince($s_name, $i_timestamp){
    $s_name = $this->makeAddress($s_name);
    if( !$this->service_File->exists($s_name) ){
      return false;
    }

    $i_modified = $this->service_FileData->getLastModified($s_name);

    return ($i_modified > $i_timestamp);
  }

}
?>
