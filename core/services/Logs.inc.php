<?php
namespace core\services;

/**
 * Log-handler for manipulating log files
 * The log files are written in de logs directory in de data dir (default admin/data)
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2014,2015,2016 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 *        @date 12/01/2006
 *
 *       
 *        Scripthulp framework is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU Lesser General Public License as published by
 *        the Free Software Foundation, either version 3 of the License, or
 *        (at your option) any later version.
 *       
 *        Scripthulp framework is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *        GNU General Public License for more details.
 *       
 *        You should have received a copy of the GNU Lesser General Public License
 *        along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 */
class Logs extends Service
{

    protected $model_Config;

    protected $service_FileData;

    protected $service_File;

    protected $s_directory;

    protected $s_logSetting;

    protected $s_errorLog = null;

    /**
     * PHP 5 constructor
     *
     * @param core\services\File $service_File
     *            The file service
     * @param core\services\FileData $service_FileData
     *            The file data service
     * @param core\models\Config $model_Config
     *            The configuration
     */
    public function __construct(\core\services\File $service_File, \core\services\FileData $service_FileData, \core\models\Config $model_Config)
    {
        $this->service_File = $service_File;
        $this->model_Config = $model_Config;
        $this->service_FileData = $service_FileData;
        
        $this->s_directory = $model_Config->getLogLocation();
        $this->s_logSetting = $model_Config->logging();
        
        if ($this->s_logSetting == 'default') {
            /* Get error log location */
            $this->s_errorLog = realpath($this->s_directory . 'error.log');
            if (! $this->s_errorLog) {
                touch($s_address);
                $this->s_errorLog = realpath($s_address);
            }
        }
    }

    protected function logRotate($s_name)
    {
        if (! $this->service_File->exists($this->s_directory . $s_name . '.log')) {
            return;
        }
        
        if ($this->service_FileData->getFileSize($this->s_directory . $s_name . '.log') >= $this->model_Config->getLogfileMaxSize()) {
            if ($this->service_File->exists($this->s_directory . $s_name . '.1.log')) {
                $this->service_File->moveFile($this->s_directory . $s_name . '.1.log', $this->s_directory . $s_name . '.2.log');
            }
            
            $this->service_File->moveFile($this->s_directory . $s_name . '.log', $this->s_directory . $s_name . '.1.log');
        }
    }

    /**
     * Writes the data to the login log or makes a new one
     *
     * @param String $s_username
     *            username
     * @param String $s_status
     *            status (failed|success)
     * @param int $i_tries
     *            of login tries
     * @param String $s_openID
     *            default empty
     * @throws Exception when the log can not be written
     */
    public function loginLog($s_username, $s_status, $i_tries, $s_openID = '')
    {
        if (empty($s_openID)) {
            $s_log = 'Login to account ' . $s_username . ' from IP : ' . $_SERVER['REMOTE_ADDR'] . ' for ' . $i_tries . ' tries. Status : ' . $s_status . "\n";
        } else {
            $s_log = 'Login to account ' . $s_username . ' from IP : ' . $_SERVER['REMOTE_ADDR'] . ' with openID ' . $s_openID . '. Status : ' . $s_status . "\n";
        }
        
        $this->setLog('login', $s_log);
    }

    /**
     * Writes the data to the security log or makes a new one
     *
     * @param String $s_log
     *            The content of the entry
     * @throws Exception when the log can not be written
     */
    public function securityLog($s_log)
    {
        $s_log .= '  IP : ' . $_SERVER['REMOTE_ADDR'] . "\n";
        $this->setLog('security', $s_log);
    }

    /**
     * Writes the data to the error log or makes a new one
     *
     * @param String $s_log
     *            The content of the entry
     * @throws Exception when the log can not be written
     */
    public function errorLog($s_log)
    {
        $this->setLog('error', $s_log);
    }

    public function accountBlockLog($s_username, $i_attemps)
    {
        $s_log = 'The account ' . $s_username . ' is disabled on ' . date('d-m-Y H:i:s') . ' after ' . $i_attemps . ' failed login attempts.\n\n System';
        
        $this->setLog('accountBlock', $s_log);
    }

    public function ipBlockLog($i_attemps)
    {
        $s_log = 'The IP ' . $_SERVER['REMOTE_ADDR'] . ' is blocked on ' . date('d-m-Y H:i:s') . ' after ' . $i_attemps . ' failed login attempts. \n\n System';
        
        $this->setLog('accountBlock', $s_log);
    }

    /**
     * Writes the data to the log or makes a new one
     *
     * @param String $s_name
     *            The name of the log
     * @param String $s_log
     *            The content of the log
     * @throws Exception when the log can not be written
     */
    public function setLog($s_name, $s_log)
    {
        \core\Memory::type('string', $s_name);
        \core\Memory::type('string', $s_log);
        
        switch ($this->s_logSetting) {
            case 'default':
                $this->logRotate($s_name);
                
                if ($s_name == 'error') {
                    $s_destination = $this->s_errorLog;
                } else {
                    $s_destination = $this->s_directory . $s_name . '.log';
                }
                if (! error_log($s_log . "\n", 3, $s_destination)) {
                    if ($s_name != 'error') {
                        throw new \IOException('Could not write to ' . $s_name . ' log on location ' . $this->s_directory . '.');
                    }
                }
                break;
            
            case 'error_log':
                if (! error_log($s_log)) {
                    if ($s_name != 'error') {
                        throw new \IOException('Could not write to ' . ini_get('error_log') . '.');
                    }
                }
                break;
            
            case 'sys_log':
                $s_ident = $this->model_Config->getHost() . ' : ' . $s_name;
                if (openlog($s_ident, LOG_PID | LOG_PERROR, LOG_USER)) {
                    if ($s_name == 'error') {
                        $type = LOG_ERR;
                    } else {
                        $type = LOG_INFO;
                    }
                    
                    syslog($type, $s_log);
                    
                    closelog();
                } else 
                    if ($s_type != 'error') {
                        throw new \IOException('Could not write to system log.');
                    }
        }
    }
}
?>
