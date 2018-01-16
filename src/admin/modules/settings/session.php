<?php

namespace admin\modules\settings;

/**
 * Login configuration class
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Session extends \admin\modules\settings\Settings
{

  /**
   *
   * @var \Config
   */
  private $config;

  /**
   * Constructor
   * 
   * @param \youconix\core\templating\AdminControllerWrapper $wrapper
   * @param \youconix\core\helpers\OnOff $onOff
   * @param \Settings $settings
   * @param \Config $config
   */
  public function __construct(\youconix\core\templating\AdminControllerWrapper $wrapper,
			      \youconix\core\helpers\OnOff $onOff, \Settings $settings,
			      \Config $config)
  {
    parent::__construct($wrapper, $onOff, $settings);

    $this->config = $config;
  }

  /**
   * Inits the class Settings
   */
  protected function init()
  {
    $this->init_post = [
	'session_name' => 'string',
	'session_path' => 'string',
	'session_expire' => 'int'
    ];

    parent::init();
  }

  /**
   * Displays the sessions
   * @Route("/controlpanel/settings/session", name="admin_settings_session_index")
   * @return \Output
   */
  public function sessions()
  {
    $template = $this->createView('admin/modules/settings/session/view');

    $template->set('generalTitle', $this->getText('sessions', 'title'));
    $template->set('sessionNameText', $this->getText('sessions', 'name'));
    $template->set('sessionName',
		   $this->getValue('session/sessionName', 'miniature-happiness'));
    $template->set('sessionPathText', $this->getText('sessions', 'path'));
    $template->set('sessionPath',
		   $this->getValue('sessions/sessionPath', 'data/sessions'));
    $template->set('sessionExpireText', $this->getText('sessions', 'expire'));
    $template->set('sessionExpire',
		   $this->getvalue('session/sessionExpire', 300));

    $template->set('sessionNameError', $this->getText('sessions', 'nameError'));
    $template->set('sessionPathError', $this->getText('sessions', 'pathError'));
    $template->set('sessionExpireError',
		   $this->getText('sessions', 'expireError'));

    $template->set('saveButton', t('system/buttons/save'));
    $this->setDefaultValues($template);

    return $template;
  }

  /**
   * Saves the sessions
   * @Route("/controlpanel/settings/session/save", name="admin_settings_session_save")
   */
  public function sessionsSave()
  {
    $post = $this->getRequest()->post();
    if (!$post->validate([
	    'session_name' => 'required',
	    'session_path' => 'required',
	    'session_expire' => 'required|type:int|min:60'
	])) {
      $this->badRequest();
      return;
    }

    $this->setValue('session/sessionName', $post->get('session_name'));
    $this->getValue('sessions/sessionPath', $post->get('session_path'));
    $this->setvalue('session/sessionExpire', $post->get('session_expire'));

    $this->settings->save();
  }
}
