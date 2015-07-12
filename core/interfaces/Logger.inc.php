<?php 
#require_once(NIV.'vendor/Psr/Log/LoggerAwareInterface.php');
#require_once(NIV.'vendor/Psr/Log/LoggerInterface.php');
#require_once(NIV.'vendor/Psr/Log/LogLevel.php');
interface Logger extends \Psr\Log\LoggerInterface {
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
    public function loginLog($s_username, $s_status, $i_tries, $s_openID = '');
    
    /**
     * Writes the data to the security log or makes a new one
     *
     * @param String $s_log
     *            The content of the entry
     * @throws Exception when the log can not be written
     */
    public function securityLog($s_log);
    
    /**
     * Writes the data to the error log or makes a new one
     *
     * @param String $s_log
     *            The content of the entry
     * @throws Exception when the log can not be written
     */
    public function errorLog($s_log);
    
    public function accountBlockLog($s_username, $i_attemps);
    
    public function ipBlockLog($i_attemps);
    
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
    public function log($level, $message, array $context = array());
    
    public function exception($exception);
}
?>
