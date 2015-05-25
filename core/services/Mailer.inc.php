<?php
namespace core\services;

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
 * Mailer service
 * Wraps the class PHPMailer (GPL)
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Mailer extends Service
{

    private $obj_phpMailer;

    private $service_Language;

    private $s_language;

    private $service_File;

    private $s_domain;

    private $s_domainUrl;

    /**
     * Inits the class Mailer
     *
     * @param core\services\Language $service_Language
     *            The language service
     * @param core\services\File $service_File
     *            The file service
     * @param core\models\Config $model_Config
     *            The Config model.
     */
    public function __construct(\core\services\Language $service_Language, \core\services\File $service_File, \core\models\Config $model_Config)
    {
        require_once (NIV . 'core/mailer/MailWrapper.inc.php');
        $this->obj_phpMailer = new \MailWrapper();
        
        $this->service_Language = $service_Language;
        $this->s_language = $this->service_Language->getLanguage();
        $this->service_File = $service_File;
        
        $this->s_domain = $_SERVER['HTTP_HOST'];
        $this->s_domainUrl = $model_Config->getProtocol() . $this->s_domain . $model_Config->getBase();
    }

    /**
     * Returns the PHPMailer
     *
     * @param Boolean $bo_html
     *            true for html mail, default true
     * @return PHPMailer mailer
     */
    public function getMailer($bo_html = true)
    {
        $this->obj_phpMailer->clearAll();
        
        $this->obj_phpMailer->useHTML($bo_html);
        
        return $this->obj_phpMailer;
    }

    /**
     * Sends the registration activation email
     *
     * @param String $s_username
     *            username
     * @param String $s_email
     *            email address
     * @param String $s_registrationKey
     *            activation code
     * @return Boolean if the email is send
     */
    public function registrationMail($s_username, $s_email, $s_registrationKey)
    {
        $a_mail = $this->getMail('registration');
        $s_body = $this->service_Language->insert($a_mail['body'], array(
            'username',
            'code'
        ), array(
            $s_username,
            $s_registrationKey
        ));
        $s_bodyAlt = $this->service_Language->insert($a_mail['bodyAlt'], array(
            'username',
            'code'
        ), array(
            $s_username,
            $s_registrationKey
        ));
        
        $obj_mailer = $this->getMailer();
        $obj_mailer->addAddress($s_email, $s_username);
        $obj_mailer->setSubject($a_mail['subject']);
        $obj_mailer->setBody($s_body);
        $obj_mailer->setAltBody($s_bodyAlt);
        
        return $this->sendMail($obj_mailer);
    }

    /**
     * Sends the registration confirm email triggerd by a admin
     *
     * @param String $s_username
     *            username
     * @param String $s_password
     *            plain text password
     * @param String $s_email
     *            email address
     * @return Boolean if the email is send
     */
    public function adminAdd($s_username, $s_password, $s_email)
    {
        $a_mail = $this->getMail('registrationAdmin');
        $s_body = $this->service_Language->insert($a_mail['body'], array(
            'username',
            'password'
        ), array(
            $s_username,
            $s_password
        ));
        $s_bodyAlt = $this->service_Language->insert($a_mail['bodyAlt'], array(
            'username',
            'password'
        ), array(
            $s_username,
            $s_password
        ));
        
        $obj_mailer = $this->getMailer();
        $obj_mailer->addAddress($s_email, $s_username);
        $obj_mailer->setSubject($a_mail['subject']);
        $obj_mailer->setBody($s_body);
        $obj_mailer->setAltBody($s_bodyAlt);
        
        return $this->sendMail($obj_mailer);
    }

    /**
     * Sends the password reset email
     *
     * @param String $s_username
     *            username
     * @param String $s_email
     *            email address
     * @param String $s_newPassword
     *            new plain text password
     * @param String $s_hash
     *            reset confirm code
     * @return Boolean if the email is send
     */
    public function passwordResetMail($s_username, $s_email, $s_newPassword, $s_hash)
    {
        $a_mail = $this->getMail('passwordReset');
        $s_body = $this->service_Language->insert($a_mail['body'], array(
            'username',
            'password',
            'code'
        ), array(
            $s_username,
            $s_newPassword,
            $s_hash
        ));
        $s_bodyAlt = $this->service_Language->insert($a_mail['bodyAlt'], array(
            'username',
            'password',
            'code'
        ), array(
            $s_username,
            $s_newPassword,
            $s_hash
        ));
        
        $obj_mailer = $this->getMailer();
        $obj_mailer->addAddress($s_email, $s_username);
        $obj_mailer->setSubject($a_mail['subject']);
        $obj_mailer->setBody($s_body);
        $obj_mailer->setAltBody($s_bodyAlt);
        
        return $this->sendMail($obj_mailer);
    }

    /**
     * Sends the password reset email triggerd by a admin
     *
     * @param String $s_username
     *            username
     * @param String $s_email
     *            email address
     * @param String $s_newPassword
     *            new plain text password
     * @return Boolean if the email is send
     */
    public function adminPasswordReset($s_username, $s_email, $s_newPassword)
    {
        $a_mail = $this->getMail('passwordResetAdmin');
        $s_body = $this->service_Language->insert($a_mail['body'], array(
            'username',
            'password'
        ), array(
            $s_username,
            $s_newPassword
        ));
        $s_bodyAlt = $this->service_Language->insert($a_mail['bodyAlt'], array(
            'username',
            'password'
        ), array(
            $s_username,
            $s_newPassword
        ));
        
        $obj_mailer = $this->getMailer();
        $obj_mailer->addAddress($s_email, $s_username);
        $obj_mailer->setSubject($a_mail['subject']);
        $obj_mailer->setBody($s_body);
        $obj_mailer->setAltBody($s_bodyAlt);
        
        return $this->sendMail($obj_mailer);
    }

    /**
     * Sends the account disable notification email
     *
     * @param String $s_username
     *            username
     * @param String $s_email
     *            email address
     * @return Boolean if the email is send
     */
    public function accountDisableMail($s_username, $s_email)
    {
        $a_mail = $this->getMail('accountDisabled');
        $s_body = $this->service_Language->insert($a_mail['body'], array(
            'username'
        ), array(
            $s_username
        ));
        $s_bodyAlt = $this->service_Language->insert($a_mail['bodyAlt'], array(
            'username'
        ), array(
            $s_username
        ));
        
        $obj_mailer = $this->getMailer();
        $obj_mailer->addAddress($s_email, $s_username);
        $obj_mailer->setSubject($a_mail['subject']);
        $obj_mailer->setBody($s_body);
        $obj_mailer->setAltBody($s_bodyAlt);
        
        return $this->sendMail($obj_mailer);
    }

    /**
     * Sends the personal message notification email
     *
     * @param \core\models\data\DataUser $obj_receiver
     *            The receiver
     * @return Boolean True if the email is send
     */
    public function PM(\core\models\data\DataUser $obj_receiver)
    {
        $s_email = $obj_receiver->getEmail();
        $s_username = $obj_receiver->getUsername();
        
        $a_mail = $this->getMail('PM');
        $s_body = $this->service_Language->insert($a_mail['body'], array(
            'username'
        ), array(
            $s_username
        ));
        $s_bodyAlt = $this->service_Language->insert($a_mail['bodyAlt'], array(
            'username'
        ), array(
            $s_username
        ));
        
        $obj_mailer = $this->getMailer();
        $obj_mailer->addAddress($s_email, $s_username);
        $obj_mailer->setSubject($a_mail['subject']);
        $obj_mailer->setBody($s_body);
        $obj_mailer->setAltBody($s_bodyAlt);
        
        return $this->sendMail($obj_mailer);
    }

    /**
     * Sends the log alert email to the administrator
     *
     * @param string $s_message
     *            The log message
     * @param array $a_address
     *            The name and emailaddress
     * @param string $s_domain
     *            The domain
     * @return Boolean True if the email is send
     */
    public function logDeamon($s_message, $a_address, $s_domain)
    {
        $s_email = $a_address['email'];
        $s_name = $a_address['name'];
        
        $a_mail = $this->getMail('log');
        $s_body = $this->service_Language->insert($a_mail['body'], array(
            'name',
            'message',
            'domain'
        ), array(
            $s_name,
            nl2br($s_message),
            $s_domain
        ));
        $s_bodyAlt = $this->service_Language->insert($a_mail['bodyAlt'], array(
            'name',
            'message',
            'domain'
        ), array(
            $s_name,
            $s_message,
            $s_domain
        ));
        
        $obj_mailer = $this->getMailer();
        $obj_mailer->addAddress($s_email, $s_name);
        $obj_mailer->setSubject($a_mail['subject']);
        $obj_mailer->setBody($s_body);
        $obj_mailer->setAltBody($s_bodyAlt);
        
        return $this->sendMail($obj_mailer);
    }

    /**
     * Collects the email template
     *
     * @param String $s_code
     *            template code
     * @param String $s_language
     *            The language, optional
     * @throws Exception the template does not exist
     * @return array templates
     */
    private function getMail($s_code, $s_language = '')
    {
        if (empty($s_language))
            $s_language = $this->s_language;
        
        if (! $this->service_File->exists(WEBSITE_ROOT . 'emails/' . $s_language . '/' . $s_code . '.tpl')) {
            throw new \Exception("Can not find the email template " . $s_code . " for language " . $this->s_language);
        }
        
        $a_file = explode('<==========>', $this->service_File->readFile(WEBSITE_ROOT . 'emails/' . $s_language . '/' . $s_code . '.tpl'));
        $a_filePlain = explode('<==========>', $this->service_File->readFile(WEBSITE_ROOT . 'emails/' . $s_language . '/' . $s_code . '_plain.tpl'));
        
        $s_htmlBody = $this->service_File->readFile(WEBSITE_ROOT . 'emails/main.tpl');
        $s_plainBody = $this->service_File->readFile(WEBSITE_ROOT . 'emails/main_plain.tpl');
        $a_filePlain[1] = str_replace('[content]', $a_filePlain[1], $s_plainBody);
        $a_file[1] = str_replace('[content]', $a_file[1], $s_htmlBody);
        
        $a_file[0] = $this->setMainMail($a_file[0]);
        $a_file[1] = $this->setMainMail($a_file[1]);
        $a_filePlain[1] = $this->setMainMail($a_filePlain[1]);
        
        return array(
            'subject' => trim($a_file[0]),
            'body' => $a_file[1],
            'bodyAlt' => $a_filePlain[1]
        );
    }

    /**
     * Sets the main email data
     *
     * @param String $s_mail
     *            email
     * @return String processed email
     */
    protected function setMainMail($s_mail)
    {
        $s_domain = $this->s_domain;
        $s_domainUrl = $this->s_domainUrl;
        
        $s_mail = $this->service_Language->insert($s_mail, array(
            'domain',
            'domainUrl'
        ), array(
            $s_domain,
            $s_domainUrl
        ));
        
        return trim($s_mail);
    }

    /**
     * Sends the email if testing mode is not active
     *
     * @param PHPMailer $obj_mailer
     *            mailer
     */
    private function sendMail($obj_mailer)
    {
        if (\core\Memory::isTesting())
            return true;
        
        return $obj_mailer->send();
    }
}