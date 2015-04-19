<?php
namespace admin;

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
if (! defined('NIV')) {
    define('NIV', '../../../');
}

include (NIV . 'admin/modules/settings/settings.php');

class Email extends \admin\Settings
{
    /**
     * Calls the functions
     */
    protected function menu(){
        if (isset($this->get['command'])) {
            switch ($this->get['command']) {
                case 'email':
                    $this->email();
                    break;
            }
        } else
            if (isset($this->post['command'])) {
                switch ($this->post['command']) {
                    case 'email':
                        $this->emailSave();
                        break;
                }
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
        $this->service_Template->set('emailTitle', t('system/admin/settings/email/title'));
        
        $this->service_Template->set('emailGeneralTitle', t('system/admin/settings/email/generalTitle'));
        $this->service_Template->set('nameText', t('system/admin/settings/email/senderName'));
        $this->service_Template->set('name', $this->getValue('mail/senderName'));
        $this->service_Template->set('emailText', t('system/admin/settings/email/senderEmail'));
        $this->service_Template->set('email', $this->getValue('mail/senderEmail'));
        
        $this->service_Template->set('SmtpTitle', t('system/admin/settings/email/smtp'));
        $this->service_Template->set('smtpActiveText', t('system/admin/settings/email/useSmtp'));
        $smtpActive = $this->getValue('mail/SMTP');
        if ($smtpActive == 1) {
            $this->service_Template->set('smtpActive', 'checked="checked"');
        } else {
            $this->service_Template->set('showSMTP', 'style="display:none"');
        }
        $this->service_Template->set('smtpHostText', t('system/admin/settings/host'));
        $this->service_Template->set('smtpHost', $this->getValue('mail/host'));
        $this->service_Template->set('smtpUsernameText', t('system/admin/settings/username'));
        $this->service_Template->set('smtpUsername', $this->getValue('mail/username'));
        $this->service_Template->set('smtpPasswordText', t('system/admin/settings/password'));
        $this->service_Template->set('smtpPassword', $this->getValue('mail/password'));
        $this->service_Template->set('smtpPortText', t('system/admin/settings/port'));
        $this->service_Template->set('smtpPort', $this->getValue('mail/port', 587));
        
        $this->service_Template->set('emailAdminTitle', t('system/admin/settings/email/adminSenderTitle'));
        $this->service_Template->set('nameAdminText', t('system/admin/settings/email/adminSenderName'));
        $this->service_Template->set('nameAdmin', $this->getValue('main/admin/name'));
        $this->service_Template->set('emailAdminText', t('system/admin/settings/email/adminSenderEmail'));
        $this->service_Template->set('emailAdmin', $this->getValue('main/admin/email'));
        
        $this->service_Template->set('nameError', t('system/admin/settings/email/senderEmpty'));
        $this->service_Template->set('emailError', t('system/admin/settings/email/senderEmailEmpty'));
        $this->service_Template->set('smtpHostError', t('system/admin/settings/email/smtpHostError'));
        $this->service_Template->set('smtpUsernameError', t('system/admin/settings/email/smtpUsernameError'));
        $this->service_Template->set('smptPasswordError', t('system/admin/settings/email/smptPasswordError'));
        $this->service_Template->set('smtpPortError', t('system/admin/settings/email/smtpPortError'));
        $this->service_Template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Saves the email settings
     */
    private function emailSave()
    {
        if (! $this->service_Validation->validate(array(
            'email_name' => array(
                'required' => 1
            ),
            'email_email' => array(
                'required' => 1,
                'pattern' => 'email'
            ),
            'email_admin_name' => array(
                'required' => 1
            ),
            'email_admin_email' => array(
                'required' => 1,
                'pattern' => 'email'
            )
        ), $this->post)) {
            return;
        }
        
        if (isset($this->post['smtp_active']) && ! $this->service_Validation->validate(array(
            'smtp_host' => array(
                'required' => 1,
                'pattern' => 'url'
            ),
            'smtp_username' => array(
                'required' => 1
            ),
            'smtp_password' => array(
                'required' => 1
            ),
            'smtp_port' => array(
                'required' => 1,
                'type' => 'int',
                'min-value' => 1
            )
        ), $this->post)) {
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

$obj_Email = new Email();
unset($obj_Email);