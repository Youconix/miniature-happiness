<?php
namespace core\helpers;

if (! class_exists('AdminUserAdd')) {
    \core\Memory::helpers('AdminUserAdd');
}

class AdminUserView extends AdminUserAdd
{

    protected $s_template = 'admin/modules/general/users/userview.tpl';

    protected $a_data;

    protected $obj_User;

    public function run()
    {
        parent::run();
        
        $this->service_Template->set('blockedHeader', $this->service_Language->get('system/admin/users/blocked'));
        $this->service_Template->set('loggedinHeader', $this->service_Language->get('system/admin/users/loggedIn'));
        $this->service_Template->set('registratedHeader', $this->service_Language->get('system/admin/users/registrated'));
        $this->service_Template->set('activeHeader', $this->service_Language->get('system/admin/users/activated'));
        
        $this->service_Template->set('username', $this->a_data['username']);
        $this->service_Template->set('email', $this->a_data['email']);
        $this->service_Template->set('bot', $this->a_data['bot']);
        $this->service_Template->set('registrated', $this->a_data['registrated']);
        $this->service_Template->set('loggedIn', $this->a_data['loggedin']);
        $this->service_Template->set('active', $this->a_data['active']);
        $this->service_Template->set('blocked', $this->a_data['blocked']);
        $this->service_Template->set('id', $this->a_data['id']);
        
        $this->checkDeleteOption();
    }

    protected function checkDeleteOption()
    {
        (USERID == $this->a_data['id']) ? $s_deleteRejected = 'style="color:grey; text-decoration: line-through; cursor:auto"' : $s_deleteRejected = '';
        
        $this->service_Template->set('deleteRejected', $s_deleteRejected);
        
        $this->service_Template->set('edit', t('system/buttons/edit'));
        $this->service_Template->set('delete', t('system/buttons/delete'));
        $this->service_Template->set('loginAss', 'Inloggen als gebruiker');
    }

    protected function setGroups()
    {
        $model_Group = \core\Memory::models('Groups');
        $a_groups = $this->obj_User->getGroups();
        
        foreach ($a_groups as $i_id => $i_level) {
            $obj_group = $model_Group->getGroup($i_id);
            
            $this->service_Template->setBlock('userGroup', array(
                'name' => $obj_group->getName(),
                'level' => $this->service_Language->get('system/rights/level_' . $i_level)
            ));
        }
    }

    public function setData($obj_User)
    {
        $this->obj_User = $obj_User;
        
        $s_yes = $this->service_Language->get('system/admin/users/yes');
        $s_no = $this->service_Language->get('system/admin/users/no');
        
        $this->a_data = array(
            'id' => $obj_User->getID(),
            'username' => $obj_User->getUsername(),
            'email' => $obj_User->getEmail(),
            'bot' => ($obj_User->isBot() ? $s_yes : $s_no),
            'registrated' => ($obj_User->getRegistrated() == 0) ? '-' : $this->a_data['registrated'] = date('d-m-Y H:i', $obj_User->getRegistrated()),
            'loggedin' => ($obj_User->lastLoggedIn() == 0) ? '-' : $this->a_data['loggedin'] = date('d-m-Y H:i', $obj_User->lastLoggedIn()),
            'active' => ($obj_User->isEnabled()) ? $s_yes : $s_no,
            'blocked' => ($obj_User->isBlocked()) ? $s_yes : $s_no
        );
    }
}