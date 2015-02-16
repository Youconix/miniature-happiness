<?php
namespace core\helpers;

if (! class_exists('AdminUserView')) {
    \core\Memory::helpers('AdminUserView');
}

class AdminUserEdit extends AdminUserView
{

    protected $service_Language;

    protected $service_Template;

    protected $s_template = 'admin/modules/general/users/useredit.tpl';

    protected $a_data;

    public function run()
    {
        parent::run();
        
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
        
        $this->checkDeleteOption();
    }

    protected function checkDeleteOption()
    {
        (USERID == $this->a_data['id']) ? $s_deleteRejected = 'style="color:grey; text-decoration: line-through; cursor:auto"' : $s_deleteRejected = '';
        
        $this->service_Template->set('deleteRejected', $s_deleteRejected);
        $this->service_Template->set('delete', t('system/buttons/delete'));
    }

    protected function setGroups()
    {
        $model_Group = \core\Memory::models('Groups');
        $a_groups = $this->obj_User->getGroups();
        
        $a_currentGroups = array();
        foreach ($a_groups as $i_id => $i_level) {
            $obj_group = $model_Group->getGroup($i_id);
            
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
        
        $a_groups = $model_Group->getGroups();
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
}