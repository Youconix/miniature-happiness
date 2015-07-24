<?php
namespace tests\stubs\services;

class Mailer extends \core\services\Mailer
{

    public function __construct()
    {}

    /**
     * Returns the PHPMailer
     *
     * @param Boolean $bo_html
     *            true for html mail, default true
     * @return PHPMailer mailer
     */
    public function getMailer($bo_html = true)
    {
        return null;
    }

    /**
     * Sends the registration activation email
     *
     * @param String $s_username
     *            username
     * @param String $s_password
     *            plain text password
     * @param String $s_email
     *            email address
     * @param String $s_registrationKey
     *            activation code
     * @return Boolean if the email is send
     */
    public function registrationMail($s_username, $s_email, $s_registrationKey)
    {
        return true;
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
        return true;
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
        return true;
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
        return true;
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
        return true;
    }

    /**
     * Sends the personal message notification email
     *
     * @param \core\models\data\User $obj_receiver
     *            The receiver
     * @return Boolean if the email is send
     */
    public function PM(\core\models\data\User $obj_receiver)
    {
        return true;
    }
}
?>