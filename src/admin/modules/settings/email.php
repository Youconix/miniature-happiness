<?php

namespace admin\modules\settings;

/**
 * Email configuration class
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Email extends \admin\modules\settings\Settings
{

  /**
   * Inits the class Settings
   */
  protected function init()
  {
    $this->init_post = array(
	'email_name' => 'string',
	'email_email' => 'string',
	'smtp_active' => 'boolean',
	'smtp_host' => 'string',
	'smtp_username' => 'string',
	'smtp_password' => 'string',
	'smtp_port' => 'int',
	'email_admin_name' => 'string',
	'email_admin_email' => 'string'
    );

    parent::init();
  }

  /**
   * @Route("/controlpanel/settings/email", name="admin_settings_email_index")
   * Displays the email settings
   * @return \Output
   */
  public function email()
  {
    $template = $this->createView('admin/modules/settings/email/showemail', [],
				  'admin');
    $smtpActive = clone $this->onOff;
    $smtpActive->setName('smtp_active');
    if ($this->getValue('mail/SMTP')) {
      $smtpActive->setSelected(true);
    }

    $template->set('emailTitle', t('system/settings/email/title'));
    $template->set('emailGeneralTitle', t('system/settings/email/generalTitle'));

    $template->set('nameText', t('system/settings/email/senderName'));
    $template->set('name', $this->getValue('mail/senderName'));
    $template->set('emailText', t('system/settings/email/senderEmail'));
    $template->set('email', $this->getValue('mail/senderEmail'));

    $template->set('SmtpTitle', t('system/settings/email/smtp'));
    $template->set('smtpActiveText', t('system/settings/email/useSmtp'));

    $template->set('smtpActive', $smtpActive);
    $template->set('smtpHostText', t('system/settings/host'));
    $template->set('smtpHost', $this->getValue('mail/host'));
    $template->set('smtpUsernameText', t('system/settings/username'));
    $template->set('smtpUsername', $this->getValue('mail/username'));
    $template->set('smtpPasswordText', t('system/settings/password'));
    $template->set('smtpPassword', $this->getValue('mail/password'));
    $template->set('smtpPortText', t('system/settings/port'));
    $template->set('smtpPort', $this->getValue('mail/port', 587));

    $template->set('emailAdminTitle',
		   t('system/settings/email/adminSenderTitle'));
    $template->set('nameAdminText', t('system/settings/email/adminSenderName'));
    $template->set('nameAdmin', $this->getValue('main/controlpanel/name'));
    $template->set('emailAdminText', t('system/settings/email/adminSenderEmail'));
    $template->set('emailAdmin', $this->getValue('main/controlpanel/email'));

    $template->set('nameError', t('system/settings/email/senderEmpty'));
    $template->set('emailError', t('system/settings/email/senderEmailEmpty'));
    $template->set('smtpHostError', t('system/settings/email/smtpHostError'));
    $template->set('smtpUsernameError',
		   t('system/settings/email/smtpUsernameError'));
    $template->set('smptPasswordError',
		   t('system/settings/email/smptPasswordError'));
    $template->set('smtpPortError', t('system/settings/email/smtpPortError'));
    $template->set('saveButton', t('system/buttons/save'));

    $this->setDefaultValues($template);

    return $template;
  }

  /**
   * Saves the email settings
   * @Route("/controlpanel/settings/email/save", name="admin_settings_email_save")
   */
  public function emailSave()
  {
    $post = $this->getRequest()->post();
    if (!$post->validate(array(
	    'email_name' => 'required',
	    'email_email' => 'required|pattern:email',
	    'email_admin_name' => 'required',
	    'email_admin_email' => 'required|pattern:email',
	    'smtp_active' => 'required|type:boolean'
	))) {
      return;
    }

    if ($post->get('smtp_active') == 1 && !$post->validate(array(
	    'smtp_host' => 'required|pattern:url',
	    'smtp_username' => 'required',
	    'smtp_password' => 'required',
	    'smtp_port' => 'required|type:port'
	))) {
      return;
    }

    $this->setValue('settings/mail/senderName', $post->get('email_name'));
    $this->setValue('settings/mail/senderEmail', $post->get('email_email'));

    $this->setValue('settings/mail/SMTP', $post->get('smtp_active'));
    $this->setValue('settings/mail/host', $post->get('smtpHost'));
    $this->setValue('settings/mail/username', $post->get('smtp_username'));
    $this->setValue('settings/mail/password', $post->get('smtp_password'));
    $this->setValue('settings/mail/port', $post->get('smtp_port'));

    $this->setValue('main/controlpanel/name', $post->get('email_admin_name'));
    $this->setValue('main/controlpanel/email', $post->get('email_admin_email'));

    $this->settings->save();
  }
}
