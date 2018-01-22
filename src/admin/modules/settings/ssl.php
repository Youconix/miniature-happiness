<?php

namespace admin\modules\settings;

/**
 * General settings configuration class
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Ssl extends \admin\modules\settings\Settings
{

  /**
   *
   * @var \youconix\core\services\FileHandler
   */
  private $fileHandler;

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
   * @param \youconix\core\services\FileHandler $fileHandler            
   * @param \Config $config
   */
  public function __construct(\youconix\core\templating\AdminControllerWrapper $wrapper,
			      \youconix\core\helpers\OnOff $onOff, \Settings $settings,
			      \youconix\core\services\FileHandler $fileHandler, \Config $config)
  {
    parent::__construct($wrapper, $onOff, $settings);

    $this->fileHandler = $fileHandler;
    $this->config = $config;
  }

  /**
   * Inits the class Settings
   */
  protected function init()
  {
    $this->init_post = array(
	'ssl' => 'int'
    );

    parent::init();
  }

  /**
   * Displays the SSL setting
   * @Route("/controlpanel/settings/ssl", name="admin_settings_ssl_index")
   */
  public function ssl()
  {
    $template = $this->createView('admin/modules/settings/ssl/view');

    $bo_ssl = $this->checkSSL();
    $i_currentSSL = $this->getValue('main/ssl', \Settings::SSL_DISABLED);

    $s_noSllValue = '';
    $s_loginSslValue = '';
    $s_alwaysSslValue = '';

    if (!$bo_ssl) {
      $s_loginSslValue = 'disabled="disabled" ';
      $s_alwaysSslValue = 'disabled="disabled" ';
    }

    if ($i_currentSSL == \Settings::SSL_DISABLED) {
      $s_noSllValue .= 'checked="checked"';
    } else
    if ($i_currentSSL == \Settings::SSL_LOGIN) {
      $s_loginSslValue .= 'checked="checked"';
    } else {
      $s_alwaysSslValue .= 'checked="checked"';
    }

    $template->set('no_ssl_value', $s_noSllValue);
    $template->set('login_ssl_value', $s_loginSslValue);
    $template->set('always_ssl_value', $s_alwaysSslValue);
    $template->set('current_ssl', $i_currentSSL);

    $template->set('no_ssl', \Settings::SSL_DISABLED);
    $template->set('login_ssl', \Settings::SSL_LOGIN);
    $template->set('always_ssl', \Settings::SSL_ALL);

    $template->set('sslTitle', t('system/settings/ssl/title'));
    $template->set('noSslText', t('system/settings/ssl/no_ssl'));
    $template->set('loginSslText', t('system/settings/ssl/login_ssl'));
    $template->set('alwaysSslText', t('system/settings/ssl/always_ssl'));
    $template->set('saveButton', t('system/buttons/save'));

    $this->setDefaultValues($template);

    return $template;
  }

  /**
   * Saves the SSL setting
   * @Route("/controlpanel/settings/ssl/save", name="admin_settings_ssl_save")
   */
  public function sslSave()
  {
    $post = $this->getRequest()->post();
    if (!$post->has('ssl') || !in_array($post->get('ssl'),
						   [
	    \Settings::SSL_DISABLED,
	    \Settings::SSL_LOGIN,
	    \Settings::SSL_ALL
	])) {
      $this->badRequest();
      return;
    }

    $bo_ssl = $this->checkSSL();

    if (!$bo_ssl && $post->get('ssl') != \Settings::SSL_DISABLED) {
      return;
    }

    $this->setValue('main/ssl', $post->get('ssl'));
    $this->settings->save();
  }

  /**
   * 
   * @return boolean
   */
  private function checkSSL()
  {
    if (@file_get_contents('https://' . $_SERVER['HTTP_HOST']) !== false) {
      return true;
    }
    return false;
  }
}
