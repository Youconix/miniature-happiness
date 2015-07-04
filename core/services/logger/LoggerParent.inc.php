<?php
namespace core\services\logger;

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
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 2.0
 */
abstract class LoggerParent extends \core\services\Service implements \Logger
{

    protected $service_Mailer;

    protected $a_address;

    protected $obj_loglevel;

    protected $s_host;
    
    public function __construct(\core\services\Mailer $mailer,\Config $config,\Psr\Log\LogLevel $loglevel){
        $this->service_Mailer = $mailer;
        $this->a_address = $config->getAdminAddress();
        $this->obj_loglevel = $loglevel;
        $this->s_host = $config->getHost();
    }
    
    /**
     * Returns if the object schould be treated as singleton
     *
     * @return boolean True if the object is a singleton
     */
    public static function isSingleton()
    {
        return true;
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
    
        if (! array_key_exists('level', $context)) {
            $level = $obj_loglevel::INFO;
        } else {
            $level = $context['level'];
            unset($context['level']);
        }
    
        $this->obj_logger->log($level, $message, $context);
    }
    
    public function exception($exception)
    {
        $obj_loglevel = new \Psr\Log\LogLevel();
        $this->log($obj_loglevel::CRITICAL,'Throw '.get_class($exception), array(
            'exception' => $exception
        ));
    }

    /**
     * System is unusable.
     *
     * @param string $message            
     * @param array $context            
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        $obj_loglevel = $this->obj_loglevel;
        
        $this->log($obj_loglevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message            
     * @param array $context            
     * @return null
     */
    public function alert($message, array $context = array())
    {
        $obj_loglevel = $this->obj_loglevel;
        
        $this->log($obj_loglevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message            
     * @param array $context            
     * @return null
     */
    public function critical($message, array $context = array())
    {
        $obj_loglevel = $this->obj_loglevel;
        
        $this->log($obj_loglevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message            
     * @param array $context            
     * @return null
     */
    public function error($message, array $context = array())
    {
        $obj_loglevel = $this->obj_loglevel;
        
        $this->log($obj_loglevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message            
     * @param array $context            
     * @return null
     */
    public function warning($message, array $context = array())
    {
        $obj_loglevel = $this->obj_loglevel;
        
        $this->log($obj_loglevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message            
     * @param array $context            
     * @return null
     */
    public function notice($message, array $context = array())
    {
        $obj_loglevel = $this->obj_loglevel;
        
        $this->log($obj_loglevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message            
     * @param array $context            
     * @return null
     */
    public function info($message, array $context = array())
    {
        $obj_loglevel = $this->obj_loglevel;
        
        $this->log($obj_loglevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message            
     * @param array $context            
     * @return null
     */
    public function debug($message, array $context = array())
    {
        $obj_loglevel = $this->obj_loglevel;
        
        $this->log($obj_loglevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level            
     * @param string $message            
     * @param array $context            
     * @return null
     */
    abstract public function log($level, $message, array $context = array());

    /**
     * Parses the level, message and context
     *
     * @param \Psr\Log\LogLevel $level
     *            The log level
     * @param string $message
     *            The message
     * @param array $context
     *            The context
     * @return string The parsed message
     */
    protected function parseContext($level, $message, $context)
    {
        $message = $level . ' ' . $message;
        $s_exception = '';
        
        foreach ($context as $key => $value) {
            if ($key == 'exception') {
                $s_exception = $value->getMessage() . "\n" . $value->getTraceAsString();
            } else {
                $message .= "\n" . $key . ' : ' . $value;
            }
        }
        
        if (! empty($s_exception)) {
            $message .= "\n" . $s_exception;
        }
        
        return $message;
    }

    /**
     * Warns the admin trough email is the level is high enough
     *
     * @param \Psr\Log\LogLevel $level
     *            The log level
     * @param string $message
     *            The message
     */
    protected function warnAdmin($level, $message)
    {
        $obj_loglevel = $this->obj_loglevel;
        
        if (($level == $obj_loglevel::EMERGENCY) || ($level == $obj_loglevel::ALERT) || ($level == $obj_loglevel::CRITICAL)) {
            $this->service_Mailer->logDeamon($message, $this->a_address, $this->s_host);
        }
    }
}