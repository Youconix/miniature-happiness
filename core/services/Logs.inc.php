<?php
namespace core\services;

/**
 * Miniature-happiness is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Miniature-happiness is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 *
 * Log-handler for manipulating log files
 * The log files are written in de logs directory in de data dir (default admin/data)
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 */
class Logs extends Service implements \Psr\Log\LoggerAwareInterface
{

    protected $obj_logger = null;
    
    /**
     * Returns if the object schould be traded as singleton
     *
     * @return boolean  True if the object is a singleton
     */
    public static function isSingleton(){
        return true;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger            
     * @return null
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        $this->obj_logger = $logger;
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
        
        $this->obj_logger->info($s_log, array(
            'type' => 'login'
        ));
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
        
        $this->obj_logger->error($s_log, array(
            'type' => 'security'
        ));
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
        $this->obj_logger->emergency($s_log, array(
            'type' => 'error'
        ));
    }

    public function accountBlockLog($s_username, $i_attemps)
    {
        $s_log = 'The account ' . $s_username . ' is disabled on ' . date('d-m-Y H:i:s') . ' after ' . $i_attemps . ' failed login attempts.\n\n System';
        
        $this->obj_logger->info($s_log, array(
            'type' => 'accountBlock'
        ));
    }

    public function ipBlockLog($i_attemps)
    {
        $s_log = 'The IP ' . $_SERVER['REMOTE_ADDR'] . ' is blocked on ' . date('d-m-Y H:i:s') . ' after ' . $i_attemps . ' failed login attempts. \n\n System';
        
        $this->obj_logger->info($s_log, array(
            'type' => 'accountBlock'
        ));
    }

    /**
     * Writes the data to the log or makes a new one
     *
     * @param String $s_name
     *            The name of the log
     * @param String $s_log
     *            The content of the log
     * @param array $context
     *            The context, add an exception under the key 'exception'
     */
    public function setLog($s_name, $s_log, $context = array())
    {
        $obj_loglevel = new \Psr\Log\LogLevel();
        
        if( !array_key_exists('level', $context) ){
            $level = $obj_loglevel::INFO;
        }
        else {
            $level = $context['level'];
            unset($context['level']);
        }
        
        $this->obj_logger->log($level, $message,$context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level            
     * @param string $message            
     * @param array $context            
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $this->obj_logger->log($level, $message,$context);
    }
    
    public function exception($exception){
        $obj_loglevel = new \Psr\Log\LogLevel();
        $this->log($obj_loglevel::CRITICAL, 'Uncaught exception',array('exception'=>$exception));
    }
}