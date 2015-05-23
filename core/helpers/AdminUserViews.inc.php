<?php
namespace core\helpers;

class AdminUserViews extends \core\helpers\Helper {
    protected $service_Language;
    
    protected $service_Template;
    
    protected $model_Groups;
    
    protected $s_modus = 'add';
    
    public function __construct(\core\services\Language $service_Language, \core\services\Template $service_Template,\core\models\Groups $model_Groups)
    {
        $this->service_Language = $service_Language;
        $this->service_Template = $service_Template;
        $this->model_Groups = $model_Groups;
    }
    
    public function setModus($s_modus){
        if( in_array($s_modus,array('add','view','edit')) ){
            $this->s_modus = $s_modus;
        }
    }
    
    public function run()
    {
        $this->service_Template->set('userid', USERID);
    
        $this->headersGeneral();
        
        if( $this->s_modus == 'add' ){
            return;
        }
        
        $this->runView();
        
        $this->checkDeleteOption();
        
        if( $this->s_modus == 'view' ){
            $this->setGroupsView();
            return;
        }
    
        $a_bot = array(
            0 => '',
            1 => ''
        );
        $a_bot[$this->obj_User->isBot()] = 'checked="checked"';
        
        $a_blocked = array(
            0 => '',
            1 => ''
        );
        $a_blocked[$this->obj_User->isBlocked()] = 'checked="checked"';
        
        $this->service_Template->set('bot0', $a_bot[0]);
        $this->service_Template->set('bot1', $a_bot[1]);
        $this->service_Template->set('blocked0', $a_blocked[0]);
        $this->service_Template->set('blocked1', $a_blocked[1]);
        
        $this->setGroupsEdit();
    }
    
    private function runView(){
        $this->service_Template->set('blockedHeader', $this->service_Language->get('system/admin/users/blocked'));
        $this->service_Template->set('loggedinHeader', $this->service_Language->get('system/admin/users/loggedIn'));
        $this->service_Template->set('registratedHeader', $this->service_Language->get('system/admin/users/registrated'));
        $this->service_Template->set('activeHeader', $this->service_Language->get('system/admin/users/activated'));
        $this->service_Template->set('headerText', $this->service_Language->get('system/admin/users/headerView'));
        
        $this->service_Template->set('username', $this->a_data['username']);
        $this->service_Template->set('email', $this->a_data['email']);
        $this->service_Template->set('bot', $this->a_data['bot']);
        $this->service_Template->set('registrated', $this->a_data['registrated']);
        $this->service_Template->set('loggedIn', $this->a_data['loggedin']);
        $this->service_Template->set('active', $this->a_data['active']);
        $this->service_Template->set('blocked', $this->a_data['blocked']);
        $this->service_Template->set('id', $this->a_data['id']);
    }

    private function checkDeleteOption()
    {
        (USERID == $this->a_data['id']) ? $s_deleteRejected = 'style="color:grey; text-decoration: line-through; cursor:auto"' : $s_deleteRejected = '';
        
        $this->service_Template->set('deleteRejected', $s_deleteRejected);
        $this->service_Template->set('delete', t('system/buttons/delete'));
    }
    
    protected function setGroupsView(){
        $a_groups = $this->obj_User->getGroups();
        
        foreach ($a_groups as $i_id => $i_level) {
            $obj_group = $this->model_Groups->getGroup($i_id);
        
            $this->service_Template->setBlock('userGroup', array(
                'name' => $obj_group->getName(),
                'level' => $this->service_Language->get('system/rights/level_' . $i_level)
            ));
        }
    }

    protected function setGroupsEdit()
    {
        $a_groups = $this->obj_User->getGroups();
        
        $a_currentGroups = array();
        foreach ($a_groups as $i_id => $i_level) {
            $obj_group = $this->model_Groups->getGroup($i_id);
            
            $a_data = array(
                'name' => $obj_group->getName(),
                'level' => $this->service_Language->get('system/rights/level_' . $i_level),
                'levelNr' => $i_level,
                'id' => $obj_group->getID()
            );
            
            if (($obj_group->getID() == 0) && ($this->obj_User->getID() == USERID)) {
                $this->service_Template->setBlock('userGroupBlocked', $a_data);
            } else {
                $this->service_Template->setBlock('userGroup', $a_data);
            }
            
            $a_currentGroups[] = $obj_group->getID();
        }
        
        $a_groups = $this->model_Groups->getGroups();
        foreach ($a_groups as $obj_group) {
            if (in_array($obj_group->getID(), $a_currentGroups)) {
                continue;
            }
            
            $this->service_Template->setBlock('newGroup', array(
                'value' => $obj_group->getID(),
                'text' => $obj_group->getName()
            ));
        }
        
        for ($i = 0; $i <= 2; $i ++) {
            $this->service_Template->setBlock('newLevel', array(
                'value' => $i,
                'text' => $this->service_Language->get('system/rights/level_' . $i)
            ));
        }
    }
    
    private function headersGeneral(){
        $this->service_Template->set('usernameHeader', $this->service_Language->get('system/admin/users/username'));
        $this->service_Template->set('emailHeader', $this->service_Language->get('system/admin/users/email'));
        $this->service_Template->set('headerText', $this->service_Language->get('system/admin/users/headerAdd'));
        $this->service_Template->set('botHeader', $this->service_Language->get('system/admin/users/bot'));
        
        $this->service_Template->set('buttonBack', $this->service_Language->get('system/buttons/back'));
        
        $this->service_Template->set('no', $this->service_Language->get('system/admin/users/no'));
        $this->service_Template->set('yes', $this->service_Language->get('system/admin/users/yes'));
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