<?php
namespace core\helpers;

class AdminUserAdd extends Helper
{

    protected $service_Language;

    protected $service_Template;

    protected $s_template = 'admin/modules/general/users/useradd.tpl';

    public function __construct(\core\services\Language $service_Language, \core\services\Template $service_Template)
    {
        $this->service_Language = $service_Language;
        $this->service_Template = $service_Template;
    }

    public function run()
    {
        $this->service_Template->loadTemplate('adminUserView', $this->s_template);
        
        $this->service_Template->set('userid', USERID);
        
        $this->service_Template->set('usernameHeader', $this->service_Language->get('system/admin/users/username'));
        $this->service_Template->set('emailHeader', $this->service_Language->get('system/admin/users/email'));
        $this->service_Template->set('headerText', $this->service_Language->get('system/admin/users/headerView'));
        $this->service_Template->set('botHeader', $this->service_Language->get('system/admin/users/bot'));
        
        $this->service_Template->set('buttonBack', $this->service_Language->get('system/buttons/back'));
        
        $this->service_Template->set('no', $this->service_Language->get('system/admin/users/no'));
        $this->service_Template->set('yes', $this->service_Language->get('system/admin/users/yes'));
        
        $this->setGroups();
    }

    protected function setGroups()
    {}
}