<?php
/** 
 * Framework installer file
 * 
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   17/06/12
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

define('NIV','../');
define('PROCESS','1');

/* Style dir */
define('STYLEDIR',NIV.'styles/default/');

require(NIV.'admin/SettingsMain.php');

class Install extends SettingsMain{
	private $service_Language;
	private $i_step = 1;
	private $s_layout;
	private $bo_error   = false;
        private $s_version = '2.0';
	
	private $a_output;

	/**
	 * PHP 5 constructor
	 */
	public function __construct(){
		$this->init();

		if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
			if( isset($_POST['command']) ){
				if( $_POST['command'] == 'checkLDAP' ){
					if( $this->checkLDAP($_POST['server'],$_POST['port']) ){
						echo('1');
					}
					else {
						echo('0');
					}
				}
				else if( $_POST['command'] == 'checkSMTP' ){
					if( $this->checkSMTP($_POST['server'],$_POST['port'],$_POST['username'],$_POST['password']) ){
						echo('1');
					}
					else {
						echo('0');
					}
				}
				else if( $_POST['command'] == 'checkDB' ){
					$a_data = array(
						'databaseType'  => $_POST['type'],
						'sqlUsername' => $_POST['username'],
						'sqlPassword' => $_POST['password'],
						'sqlDatabase' => $_POST['database'],
                		'sqlHost' => $_POST['host'],
						'sqlPort' => $_POST['port']
					);
					if( $this->checkDatabase($a_data) ){
						echo('1');
					}
					else {
						echo('0');
					}
				}
				return;
			}
			
			switch($this->i_step){
				case 3 :
					$this->settingsCheck();
					break;
					
				case 4 :
					$this->populateDB();
					break;

				case 5 :
					$this->standardUserSave();
					break;
			}
		}
		else {
			switch($this->i_step){
				case 1 :
					$this->mainscreen();
					break;
				
				case 2 :
					$this->a_output = array('result'=>1,'system'=>'','logs'=>'','settings'=>'','framework'=>'');
					
					$this->systemCheck();
					$this->frameworkCheck();
					
					echo('['.json_encode($this->a_output).']');
					exit();
					break;

				case 3 :
					$this->settingsScreen();
					break;

				case 5 :
					$this->standardUserScreen();
					break;
			}
		}

		$s_dataDir = 'admin/settings/';
		if( defined('DATA_DIR') ){	$s_dataDir = DATA_DIR.'/settings/'; }
		
		/* Get base */
		$s_base = $this->getBase();
		
