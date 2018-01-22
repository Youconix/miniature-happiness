<?php

namespace admin\modules\settings;

/**
 * Database configuration class
 *
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Database extends \admin\modules\settings\Settings
{

  /**
   *
   * @var \youconix\core\services\FileHandler
   */
  private $fileHandler;
  private $dalDirectory;

  /**
   * Constructor
   *
   * @param \youconix\core\templating\AdminControllerWrapper $wrapper
   * @param \youconix\core\helpers\OnOff $onOff
   * @param \Settings $settings            
   * @param \youconix\core\services\FileHandler $fileHandler
   */
  public function __construct(\youconix\core\templating\AdminControllerWrapper $wrapper,
			      \youconix\core\helpers\OnOff $onOff, \Settings $settings,
			      \youconix\core\services\FileHandler $fileHandler)
  {
    parent::__construct($wrapper, $onOff, $settings);

    $this->fileHandler = $fileHandler;
    $this->dalDirectory = NIV . 'vendor' . DS . 'youconix' . DS . 'core' . DS . 'ORM' . DS . 'database';
  }

  /**
   * Inits the class Settings
   */
  protected function init()
  {
    $this->init_post = [
	'prefix' => 'string',
	'type' => 'string',
	'username' => 'string',
	'password' => 'string',
	'database' => 'string',
	'host' => 'string',
	'port' => 'int'
    ];

    parent::init();
  }

  /**
   * Displays the database settings
   * @Route("/controlpanel/settings/database/index", name="admin_settings_database_index")
   * @return \Output
   */
  public function database()
  {
    $template = $this->createView('admin/modules/settings/database/database');

    $template->set('databaseTitle', t('system/settings/database/title'));
    $template->set('prefixText', t('system/settings/database/prefix'));
    $template->set('prefix', $this->getValue('SQL/prefix', 'MH_'));
    $template->set('typeText', t('system/settings/database/type'));

    $type = $this->getValue('SQL/type');
    $databases = $this->getDatabases($type);
    $template->set('databases', $databases);

    $template->set('usernameText', t('system/settings/username'));
    $template->set('username', $this->getValue('SQL/' . $type . '/username'));
    $template->set('passwordText', t('system/settings/password'));
    $template->set('password', $this->getValue('SQL/' . $type . '/password'));
    $template->set('databaseText', t('system/settings/database/database'));
    $template->set('database', $this->getValue('SQL/' . $type . '/database'));
    $template->set('hostText', t('system/settings/host'));
    $template->set('host', $this->getValue('SQL/' . $type . '/host'));
    $template->set('portText', t('system/settings/port'));
    $template->set('port', $this->getValue('SQL/' . $type . '/port', 3306));

    $template->set('saveButton', t('system/buttons/save'));

    $template->set('prefixError', $this->getText('database', 'prefixError'));
    $template->set('usernameError', $this->getText('database', 'usernameError'));
    $template->set('passwordError', $this->getText('database', 'passwordError'));
    $template->set('databaseError', $this->getText('database', 'databaseError'));
    $template->set('hostError', $this->getText('database', 'hostError'));

    $this->setDefaultValues($template);

    return $template;
  }

  /**
   * 
   * @param string $type
   * @return array
   */
  private function getDatabases($type)
  {
    $directory = $this->fileHandler->readDirectory($this->dalDirectory);
    $directory = $this->fileHandler->directoryFilterName($directory,
							 [
	'*.php',
	'!_binded',
	'!GeneralDAL.php',
	'!Builder_',
	'!Parser_'
    ]);

    foreach ($directory as $file) {
      $name = str_replace('.php', '', $file->getFilename());
      $selected = (($name == $type) ? 'selected="selected"' : '');
      $databases[] = [
	  'value' => $name,
	  'selected' => $selected,
	  'text' => $name
      ];
    }

    return $databases;
  }

  /**
   * Checks the database settings
   * @Route("/controlpanel/settings/database/check", name="admin_settings_database_check")
   */
  public function databaseCheck()
  {
    $post = $this->getRequest()->post();
    if (!$this->validateRequest()) {
      echo ('0');
      die();
    }

    $username = $post->get('username');
    $password = $post->get('password');
    $database = $post->get('database');
    $host = $post->get('host');
    $port = $post->get('port');
    $type = $post->get('type');

    switch ($type) {
      case 'Mysqli':
	require_once ($this->dalDirectory . '/Mysqli.php');
	$class = '\youconix\core\ORM\database\Mysqli';
	break;

      case 'PostgreSql':
	require_once ($this->dalDirectory . '/PostgreSql.php');
	$class = '\youconix\core\ORM\database\PostgreSql';
	break;
    }

    $oke = $class::checkLogin(
	    $username, $password, $database, $host, $port
    );

    if ($oke) {
      echo ('1');
    } else {
      echo ('0');
    }
    die();
  }

  /**
   * @Route("/controlpanel/settings/database/save", name="admin_settings_database_save")
   * Saves the database settings
   */
  public function databaseSave()
  {
    $post = $this->getRequest()->post();
    if (!$this->validateRequest()) {
      return;
    }

    $type = $post->get('type');
    $this->setValue('SQL/prefix', $post->get('prefix'));
    $this->setValue('SQL/type', $type);
    $this->setValue('SQL/' . $type . '/username', $post->get('username'));
    $this->setValue('SQL/' . $type . '/password', $post->get('password'));
    $this->setValue('SQL/' . $type . '/database', $post->get('database'));
    $this->setValue('SQL/' . $type . '/host', $post->get('host'));
    $this->setValue('SQL/' . $type . '/port', $post->get('port'));

    $this->settings->save();
  }

  /**
   * 
   * @return boolean
   */
  private function validateRequest()
  {
    $post = $this->getRequest()->post();
    if (!$post->validate([
	    'type' => 'required',
	    'username' => 'required',
	    'password' => 'required',
	    'database' => 'required',
	    'host' => 'required',
	    'port' => 'required|type:port'
	])) {
      return false;
    }

    return true;
  }
}
