<?php

namespace admin\modules\settings;

/**
 * Login configuration class
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Login extends \admin\modules\settings\Settings
{

  /**
   *
   * @var \youconix\core\services\FileHandler
   */
  private $file;

  /**
   *
   * @var \Guards[]
   */
  private $guards = [];

  /**
   * Constructor
   * 
   * @param \youconix\core\templating\AdminControllerWrapper $wrapper
   * @param \youconix\core\helpers\OnOff $onOff
   * @param \Settings $settings
   * @param \youconix\core\services\FileHandler $file
   */
  public function __construct(\youconix\core\templating\AdminControllerWrapper $wrapper,
			      \youconix\core\helpers\OnOff $onOff, \Settings $settings,
			      \youconix\core\services\FileHandler $file)
  {
    parent::__construct($wrapper, $onOff, $settings);

    $this->file = $file;
  }

  /**
   * Inits the class Settings
   */
  protected function init()
  {
    $this->bo_acceptAllInput = true;

    parent::init();
  }

  /**
   * Displays the login settings
   * @Route("/controlpanel/settings/login", name="admin_settings_login_index")
   */
  public function login()
  {
    $this->detectGuards();

    $template = $this->createView('admin/modules/settings/login/view');

    $loginRedirect = $this->getValue('auth/login', 'index/view');
    $logoutRedirect = $this->getValue('auth/logout', 'index/view');
    $registrationRedirect = $this->getValue('auth/registration', 'index/view');
    $defaultGuard = $this->getValue('auth/defaultGuard', 'normal');
    $registrationEnabled = $this->getValue('auth/usersRegister', 1);

    $template->set('generalTitle', $this->getText('login', 'title'));
    $template->set('loginRedirectText', $this->getText('login', 'loginRedirect'));
    $template->set('loginRedirect', $loginRedirect);
    $template->set('logoutRedirectText',
		   $this->getText('login', 'logoutRedirect'));
    $template->set('logoutRedirect', $logoutRedirect);
    $template->set('defaultGuardText', $this->getText('login', 'defaultGuard'));
    $template->set('defaultGuard', $defaultGuard);
    $template->set('registrationRedirectText',
		   $this->getText('login', 'registrationRedirect'));
    $template->set('registrationRedirect', $registrationRedirect);
    $template->set('registrationEnabledText', $this->getText('login', 'registrationEnabled'));
    $template->set('registrationEnabled',
		   $this->createSlider('registration_enabled', $registrationEnabled)
    );

    $template->set('redirectError', $this->getText('login', 'redirectError'));
    $template->set('saveButton', t('system/buttons/save'));
    $template->set('loginChoiceText', $this->getText('login', 'loginChoice'));

    $guards = [];
    foreach ($this->guards as $guard) {
      $guards[] = [
	  $guard,
	  $this->createSlider($guard->getName() . '_enabled', $guard->isEnabled())
      ];
    }

    $enabledGuards = [];
    foreach ($this->guards as $guard) {
      $enabledGuards[$guard->getName()] = [
	  'config' => $guard->hasConfig(),
	  'name' => $guard->getDisplayName()
      ];
    }

    $template->set('guards', $guards);
    $template->set('enabledGuards', json_encode($enabledGuards));

    $this->setDefaultValues($template);

    return $template;
  }

  /**
   * Saves the login settings
   * @Route("/controlpanel/settings/login/save", name="admin_settings_login_save")
   */
  public function loginSave()
  {
    $post = $this->getRequest()->post();
    if (!$post->validate([
	    'login_redirect' => 'required',
	    'logout_redirect' => 'required',
	    'registration_redirect' => 'required',
	    'registration_enabled' => 'required|set:0,1',
	    'default_guard' => 'required|type:string'
	])) {
      $this->badRequest();
      return;
    }

    $this->detectGuards();

    $default = $post->get('default_guard');

    $guardsOK = true;
    $defaultFound = false;
    $guards = [];
    foreach ($this->guards as $guard) {
      $name = $guard->getName();
      if (!$post->has($name . '_enabled')) {
	continue;
      }

      if (!$guard->validate($post)) {
	$guardsOK = false;
	continue;
      }
      $guard->setConfig($post);

      if ($guard->getName() == $default) {
	$defaultFound = true;
      }

      $className = get_class($guard);
      $guards[$className] = $guard;
    }

    if (!$guardsOK || !$defaultFound) {
      $this->badRequest();
      return;
    }

    $this->setValue('auth/login', $post->get('login_redirect'));
    $this->setValue('auth/logout', $post->get('logout_redirect'));
    $this->setValue('auth/registration', $post->get('registration_redirect'));
    $this->getValue('auth/usersRegister', $default);
    $this->setValue('auth/usersRegister', $post->get('registration_enabled'));

    $this->settings->emptyPath('auth/guards');
    foreach ($guards as $path => $guard) {
      $this->settings->add('auth/guards/' . $guard->getName(), $path);
    }

    $this->settings->save();
  }

  private function detectGuards()
  {
    $siteGuards = NIV . DS . 'includes' . DS . 'auth' . DS . 'guards';
    if ($this->file->exists($siteGuards)) {
      $guards = $this->file->readFilteredDirectoryNames($siteGuards, [], 'php');
      foreach ($guards as $path) {
	$this->addGuard($path);
      }
    }

    $coreGuards = NIV . DS . 'vendor' . DS . 'youconix' . DS . 'core' . DS . 'auth' . DS . 'guards';
    $guards = $this->file->readFilteredDirectoryNames($coreGuards, [], 'php');
    foreach ($guards as $path) {
      $this->addGuard($path);
    }
  }

  /**
   * 
   * @param string $path
   */
  private function addGuard($path)
  {
    $path = str_replace('./', '',
			str_replace('../', '',
	       str_replace('\\', '', str_replace('.php', '', $path)
    )));

    $name = explode(DS, $path);
    $name = end($name);

    if (array_key_exists($name, $this->guards)) {
      return;
    }

    $call = str_replace(DS, '\\',
			str_replace(DS . 'vendor' . DS, '', $path
    ));

    $guard = \Loader::inject($call);
    if (!is_null($guard) && ($guard instanceof \Guard)) {
      $this->guards[$name] = $guard;
    }
  }
}
