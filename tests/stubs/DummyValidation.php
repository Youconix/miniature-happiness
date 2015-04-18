<?php
if (! class_exists('\core\services\Validation')) {
    require (NIV . 'core/services/Validation.inc.php');
}

class DummyValidation extends \core\services\Validation
{
    protected $a_errors;
    
    /**
     * Validates the given email address
     *
     * @param string $s_email
     *            The email address
     * @return boolean True if the email address is valid, otherwise false
     */
    public function checkEmail($s_email)
    {
        return true;
    }
    
    /**
     * Validates the given URI
     *
     * @param string $s_uri
     *            The URI
     * @return boolean True if the URI is valid, otherwise false
     */
    public function checkURI($s_uri)
    {
        return true;
    }
    
    /**
     * Validates the given dutch postal address
     *
     * @param string $s_value
     *            The postal address
     * @return boolean True if the postal address is valid, otherwise false
     */
    public function checkPostalNL($s_value)
    {
        return true;
    }
    
    /**
     * Validates the given belgium postal address
     *
     * @param string $s_value
     *            The postal address
     * @return boolean True if the postal address is valid, otherwise false
     */
    public function checkPostalBE($i_value)
    {
        return true;
    }
    
    /**
     * Validates the IP address
     *
     * @param string $s_value
     *            The IPv4 or IPv6 address
     * @return boolean True if the address is valid
     */
    public function validateIP($s_value)
    {
        return true;
    }
    
    /**
     * Performs the validation
     *
     * @return boolean True if the fields are valid
     */
    public function validate($a_validation, $a_collection)
    {
        return true;
    }
    
    /**
     * Returns the errors after validation
     *
     * @return array The errors
     */
    public function getErrors()
    {
        return array();
    }
}
?>