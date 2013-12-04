<?php
/** 
 * Session service class for managing sessions and login status                           
 *                                                                              
 * This file is part of Scripthulp framework                                    
 *                                                                              
 * @copyright 2012,2013,2014  Rachelle Scheijen                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                              
 * @changed   26/09/2012
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
class Service_Session extends Service {
    const FORBIDDEN         = -1;
    const USER              = 0;
    const MODERATOR         = 1;
    const ADMIN             = 2;
    
    const FORBIDDEN_COLOR   = 'grey';
    const USER_COLOR        = 'black';
    const MODERATOR_COLOR   = 'green';
    const ADMIN_COLOR       = 'red';

    /**
     * PHP 5 constructor
     */
    public function __construct(){
        $service_XmlSettings		= Memory::services('XmlSettings');

        $s_sessionSetName 		= $service_XmlSettings->get('settings/session/sessionName');
        $s_sessionSetPath 		= $service_XmlSettings->get('settings/session/sessionPath');
        $s_sessionExpire 		= $service_XmlSettings->get('settings/session/sessionExpire');

        if( $s_sessionSetName != '' ){
                @session_name($s_sessionSetName);
        }
        if( $s_sessionSetPath != '' ){
                @session_save_path($s_sessionSetPath);
        }
        if( $s_sessionExpire != '' ){
                @ini_set("session.gc_maxlifetime", $s_sessionExpire);
        }

        @session_start();
    }

    /**
     * Destructor
     */
    public function __destruct(){
        session_write_close();
    }

    /**
     * Sets the session with the given name and content
     *
     * @param		string	$s_sessionName	The name of the session
     * @param		mixed	$s_sessionData	The content of the session
     */
    public function set($s_sessionName,$s_sessionData){
        Memory::type('string',$s_sessionName);

        /* Set session */
        $_SESSION[$s_sessionName] = $s_sessionData;
    }

    /**
     * Deletes the session with the given name
     *
     * @param   string	$s_sessionName	The name of the session
     * @throws  IOException if the session does not exist
     */
    public function delete($s_sessionName){
        Memory::type('string',$s_sessionName);

        if( !$this->exists($s_sessionName)){
            throw new IOException('Session '.$s_sessionName.' does not exist');
        }

        unset($_SESSION[$s_sessionName]);
    }

    /**
     * Collects the content of the given session
     *
     * @param   string $s_sessionName	The name of the session
     * @return  string	The asked session
     * @throws  IOException if the session does not exist
     */
    public function get($s_sessionName){
        $s_data = null;

        Memory::type('string',$s_sessionName);

        if( !$this->exists($s_sessionName) ){
            throw new IOException('Session '.$s_sessionName.' does not exist');
        }

        $s_data = $_SESSION[$s_sessionName];

        return $s_data;
    }

    /**
     * Checks or the given session exists
     *
     * @param		string  $s_sessionName	The name of the session
     * @return		boolean True if the session exists, false if it does not
     */
    public function exists($s_sessionName){
        Memory::type('string',$s_sessionName);

        if( isset($_SESSION[$s_sessionName]) ){
            return true;
        }
        else{
            return false;
        }
    }

   /**
     * Renews the given session
     * 
     * @param   string  $s_sessionName  The name of the session
     */
    public function renew($s_sessionName){
		Memory::type('string',$s_sessionName);

        if( $this->exists($s_sessionName) ){
            $_SESSION[$s_sessionName] = $_SESSION[$s_sessionName];
        }
    }

    /**
     * Destroys all sessions currently set
     */
    public function destroy(){
        session_destroy();
    }

    /**
     * Logges the user in and sets the login-session
     *
     * @param	int		$i_userid		The userid of the user
     * @param	int		$s_username		The username of the user
     * @param	int		$i_lastLogin	The last login as a timestamp
     */
    public function setLogin($i_userid,$s_username,$i_lastLogin){
    	/* Get data */
    	$service_Database	= Memory::services('Database');
    	$service_Database->queryBinded("UPDATE ".DB_PREFIX."users SET lastLogin = ? WHERE id = ?",array('i','i'),array(time(),$i_userid));
    	
    	session_regenerate_id(true);
    	$_SESSION	= array();
    	
        $this->set('login','1');
        $this->set('userid',$i_userid);
        $this->set('username',$s_username);
        $this->set('fingerprint',$this->getFingerprint());
        $this->set('lastLogin',$i_lastLogin);
    }

    /**
     * Destroys the users login session
     */
    public function destroyLogin(){
        if( $this->exists('login') )     $this->delete('login');
        if( $this->exists('userid') )   	$this->delete('userid');
        if( $this->exists('username') )      $this->delete('username');
        if ($this->exists('fingerprint') )         $this->delete('fingerprint');
        if( $this->exists('lastLogin'))		$this->delete('lastLogin');
        if( $this->exists('type') )	$this->delete('type');
    }

    /**
     * Checks or the user is logged in and haves enough rights.
     * Define the groep and level to overwrite the default rights for the page
     * 
     * @param	int	$i_group		The group id, optional
     * @param	int	$i_level		The minimun level, optional
     * @throws MemoryException	If the page rights are not defined with arguments or database
     */
    public function checkLogin($i_group = -1,$i_level = -1){
        if( stripos(Memory::getPage(),'/phpunit') !== false ){
		/* Unit test */
		return;
	}
		
	$service_Database   = Memory::services('Database');
        
        if( $i_group == -1 || $i_level == -1 ){
	        $service_Database->query("SELECT groupID,minLevel FROM ".DB_PREFIX."group_pages WHERE page = '".Memory::getPage()."'");
	        if( $service_Database->num_rows() == 0 ){
	        	throw new MemoryException("Unable to get the rights from ".Memory::getPage().". Are they defined?");
	        }
	        $i_level    = (int) $service_Database->result(0,'minLevel');
	        $i_group    = (int) $service_Database->result(0,'groupID');
        }
        
        /* Call classes */
        $model_Groups		= Memory::models('Groups');
        $service_XmlSettings	= Memory::services('XmlSettings');

        /* Get redict url */
        $s_base	= Memory::getBase();
		$s_page	= str_replace($s_base,'',$_SERVER['REQUEST_URI']);
        
        if( $i_level == Session::FORBIDDEN ){
            if( $this->exists('login') )
                define('USERID',$this->get('userid'));
            
            return;
        }
        
        if( !$this->exists('login') ){
            header('HTTP/1.1 401 Autorisation required');
            if( !isset($_REQUEST['AJAX']) || $_REQUEST['AJAX'] != true ){
            	$this->set('page',$s_page);
                header("location: ".NIV.'login.php');
            }
            die();
        }
        
        /* Check fingerprint */
        if( !$this->exists('fingerprint') || ($this->get('fingerprint') != $this->getFingerprint()) ){
        	$this->destroyLogin();
            
            $this->set('page',$s_page);
            header('HTTP/1.1 401 Autorisation required');          
            header("location: ".NIV.'login.php');
            die();
        }

        $i_userid       = (int) $this->get('userid');
        $i_userLevel	= $model_Groups->getLevelByGroupID($i_group,$i_userid);

        if( ($i_userLevel < $i_level) ){
            /* Insuffient rights or no access too the group
            * No access */
        	header("HTTP/1.1 403 Forbidden");
            header('location: '.NIV.'errors/403.php');
            exit();
        }
        
        define('USERID',$i_userid);
    }
    
    /**
     * Returns the visitors browser fingerprint
     * 
     * @return string	The fingerprint
     */
    public function getFingerprint(){    	
    	return sha1($_SERVER['REMOTE_ADDR'].'-'.$_SERVER['HTTP_USER_AGENT'].'-'.$_SERVER['HTTP_HOST'].'-'.$_SERVER['SERVER_SIGNATURE'].'-'.strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']).'-'.$_SERVER['HTTP_ACCEPT_ENCODING']);
    }
}

class_alias('Service_Session', 'Session');
?>
