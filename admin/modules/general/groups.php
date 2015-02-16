<?php
namespace admin;

/**
 * Admin group configuration class
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 1.0
 *        @changed 08/12/10
 *       
 *        Scripthulp framework is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU Lesser General Public License as published by
 *        the Free Software Foundation, either version 3 of the License, or
 *        (at your option) any later version.
 *       
 *        Scripthulp framework is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *        GNU General Public License for more details.
 *       
 *        You should have received a copy of the GNU Lesser General Public License
 *        along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 *       
 *       
 */
define('NIV', '../../../');
include (NIV . 'core/AdminLogicClass.php');

class Groups extends \core\AdminLogicClass
{

    private $model_Groups;

    /**
     * PHP 5 constructor
     */
    public function __construct()
    {
        $this->init();
        
        if (! \core\Memory::models('Config')->isAjax()) {
            exit();
        }
        
        if (isset($this->get['command'])) {
            if ($this->get['command'] == 'getGroup') {
                $this->getGroup();
            } else 
                if ($this->get['command'] == 'viewUsers') {
                    $this->viewUsers();
                } else 
                    if ($this->get['command'] == 'addScreen') {
                        $this->addScreen();
                    }
        } else 
            if (isset($this->post['command'])) {
                if ($this->post['command'] == 'add') {
                    $this->add();
                } else 
                    if ($this->post['command'] == 'edit') {
                        $this->edit();
                    } else 
                        if ($this->post['command'] == 'delete') {
                            $this->delete();
                        }
            } else {
                $this->groupview();
            }
    }

    /**
     * Inits the class Groups
     */
    protected function init()
    {
        $this->init_get = array(
            'id' => 'int'
        );
        
        $this->init_post = array(
            'id' => 'int',
            'name' => 'string-DB',
            'description' => 'string-DB',
            'default' => 'int',
            'id' => 'int'
        );
        
        parent::init();
        
        $this->model_Groups = \core\Memory::models('Groups');
    }

    /**
     * Generates the group overview
     */
    private function groupview()
    {
        $this->setHeader();
        $a_groups = $this->model_Groups->getGroups();
        
        $this->service_Template->set('groupTitle', t('system/admin/groups/groups'));
        $this->service_Template->set('headerID', t('system/admin/groups/id'));
        
        foreach ($a_groups as $obj_group) {
            $a_data = array(
                'id' => $obj_group->getID(),
                'name' => $obj_group->getName(),
                'description' => $obj_group->getDescription(),
                'default' => ($obj_group->isDefault() ? 1 : 0)
            );
            if ($obj_group->inUse()) {
                $this->service_Template->setBlock('groupBlocked', $a_data);
            } else {
                $this->service_Template->setBlock('group', $a_data);
            }
        }
        
        $this->service_Template->set('buttonDelete', t('system/buttons/delete'));
        $this->service_Template->set('addButton', t('system/buttons/add'));
    }

    /**
     * Generates the group details
     */
    private function getGroup()
    {
        try {
            $obj_group = $this->model_Groups->getGroup($this->get['id']);
        } catch (Exception $e) {
            Memory::services('Logs')->securityLog('Call to unknown group ' . $this->get['id'] . '.');
            header('location: ' . NIV . 'logout.php');
            exit();
        }
        
        $this->setHeader();
        $this->service_Template->set('nameDefault', $obj_group->getName());
        $this->service_Template->set('descriptionDefault', $obj_group->getDescription());
        if ($obj_group->isDefault())
            $this->service_Template->set('defaultChecked', 'checked="checked"');
        $this->service_Template->set('id', $this->get['id']);
        
        $this->service_Template->set('editTitle', t('system/admin/groups/headerEdit'));
        $this->service_Template->set('buttonCancel', t('system/buttons/cancel'));
        $this->service_Template->set('buttonSubmit', t('system/buttons/edit'));
        
        $this->service_Template->set('buttonBack', t('system/buttons/back'));
        $this->service_Template->set('delete', t('system/buttons/delete'));
    }

