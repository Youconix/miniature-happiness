<?php
namespace admin\modules\settings;

/**
 * Miniature-happiness is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Miniature-happiness is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 *
 * Admin settings configuration class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Email extends \admin\modules\settings\Settings
{

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->email();
        } else {
            $this->emailSave();
        }
    }

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
     * Displays the email settings
     */
    private function email()
    {
        $this->template->set('emailTitle', t('system/admin/settings/email/title'));
        
        $this->template->set('emailGeneralTitle', t('system/admin/settings/email/generalTitle'));
        $this->template->set('nameText', t('system/admin/settings/email/senderName'));
        $this->template->set('name', $this->getValue('mail/senderName'));
        $this->template->set('emailText', t('system/admin/settings/email/senderEmail'));
        $this->template->set('email', $this->getValue('mail/senderEmail'));
        
        $this->template->set('SmtpTitle', t('system/admin/settings/email/smtp'));
        $this->template->set('smtpActiveText', t('system/admin/settings/email/useSmtp'));
        $smtpActive = $this->getValue('mail/SMTP');
        if ($smtpActive == 1) {
            $this->template->set('smtpActive', 'checked="checked"');
        } else {
            $this->template->set('showSMTP', 'style="display:none"');
        }
        $this->template->set('smtpHostText', t('system/admin/settings/host'));
        $this->template->set('smtpHost', $this->getValue('mail/host'));
        $this->template->set('smtpUsernameText', t('system/admin/settings/username'));
        $this->template->set('smtpUsername', $this->getValue('mail/username'));
        $this->template->set('smtpPasswordText', t('system/admin/settings/password'));
        $this->template->set('smtpPassword', $this->getValue('mail/password'));
        $this->template->set('smtpPortText', t('system/admin/settings/port'));
        $this->template->set('smtpPort', $this->getValue('mail/port', 587));
        
        $this->template->set('emailAdminTitle', t('system/admin/settings/email/adminSenderTitle'));
        $this->template->set('nameAdminText', t('system/admin/settings/email/adminSenderName'));
        $this->template->set('nameAdmin', $this->getValue('main/admin/name'));
        $this->template->set('emailAdminText', t('system/admin/settings/email/adminSenderEmail'));
        $this->template->set('emailAdmin', $this->getValue('main/admin/email'));
        
        $this->template->set('nameError', t('system/admin/settings/email/senderEmpty'));
        $this->template->set('emailError', t('system/admin/settings/email/senderEmailEmpty'));
        $this->template->set('smtpHostError', t('system/admin/settings/email/smtpHostError'));
        $this->template->set('smtpUsernameError', t('system/admin/settings/email/smtpUsernameError'));
        $this->template->set('smptPasswordError', t('system/admin/settings/email/smptPasswordError'));
        $this->template->set('smtpPortError', t('system/admin/settings/email/smtpPortError'));
        $this->template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Saves the email settings
     */
    private function emailSave()
    {
        if (! $this->post->validate(array(
            'email_name' => 'required',
            'email_email' => 'required|pattern:email',
            'email_admin_name' => 'required',
            'email_admin_email' => 'required|pattern:email'
        ))) {
            return;
        }
        
        if (isset($this->post['smtp_active']) && ! $this->post->validate(array(
            'smtp_host' => 'required|pattern:url',
            'smtp_username' => 'required',
            'smtp_password' => 'required',
            'smtp_port' => 'required|type:port'
        ))) {
            return;
        }
        
        $this->setValue('settings/mail/senderName', $this->post['email_name']);
        $this->setValue('settings/mail/senderEmail', $this->post['email_email']);
        
        $this->setValue('settings/mail/SMTP', ((isset($this->post['smtp_active'])) ? 1 : 0));
        $this->setValue('settings/mail/host', $this->post['smtpHost']);
        $this->setValue('settings/mail/username', $this->post['smtp_username']);
        $this->setValue('settings/mail/password', $this->post['smtp_password']);
        $this->setValue('settings/mail/port', $this->post['smtp_port']);
        
        $this->setValue('main/admin/name', $this->post['email_admin_name']);
        $this->setValue('main/admin/email', $this->post['email_admin_email']);
        
        $this->service_Settings->save();
    }
}