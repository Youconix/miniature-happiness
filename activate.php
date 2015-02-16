<?php
/**
 * Account activation page
 * Does not work for openID accounts
 *
 * @author:		Rachelle Scheijen <rachelle.scheijen@unixerius.nl>
 * @copyright	The au pair BV	2013
 * @version		1.0
 * @since		1.0
 * @date		25/09/12
 * @changed		25/11/12
 */
define('NIV', './');
include (NIV . 'core/BaseLogicClass.php');

class Activation extends \core\BaseLogicClass
{

    private $model_User;

    /**
     * PHP 5 constructor
     */
    public function __construct()
    {
        $this->init();
        
        $this->activate();
        
        $this->header();
        
        $this->footer();
    }

    /**
     * Inits the class Activation
     */
    protected function init()
    {
        $this->init_get = array(
            'key' => 'string-DB'
        );
        
        parent::init();
        
        $this->model_User = Memory::models('User');
    }

    /**
     * Activates the user account
     */
    private function activate()
    {
        if (! isset($this->get['key'])) {
            header('location:index.php');
            exit();
        }
        
        if ($this->model_User->activateUser($this->get['key'])) {
            $this->service_Template->set('content', '<h2 class="notice">' . $this->service_Language->get('language/activate/accountActivated') . '</h2>');
        } else {
            $this->service_Template->set('content', '<h2 class="errorNotice">' . $this->service_Language->get('language/activate/accountNotActivated') . '</h2>');
        }
    }
}

$obj_Activation = new Activation();
unset($obj_Activation);