    private function viewUsers()
    {
        try {
            $obj_Group = $this->model_Groups->getGroup($this->get['id']);
            
            $a_users = $obj_Group->getMembersByGroup();
            foreach ($a_users as $a_user) {
                $a_data = array(
                    'id' => $a_user['userid'],
                    'username' => $a_user['nick'],
                    'level' => ''
                );
                
                $a_data['rights'] = t('rights/level_' . $a_user['level']);
                
                $this->service_Template->setBlock('user', $a_data);
            }
            
            $this->service_Template->set('groupTitle', t('system/admin/groups/headerUsers') . ' ' . $obj_Group->getName());
            $this->service_Template->set('headerUser', t('system/admin/groups/user'));
            $this->service_Template->set('headerRights', t('system/admin/groups/rights'));
            $this->service_Template->set('backButton', t('system/buttons/back'));
        } catch (Exception $e) {
            Memory::services('Logs')->securityLog('Call to unknown group ' . $this->get['id'] . '.');
            header('location: ' . NIV . 'logout.php');
            exit();
        }
    }

    /**
     * Displays the add screen
     */
    private function addScreen()
    {
        $this->setHeader();
        $this->service_Template->set('groupTitle', t('system/admin/groups/headerAdd'));
        $this->service_Template->set('buttonCancel', t('system/buttons/cancel'));
        $this->service_Template->set('buttonSubmit', t('system/buttons/save'));
        $this->service_Template->set('buttonBack', t('system/buttons/back'));
    }

    /**
     * Adds a new group
     */
    private function add()
    {
        if (! isset($this->post['name']) || $this->post['name'] == '' || ! isset($this->post['description']) || $this->post['description'] == '' || ! isset($this->post['default']) || ($this->post['default'] != 0 && $this->post['default'] != 1)) {
            return;
        }
        
        $obj_Group = $this->model_Groups->generateGroup();
        $obj_Group->setName($this->post['name']);
        $obj_Group->setDescription($this->post['description']);
        if ($this->post['default'] == 1) {
            $obj_Group->setDefault(true);
            
            $obj_Group->addUsersToDefault();
        } else 
            if ($this->post['default'] == 0) {
                $obj_Group->setDefault(false);
            }
        $obj_Group->save();
    }

    /**
     * Deletes the given group
     */
    private function delete()
    {
        if (! isset($this->post['id']) || $this->post['id'] <= 0)
            return;
            
            /* Get group */
        try {
            $obj_Group = $this->model_Groups->getGroup($this->post['id']);
            $obj_Group->deleteGroup();
        } catch (Exception $e) {
            Memory::services('Logs')->securityLog('Call to unknown group ' . $this->post['id'] . '.');
            header('location: ' . NIV . 'logout.php');
            exit();
        }
    }

    /**
     * Edits the group
     */
    private function edit()
    {
        if (! isset($this->post['name']) || $this->post['name'] == '' || ! isset($this->post['description']) || $this->post['description'] == '' || ! isset($this->post['default']) || ($this->post['default'] != 0 && $this->post['default'] != 1) || ! isset($this->post['id']) || $this->post['id'] <= 0) {
            return;
        }
        
        /* Get group */
        try {
            $obj_Group = $this->model_Groups->getGroup($this->post['id']);
            $obj_Group->setName($this->post['name']);
            $obj_Group->setDescription($this->post['description']);
            if ($this->post['default'] == 1 && ! $obj_Group->isDefault()) {
                $obj_Group->setDefault(true);
                
                $obj_Group->addUsersToDefault();
            } else 
                if ($this->post['default'] == 0) {
                    $obj_Group->setDefault(false);
                }
            
            $obj_Group->persist();
        } catch (Exception $e) {
            Memory::services('Logs')->securityLog('Call to unknown group ' . $this->post['id'] . '.');
            header('location: ' . NIV . 'logout.php');
            exit();
        }
    }

    /**
     * Sets the headers
     */
    private function setHeader()
    {
        $this->service_Template->set('headerName', t('system/admin/groups/name'));
        $this->service_Template->set('headerDescription', t('system/admin/groups/description'));
        $this->service_Template->set('headerAutomatic', t('system/admin/groups/standard'));
    }
}

$obj_Groups = new Groups();
unset($obj_Groups);
?>