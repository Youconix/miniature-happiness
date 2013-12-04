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

require(NIV.'admin/SettingsMain.php');

class Install extends SettingsMain{
	private $service_Language;
	private $obj_settingsMain;
	private $i_step = 1;
	private $s_layout;
	private $bo_error   = false;

	/**
	 * PHP 5 constructor
	 */
	public function __construct(){
		$this->init();

		if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
			switch($this->i_step){
				case 3 :
					$this->settingsCheck();
					break;

				case 5 :
					$this->standardUserSave();
					break;
			}
		}
		else {
			switch($this->i_step){
				case 1 :
					$this->systemCheck();
					break;
					 
				case 2 :
					$this->frameworkCheck();
					break;

				case 3 :
					$this->settingsScreen();
					break;

				case 4 :
					$this->populateDB();
					break;

				case 5 :
					$this->standardUserScreen();
					break;

				case 6 :
					$this->complete();
					break;
			}
		}

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

		$this->s_layout = $this->readTemplate('layout');
		require(NIV.'include/language/Install.php');
		$this->service_Language = new Install_Language();

		require_once(NIV.'admin/SettingsMain.php');
		$this->obj_settingsMain = new settingsMain();
		
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
	
	/**
	 * Checks the progress
	 */
	private function progressCheck(){
		$i_step = -1;
		
		if( file_exists(DATA_DIR.'settings/settings.xml') ){
			$i_step=4;
			
			try {
				$service_Database	= Memory::services('Database');
				define('DB_PREFIX',Memory::services('XmlSettings')->get('settings/SQL/prefix'));
				
				$service_Database->query("SHOW TABLES LIKE ".DB_PREFIX."users");
				if( $service_Database->num_rows() > 0 ){
					$i_step = 5;
					
					$service_Database->query("SELECT * FROM ".DB_PREFIX."users WHERE id = 1");
					if( $service_Database->num_rows() > 0 ){
						$i_step = 6;
					}
				}
			}
			catch(Exception $e){	
				/* Install failed */
				unlink(DATA_DIR.'/settings/settings.xml');
				;
				$i_step = 3;
			}
		}
		
		if( $i_step != -1 ){
			header('location: index.php?step='.$i_step);
			exit();
		}
	}

	/**
	 * Performs the system check
	 */
	private function systemCheck(){
		require(NIV.'install/systemCheck.php');
		$obj_check	= new SystemCheck();
		
		$s_output	= $obj_check->validate();
		if( $obj_check->isValid()){
			$s_output .= '<p><a href="index.php?step=2" class="button">2 &gt;&gt;</a></p>';
		}
					
		$this->s_layout = str_replace('{content}',$s_output,$this->s_layout);
	}

	/**
	 * Performs the framework check
	 */
	private function frameworkCheck(){
		require(NIV.'install/fileCheck.php');
		$obj_check	= new FileCheck();
		
		$s_output	= $obj_check->validate();
		if( $obj_check->isValid()){
			$s_output = '<p><a href="index.php?step=3" class="button">3 &gt;&gt;</a></p>'.
			$s_output.
			'<p><a href="index.php?step=3" class="button">3 &gt;&gt;</a></p>';
		}
					
		$this->s_layout = str_replace('{content}','<h1>'.$this->service_Language->get('step2/title').'</h1>'.$s_output,$this->s_layout);
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
                'url'       => $_SERVER['HTTP_HOST'], 'timezone'  => date_default_timezone_get(),
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
		$a_data['generalError'] = '';
		$a_data['sqlError']     = '';
		

		/* Check fields */
		$a_check    = array('url','timezone','language','template');
		foreach($a_check AS $s_check){
			if( trim($a_data[$s_check]) == '' ){
				$a_data['generalError'] = $this->service_Language->get('step3/fieldsEmpty').'<br/>';
				$bo_error   = true;
				break;
			}
		}

		if( !empty($a_data['sessionExpire']) && !is_numeric($a_data['sessionExpire']) ){
			$a_data['generalError'] .= $this->service_Language->get('step3/sessionExpireInvalid').'<br/>';
			$bo_error   = true;
		}

		if( !in_array($a_data['language'],$this->getLanguages()) ){
			$a_data['generalError'] .= $this->service_Language->get('step3/languageInvalid').'<br/>';
			$bo_error   = true;
		}


		if( !in_array($a_data['template'],$this->getTemplates()) ){
			$a_data['generalError'] .= $this->service_Language->get('step3/templateInvalid').'<br/>';
			$bo_error   = true;
		}
 
		/* Check database data */
		if( !in_array($a_data['databaseType'],$this->getDatabases() ) ){
			$a_data['sqlError'] .= $this->service_Language->get('step3/databaseTypeInvalid').'<br/>';
			$bo_error   = true;
		}
		else if( !$this->checkDatabase($a_data) ){
			$a_data['sqlError']     = $this->service_Language->get('step3/databaseInvalid').'<br/>';
			$bo_error   = true;
		}

		if( $bo_error ){
			$this->settingsScreen($a_data);
		}
		else {
			unset($a_data['generalError']);
			unset($a_data['sqlError']);
		
			/* Generate settings file */
			require(NIV.'include/services/Random.inc.php');
					
			$service_Random	= new Service_Random();
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
			$main->appendChild($settings->createElement('salt',$service_Random->numberLetter(15,true)));
			$root->appendChild($main);
			
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
			$mail->appendChild($settings->createElement('senderName'));
			$mail->appendChild($settings->createElement('username'));
			$mail->appendChild($settings->createElement('password'));
			$mail->appendChild($settings->createElement('port'));
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
			
			$version	= $settings->createElement('version','1.0');
			$root->appendChild($version);
			
			$settings->appendChild($root);				

			$s_dir	= DATA_DIR.'/settings/';

			if( !is_writable($s_dir) ){
				throw new Exception($this->service_Language->get('step3/permissionFailure').' '.$s_dir.'.');
			}
		
			$settings->save($s_dir.'settings.xml');						
			header('location: index.php?step=4');
			die();
		}
	}
	
