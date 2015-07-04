<?php
namespace tests\stubs\services;

class Logs implements \Logger
{

    public $s_loginLog = '';

    public $s_securityLog = '';

    public $s_errorLog = '';

    public $s_log = '';

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
        
        $this->s_loginLog .= $s_log;
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
        $this->s_securityLog .= $s_log;
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
        $this->s_errorLog .= $this->s_errorLog;
    }
    
    public function accountBlockLog($s_username, $i_attemps){}
    
    public function ipBlockLog($i_attemps){}
    
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
    public function setLog($s_name, $s_log, $context = array());

    /**
     * Logs with an arbitrary level.
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array()){}
    
    public function exception($exception){}
    
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array()){}
    
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
    public function alert($message, array $context = array()){}
    
    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function critical($message, array $context = array()){}
    
    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function error($message, array $context = array()){}
    
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
    public function warning($message, array $context = array()){}
    
    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function notice($message, array $context = array()){}
    
    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function info($message, array $context = array()){}
    
    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function debug($message, array $context = array()){}
}
?>

