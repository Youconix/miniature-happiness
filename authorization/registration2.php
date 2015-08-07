<?php
namespace authorization;

class Registration2 extends \includes\BaseLogicClass
{

    /**
     *
     * @var \core\models\Login
     */
    private $login;

    /**
     *
     * @var \core\models\User
     */
    private $user;

    private $i_userid;

    /**
     * Constructor
     *
     * @param \Input $input
     *            The input parser
     * @param \Config $config            
     * @param \Language $language            
     * @param \Output $template            
     * @param \Header $header            
     * @param \Menu $menu            
     * @param \Footer $footer            
     * @param \core\models\Login $login            
     * @param \core\models\User $user            
     * @param \core\services\Session $session            
     */
    public function __construct(\Input $input, \Config $config, \Language $language, \Output $template, \Header $header, \Menu $menu, \Footer $footer, \core\models\Login $login, \core\models\User $user, \core\services\Session $session)
    {
        parent::__construct($input, $config, $language, $template, $header, $menu, $footer);
        
        $this->login = $login;
        $this->user = $user;
        $this->i_userid = $this->session->get('userid');
    }

    protected function index()
    {
        $user = $this->user->get($this->i_userid);
        $this->login->setLogin($user);
    }
}