	/**
	 * Populates the database
	 */
	private function populateDB(){
		require(NIV.'install/Database.php');
		$obj_Database	= new Database();
		
		if( !$obj_Database->populateDatabase()){
			$this->s_layout = str_replace('{content}','<h2>'.$this->service_Language->get('step4/error').'</h2>',$this->s_layout);
		}
		else {
			header('location: index.php?step=5');
			exit();
		}
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
		$a_data['generalError'] = '';

		/* Check fields */
		$a_check    = array('nick','email','password','password2');
		foreach($a_check AS $s_check){
			if( trim($a_data[$s_check]) == '' ){
				$a_data['generalError'] = $this->service_Language->get('step5/fieldsEmpty').'<br/>';
				$bo_error   = true;
				break;
			}
		}

		if( $a_data['password'] != $a_data['password2'] ){
			$a_data['ftpError']     = $this->service_Language->get('step5/passwordInvalid').'<br/>';
			$bo_error   = true;
		}

		if( $bo_error ){
			$this->standardUserScreen($a_data);
		}
		else {
			require(NIV.'install/Database.php');
			$obj_Database	= new Database();
			
			if( !$obj_Database->createUser($a_data['nick'],$a_data['email'],$a_data['password'])){
				$this->s_layout = str_replace('{content}','<h2 class="errorNotice">'.$this->service_Language->get('step5/error').'</h2>',$this->s_layout);
			}
			else {
				header('location: index.php?step=6');
				exit();
			}
		}
	}
	
	/**
	 * Completes the installation
	 */
	private function complete(){
		$s_content = '<h1 class="Notice">'.$this->service_Language->get('step6/complete').'</h1>
		
		<h2 class="errorNotice">'.$this->service_Language->get('step6/removeDir').'</h2>';
		
		require(NIV.'include/Memory.php');
		Memory::startUp();
		$service_CurlManager	= Memory::services('CurlManager');
		
		$s_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$s_url = $s_protocol.$_SERVER['HTTP_HOST'].Memory::getBase().'/admin/data/settings/settings.xml';
		
		$service_CurlManager->performGetCall($s_url,array());
		
		if( $service_CurlManager->getHeader() == 200 ){
			$s_content .= '<h2 class="errorNotice">'.str_replace('[base]',Memory::getBase(),$this->service_Language->get('step6/settingsVisible')).'</h2>
					<h2 class="errorNotice">'.$this->service_Language->get('step6/moveDir').'</h2>';
		}
		
		$this->s_layout = str_replace('{content}',$s_content,$this->s_layout);
	}

	/**
	 * Reads the given template
	 * 
	 * @param string $s_template		The template name
	 * @return string	The template content
	 */
	private function readTemplate($s_template){
		$file       = fopen(NIV.'styles/default/templates/install/'.$s_template.'.tpl','r');
		$s_content = fread($file,filesize(NIV.'styles/default/templates/install/'.$s_template.'.tpl'));
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
}

$obj_Install    = new Install();
unset($obj_Install);
?>
