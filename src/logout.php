<?php
use \youconix\core\templating\BaseController as BaseController;
use \youconix\core\models\Login as Login;

/**
 * Log out page.
 * Logs the user out of the system
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
if (strpos($_SERVER['REQUEST_URI'], 'logout.php') !== false) {
    $s_url = str_replace('logout.php', 'logout/performLogout', $_SERVER['REQUEST_URI']);
    header('location: ' . $s_url);
    die();
}

class Logout extends BaseController
{

    /**
     *
     * @var \core\models\Login
     */
    private $login;

    /**
     * Starts the class Logout
     *
     * @param \Request $request            
     * @param \core\models\Login $login            
     */
    public function __construct(\Request $request, Login $login)
    {
        parent::__construct($request);
        
        $this->login = $login;
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        $this->performLogout();
    }

    /**
     * Logs the user out
     */
    protected function performLogout()
    {
        $this->login->logout();
    }
}