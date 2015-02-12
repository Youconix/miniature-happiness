<?php
if( !class_exists('\core\services\Mailer') ){
  require(NIV.'include/services/Mailer.inc.php');
}

class DummyMailer extends \core\services\Mailer {
  public function __construct(){  }
  
  /**
   * Returns the PHPMailer
   *
   * @param Boolean	$bo_html	Set to true for html mail, default true
   * @return PHPMailer	The mailer
   */
  public function getMailer($bo_html = true){
    return null;
  }

  /**
   * Sends the registration activation email
   *
   * @param String $s_username			The username
   * @param String $s_password			The plain text password
   * @param String $s_email				The email address
   * @param String $s_registrationKey		The activation code
   * @return Boolean	True if the email is send
   */
  public function registrationMail($s_username, $s_password, $s_email, $s_registrationKey){
    return true;
  }

  /**
   * Sends the registration confirm email triggerd by a admin
   *
   * @param String $s_username			The username
   * @param String $s_password			The plain text password
   * @param String $s_email				The email address
   * @return Boolean	True if the email is send
   */
  public function adminAdd($s_username, $s_password, $s_email){
    return true;
  }

  /**
   * Sends the password reset email
   *
   * @param String $s_username			The username
   * @param String $s_email				The email address
   * @param String $s_newPassword			The new plain text password
   * @param String $s_hash				The reset confirm code
   * @return Boolean	True if the email is send
   */
  public function passwordResetMail($s_username, $s_email, $s_newPassword, $s_hash){
    return true;
  }

  /**
   * Sends the password reset email triggerd by a admin
   *
   * @param String $s_username			The username
   * @param String $s_email				The email address
   * @param String $s_newPassword			The new plain text password
   * @return Boolean	True if the email is send
   */
  public function adminPasswordReset($s_username, $s_email, $s_newPassword){
    return true;
  }

  /**
   * Sends the account disable notification email
   *
   * @param String $s_username			The username
   * @param String $s_email				The email address
   * @return Boolean	True if the email is send
   */
  public function accountDisableMail($s_username, $s_email){
    return true;
  }
  
  /**
   * Sends the personal message notification email
   * 
   * @param \core\models\data\DataUser $obj_receiver   The receiver
   * @return Boolean	True if the email is send
   */
  public function PM(\core\models\data\DataUser $obj_receiver){
    return true;
  }
}
?>