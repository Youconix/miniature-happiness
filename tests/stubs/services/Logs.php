<?php
namespace tests\stubs\services;

if (! class_exists('\core\services\Logs')) {
    require (NIV . 'core/services/Logs.inc.php');
}

class DummyLogs extends \core\services\Logs
{

    public $s_loginLog = '';

    public $s_securityLog = '';

    public $s_errorLog = '';

    public $s_log = '';

    public function __construct()
    {}

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

    /**
     * Writes the data to the log or makes a new one
     *
     * @param String $s_name
     *            The name of the log
     * @param String $s_log
     *            The content of the log
     * @param String $s_date
     *            The date the log is made. In format YYYYmm, optional default this month
     * @throws Exception when the log can not be written
     */
    public function setLog($s_name, $s_log, $s_date = '')
    {
        Memory::type('string', $s_name);
        Memory::type('string', $s_log);
        
        $this->s_log .= $s_log;
    }

    /**
     * Reads the given log
     *
     * @param String $s_name
     *            The name of the log
     * @param String $s_date
     *            The date the log is made. In format YYYYmm, optional default this month
     * @return String The content of the log
     * @throws IOException when the log does not exist or is not readable
     */
    public function readLog($s_name, $s_date = '')
    {
        Memory::type('string', $s_name);
        Memory::type('string', $s_date);
        
        switch ($s_name) {
            case 'login':
                return $this->s_loginLog;
            
            case 'error':
                return $this->s_errorLog;
            
            case 'security':
                return $this->s_securityLog;
            
            default:
                return $this->s_log;
        }
    }

    /**
     * Deletes the given log
     *
     * @param String $s_name
     *            The name of the log
     * @param String $s_date
     *            The date the log is made. In format YYYYmm, optional default this month
     * @throws IOException when the log does not exist
     */
    public function deleteLog($s_name, $s_date = '')
    {
        Memory::type('string', $s_name);
        Memory::type('string', $s_date);
        
        switch ($s_name) {
            case 'login':
                $this->s_loginLog = '';
                break;
            
            case 'error':
                $this->s_errorLog = '';
                break;
            
            case 'security':
                $this->s_securityLog = '';
                break;
            
            default:
                $this->s_log = '';
                break;
        }
    }

    /**
     * Downloads the given log through a forced download (download dialog in browser)
     *
     * @param String $s_name
     *            The name of the log
     * @param String $s_date
     *            The date the log is made. In format YYYYmm, optional default this month
     * @throws IOException when the log does not exist
     */
    public function downloadLog($s_name, $s_date = '')
    {
        Memory::type('string', $s_name);
        Memory::type('string', $s_date);
    }

    /**
     * Displays the names of the logs (index-page)
     *
     * @return array The logs
     */
    public function indexLogs()
    {
        return array();
    }

    /**
     * Checks or the given log exists
     *
     * @param String $s_name
     *            The name of the log
     * @param String $s_date
     *            The date the log is made. In format YYYYmm, optional default this month
     * @return boolean True if the log exists, otherwise false
     */
    public function checkLog($s_name, $s_date = '')
    {
        Memory::type('string', $s_name);
        Memory::type('string', $s_date);
        
        return true;
    }
}
?>