		$this->s_layout = str_replace(array('{step}','{styledir}','{datadir}','{version}','{LEVEL}'),array($this->i_step,str_replace(NIV,'/',STYLEDIR),$s_dataDir,$this->s_version,$s_base),$this->s_layout);
		$this->s_layout = preg_replace("#{+[a-zA-Z_0-9]+}+#si", "", $this->s_layout);
		echo($this->s_layout);
	}

	/**
	 * Inits the class Install
	 */
	private function init(){
		/* Call session data */
		session_start();
		if( !isset($_SESSION['data']) ){
			$_SESSION['data']   = array();
		}
		else {
			$_SESSION['data']   = $_SESSION['data'];
		}

		if( isset($_GET['step']) && $_GET['step'] > 0 && $_GET['step'] <= 6 ){
			$this->i_step   = $_GET['step'];
		}
		else if( isset($_POST['step']) && $_POST['step'] > 0 && $_POST['step'] <= 6 ){
			$this->i_step   = $_POST['step'];
		}

		$this->s_layout = $this->readTemplate('layout');
		require(NIV.'include/language/Install.php');
		$this->service_Language = new Install_Language();
		
		$s_progress = '';
		for($i=1; $i<=6; $i++){
			$s_class = "grey";
			if( $i<= $this->i_step )	$s_class="current";
			
			$s_progress .= '<li class="'.$s_class.'">'.$i.'</li>';
		}
		$this->s_layout = str_replace('{progress}', $s_progress, $this->s_layout);
		
	
		if( !defined('DATA_DIR') ){
			define('DATA_DIR',NIV.'admin/data/');
		}
	}
	
	private function mainscreen(){		
	}
	
	/**
	 * Performs the system check
	 */
	private function systemCheck(){
		require(NIV.'install/systemCheck.php');
		$obj_check	= new SystemCheck();
		
		$s_settingsDir = DATA_DIR.'settings';
		$s_logsDir	 	= DATA_DIR.'logs';
		
		$s_output	= $obj_check->validate();
		if( !$obj_check->isValid()){
			$this->a_output['result'] = 0;
			$this->a_output['system'] = $s_output;
		}
		if( !$obj_check->checkWritable($s_settingsDir) ){
			$this->a_output['result'] = 0;
			$this->a_output['settings'] = $s_settingsDir;
		}
		if( !$obj_check->checkWritable($s_logsDir) ){
			$this->a_output['result'] = 0;
			$this->a_output['logs'] = $s_logsDir;
		}
	}

	/**
	 * Performs the framework check
	 */
	private function frameworkCheck(){
		require(NIV.'install/fileCheck.php');
		$obj_check	= new FileCheck();
		
		$s_output	= $obj_check->validate();
		if( !$obj_check->isValid()){
			$this->a_output['result'] = 0;
			$this->a_output['framework'] = $s_output;
		}
	}

	/**
	 * Displays the settings sreen
	 * 
	 * @param array $a_data		The settings
	 */
	private function settingsScreen($a_data = array()){		
		$s_template = $this->readTemplate('settings');

		if( count($a_data) == 0 ){
			$a_data = array(
                'base'      => substr(str_replace('install/index.php','',$_SERVER['PHP_SELF']),1),
                'url'       => 'http://'.$_SERVER['HTTP_HOST'], 'timezone'  => date_default_timezone_get(),
                'sessionName'   => '', 'sessionPath' => '','sessionExpire'=>'','language'=>'nl',
                'template'=>'','sqlUsername'=>'','sqlPassword'=> '','sqlDatabase' => '',
                'sqlHost' => 'localhost','sqlPort'=>'','databasePrefix'=>'SF_');
		}

		/* Parse select lists */
		$a_data['languages'] = $this->generateList($this->getLanguages(),$a_data['language']);
		$a_data['templates'] = $this->generateList($this->getTemplates(),$a_data['template']);
		$a_data['databases'] = $this->generateList($this->getDatabases(),$a_data['databaseType']);

		$s_template = $this->writeTemplate($a_data, $s_template);

		/* Set text */
		$a_keysText     = array('title','basedir','siteUrl','timezoneText','sessionTitle','sessionNameText','sessionPathText','sessionExpireText',
            'siteSettings','defaultLanguage','templateDir','databaseSettings','username','password','database','host',
            'port','type','buttonSave','databasePrefixText');
		$s_template = $this->writeTemplateText($a_keysText, 'step3',$s_template);

		$this->s_layout = str_replace('{content}',$s_template,$this->s_layout);
	}

	/**
	 * Checks the settings and saves them
	 */
	private function settingsCheck(){
		$a_data     = $_POST;
		$bo_error   = false;
		$s_error	 = '';

		/* Check fields */
		$a_check    = array('url','timezone','language','template');
		foreach($a_check AS $s_check){
			if( trim($a_data[$s_check]) == '' ){
				$s_error = $this->service_Language->get('step3/fieldsEmpty').'<br/>';
				$bo_error   = true;
				break;
			}
		}
		if( !empty($a_data['sessionExpire']) && !is_numeric($a_data['sessionExpire']) ){
			$s_error .= $this->service_Language->get('step3/sessionExpireInvalid').'<br/>';
			$bo_error   = true;
		}
		if( !in_array($a_data['language'],$this->getLanguages()) ){
			$s_error .= $this->service_Language->get('step3/languageInvalid').'<br/>';
			$bo_error   = true;
		}
		if( !in_array($a_data['template'],$this->getTemplates()) ){
			$s_error .= $this->service_Language->get('step3/templateInvalid').'<br/>';
			$bo_error   = true;
		}
 
		/* Check database data */
		if( !in_array($a_data['databaseType'],$this->getDatabases() ) ){
			$s_error .= $this->service_Language->get('step3/databaseTypeInvalid').'<br/>';
			$bo_error   = true;
		}
		else if( !$this->checkDatabase($a_data) ){
			$s_error     = $this->service_Language->get('step3/databaseInvalid').'<br/>';
			$bo_error   = true;
		}

		if( $bo_error ){
			$this->reportError($s_error);
						
			echo('error');
			die();
		}
		
		/* Generate settings file */
		require(NIV.'include/services/Hashing.inc.php');
					
		$s_salt	= $this->createSalt();
		$settings     = new DOMDocument('1.0', 'iso-8859-1');
		// We don't want to bother with white spaces
		$settings->preserveWhiteSpace = false;
		$settings->resolveExternals = true; // for character entities
		$settings->formatOutput = true;

		$root	= $settings->createElement('settings');
			
		/* Main */
		$main	= $settings->createElement('main');
		$main->appendChild($settings->createElement('nameSite',$a_data['base']));
		$main->appendChild($settings->createElement('url',$a_data['url']));
		$main->appendChild($settings->createElement('base',$a_data['base']));
		$main->appendChild($settings->createElement('timeZone',$a_data['timezone']));
		$main->appendChild($settings->createElement('salt',$s_salt));
		$root->appendChild($main);
		
		/* Login */
		$login = $settings->createElement('login');
		$login->appendChild($settings->createElement("normalLogin",$a_data['normalLogin']));
		$login->appendChild($settings->createElement("openID",$a_data['openID']));
		$login->appendChild($settings->createElement("lDAP",$a_data['lDAP']));
		$login->appendChild($settings->createElement("ldap_server",$a_data['ldap_server']));
		$login->appendChild($settings->createElement("ldap_port",$a_data['ldap_port']));
		$root->appendChild($login);
			
		/* Session */
		$session	= $settings->createElement('session');
		$session->appendChild($settings->createElement('sessionName',$a_data['sessionName']));
		$session->appendChild($settings->createElement('sessionPath',$a_data['sessionPath']));
		$session->appendChild($settings->createElement('sessionExpire',$a_data['sessionExpire']));
		$root->appendChild($session);
		
		/* Language */
		$root->appendChild($settings->createElement('defaultLanguage',$a_data['language']));

		/* Mail */
		$mail	= $settings->createElement('mail');
		$mail->appendChild($settings->createElement('senderName',$a_data['mail_name']));
		$mail->appendChild($settings->createElement('senderEmail',$a_data['mail_email']));
		$mail->appendChild($settings->createElement('SMTP',$a_data['smtp']));
		$mail->appendChild($settings->createElement('host',$a_data['smtp_host']));
		$mail->appendChild($settings->createElement('username',$a_data['smtp_username']));
		$mail->appendChild($settings->createElement('password',$a_data['smtp_password']));
		$mail->appendChild($settings->createElement('port',$a_data['smtp_port']));
		$root->appendChild($mail);

		/* Templates */
		$templates	= $settings->createElement('templates');
		$templates->appendChild($settings->createElement('dir',$a_data['template']));
		$root->appendChild($templates);

		/* Database */
		$databaseMain	= $settings->createElement('SQL');
		$databaseMain->appendChild($settings->createElement('prefix',$a_data['databasePrefix']));
		$databaseMain->appendChild($settings->createElement('type',$a_data['databaseType']));
		$database	= $settings->createElement($a_data['databaseType']);
		$database->appendChild($settings->createElement('username',$a_data['sqlUsername']));
		$database->appendChild($settings->createElement('password',$a_data['sqlPassword']));
		$database->appendChild($settings->createElement('database',$a_data['sqlDatabase']));
		$database->appendChild($settings->createElement('host',$a_data['sqlHost']));
		$database->appendChild($settings->createElement('port',$a_data['sqlPort']));
		$databaseMain->appendChild($database);
		$root->appendChild($databaseMain);
			
		$version	= $settings->createElement('version','2.0');
		$root->appendChild($version);
			
		$settings->appendChild($root);				

		$s_dir	= DATA_DIR.'/settings/';

		if( !is_writable($s_dir) ){
			$this->reportError($this->service_Language->get('step3/permissionFailure').' '.$s_dir.'.');
			echo('error');
			die();
		}
		
		$settings->save($s_dir.'/settings.xml');
		
		/* Check access */
		require_once(NIV.'include/services/CurlManager.inc.php');
		$service_Curl = new \core\services\CurlManager();
		
		$s_url = $_SERVER['HTTP_HOST'].'/'.$a_data['base'].'/admin/settings/settings.xml';
		$service_Curl->performGetCall($s_url,array());
		if( $service_Curl->getHeader() == 200 ){
			$this->reportError('The data-directory '.$a_data['base'].'/admin/settings is world readable! Installation canceled.');
			unlink($s_dir.'/settings.xml');
			
			echo('security error');
		}
		else {
			echo('ok');
		}
		die();
	}
	
	/**
	 * Populates the database
	 */
	private function populateDB(){
		require(NIV.'install/Database.php');
		$obj_Database	= new Database();
		
		try {
			$obj_Database->populateDatabase();
			
			echo('1');
		}
		catch(Exception $e){
			echo($e->getMessage());
			$this->reportError($e);
			echo('0');
		}
		exit();
	}
	
	/**
	 * Displays the admin user screen
	 * 
	 * @param array $a_data		The data
	 */
	private function standardUserScreen($a_data = array()){
		$s_template = $this->readTemplate('user');

		if( count($a_data) == 0 ){
			$a_data = array('nick'=> '','email'=> '');
		}
		/* Set text */
		$a_keysText     = array('headerText','nickText','emailText','password','password2','buttonSubmit');
		$s_template = $this->writeTemplateText($a_keysText, 'step5',$s_template);
		
		$this->s_layout = str_replace('{content}',$s_template,$this->s_layout);
	}
	
	/**
	 * Generates the admin user
	 */
	private function standardUserSave(){
		$a_data     = $_POST;
		$bo_error   = false;
		$s_error = '';

		/* Check fields */
		$a_check    = array('nick','email','password','password2');
		foreach($a_check AS $s_check){
			if( trim($a_data[$s_check]) == '' ){
				$s_error = $this->service_Language->get('step5/fieldsEmpty').'<br/>';
				$bo_error   = true;
				break;
			}
		}

		if( $a_data['password'] != $a_data['password2'] ){
			$s_error    .= $this->service_Language->get('step5/passwordInvalid').'<br/>';
			$bo_error   = true;
		}
		if( strlen($a_data['password']) < 8 ){
			$s_error .= 'The password is too short. At least 8 characters.';
			$bo_error   = true;
		}

		if( $bo_error ){
			echo('error');
			exit();
		}
		
		require(NIV.'install/Database.php');
		$obj_Database	= new Database();
			
		try {
			$obj_Database->createUser($a_data['nick'],$a_data['email'],$a_data['password']);
			
			echo('oke');
		}
		catch(Exception $e){
			$this->reportError($e);
			
			echo('error');
		}
		exit();
	}
	
	/**
	 * Reads the given template
	 * 
	 * @param string $s_template		The template name
	 * @return string	The template content
	 */
	private function readTemplate($s_template){
		$file       = fopen(STYLEDIR.'templates/install/'.$s_template.'.tpl','r');
		$s_content = fread($file,filesize(STYLEDIR.'templates/install/'.$s_template.'.tpl'));
		fclose($file);

		return $s_content;
	}

	/**
	 * Writes the values to the given keys on the given template
	 *
	 * @param	array	$a_keys		The keys
	 * @param	array	$a_values	The values
	 * @param	string	$s_template	The template to parse
	 * @return	string	The parsed template
	 */
	private function writeTemplate($a_values, $s_template) {
		$a_keys = array_keys($a_values);
		$i_number = count($a_keys);
		for ($i = 0; $i < $i_number; $i++) {
			if (substr($a_keys[$i], 0, 1) != '{' && substr($a_keys[$i], -1) != '}')
			$a_keys[$i] = '{' . $a_keys[$i] . '}';
		}

		return str_replace($a_keys, $a_values, $s_template);
	}

	/**
	 * Writes the values to the given keys on the given template
	 *
	 * @param	array	$a_keys		The keys
	 * @param	string	$s_template	The template to parse
	 * @return	string	The parsed template
	 */
	private function writeTemplateText($a_keys, $s_step,$s_template){
		$i_number = count($a_keys);
		$a_values   = array();
		for ($i = 0; $i < $i_number; $i++) {
			$a_values[$i] = $this->service_Language->get($s_step.'/'.$a_keys[$i]);
		}

		for ($i = 0; $i < $i_number; $i++) {
			if (substr($a_keys[$i], 0, 1) != '{' && substr($a_keys[$i], -1) != '}')
			$a_keys[$i] = '{' . $a_keys[$i] . '}';
		}

		return str_replace($a_keys, $a_values, $s_template);
	}
	
	private function reportError($s_error){
		if( !class_exists('Service_Logs') ){
			require(NIV.'include/services/Logs.inc.php');
		}
		$service_Logs	= new Service_Logs();
		
		if( is_object($s_error) ){
			$s_error	= $s_error->getMessage().'
			'.		$s_error->getTraceAsString();
		}
		
		$service_Logs->errorLog($s_error);
	}
	
	private function createSalt(){
		require_once(NIV.'include/services/Random.inc.php');
		require_once(NIV.'include/services/Hashing.inc.php');
		
		
		$service_Random = new \core\services\Random();
		
		$s_salt = \core\services\Hashing::createSalt($service_Random);
		return $s_salt;
	}
	
	private function getBase(){
		if( !class_exists('\core\Memory') ){
			include(NIV.'include/Memory.php');
		}
		return \core\Memory::detectBase();
	}
}

$obj_Install    = new Install();
unset($obj_Install);
?>
