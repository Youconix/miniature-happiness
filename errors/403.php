<?php
/**
 * Error 403 class
 *
 * @name error.php
 * @package error_docs
 *          @lisence:		http://scripthulp.com/licence.php
 * @author :		REJ Scheijen Scripthulp
 * @version 1.0
 * @since 1.0
 *        @date made:		01-05-2010
 *        @date last changed:	02-05-2010
 */
define('NIV', '../');
if (! class_exists('\includes\BaseLogicClass')) {
    require (NIV . 'includes/BaseLogicClass.php');
}

class Error403 extends \includes\BaseLogicClass
{

    /**
     * Starts the class Error504
     */
    public function __construct()
    {
        $this->init();
        
        $this->displayError();
        
        $this->showLayout();
    }

    private function displayError()
    {
        header("HTTP/1.1 403 Forbidden");
        
        $service_Language = Memory::services('Language');
        
        $this->service_Template->set('title', $service_Language->get('language/errors/error403/accessDenied'));
        
        $this->service_Template->set('notice', $service_Language->get('language/errors/error403/noRights'));
        
        if (isset($_SESSION['error'])) {
            if (isset($_SESSION['errorObject'])) {
                Memory::services('ErrorHandler')->error($_SESSION['errorObject']);
            } else {
                Memory::services('ErrorHandler')->errorAsString($_SESSION['error']);
            }
            
            if (defined('DEBUG')) {
                $this->service_Template->set('debug_notice', $_SESSION['error']);
            }
        }
    }
}

$obj_Error = new Error403();
unset($obj_Error);
