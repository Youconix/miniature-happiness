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
abstract class LoggerParent extends \core\services\Service implements \Psr\Log\LoggerInterface
{

    protected $service_Mailer;

    protected $a_address;

    protected $obj_loglevel;

    protected $s_host;

    public function setMailer(\core\services\Mailer $service_Mailer, $a_address, $s_host)
    {
        $this->service_Mailer = $service_Mailer;
        $this->a_address = $a_address;
        $this->obj_loglevel = new \Psr\Log\LogLevel();
        $this->s_host = $s_host;
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
            $this->service_Mailer->logDeamon($message, $this->a_address,$this->s_host);
        }
    }
}