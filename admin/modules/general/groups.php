<?php
namespace admin\modules\general;

/**
 * Miniature-happiness is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Miniature-happiness is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Miniature-happiness. If not, see <http://www.gnu.org/licenses/>.
 *
 * Admin group configuration class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Groups extends \core\AdminLogicClass
{

    /**
     *
     * @var \core\models\Groups
     */
    private $model_Groups;

    private $a_systemGroups = array(
        0,
        1
    );

    /**
     * Groups constructor
     *
     * @param \core\Input $Input    The input parser  
     * @param \core\models\Config $model_Config            
     * @param \core\services\Language $service_Language            
     * @param \core\services\Template $service_Template            
     * @param \core\models\Groups $model_Groups            
     */
    public function __construct(\core\Input $Input, \core\models\Config $model_Config, \core\services\Language $service_Language, \core\services\Template $service_Template, \core\models\Groups $model_Groups)
    {
        parent::__construct($Input, $model_Config, $service_Language, $service_Template);
        
        $this->model_Groups = $model_Groups;
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            switch ($s_command) {
                case 'view':
                    $this->view();
                    break;
                case 'getGroup':
                    $this->getGroup();
                    break;
                
                case 'addScreen':
                    $this->addScreen();
                    break;
                default:
                    $this->groupview();
                    break;
            }
        } else {
            switch ($s_command) {
                case 'add':
                    $this->add();
                    break;
                
                case 'edit':
                    $this->edit();
                    break;
                
                case 'delete':
                    $this->delete();
                    break;
            }
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
            $this->service_Template->setBlock('group', $a_data);
        }
        
        $this->service_Template->set('buttonDelete', t('system/buttons/delete'));
        $this->service_Template->set('addButton', t('system/buttons/add'));
    }

    /**
     * Displays the group
     */
    private function view()
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
        ($obj_group->isDefault()) ? $s_automatic = t('system/yes') : $s_automatic = t('system/no');
        $this->service_Template->set('automatic', $s_automatic);
        $this->service_Template->set('id', $this->get['id']);
        
        $this->service_Template->set('groupTitle', t('system/admin/groups/headerView') . ' ' . $obj_group->getName());
        
        $this->service_Template->set('buttonBack', t('system/buttons/back'));
        $this->service_Template->set('buttonDelete', t('system/buttons/delete'));
        $this->service_Template->set('buttonEdit', t('system/buttons/edit'));
        $this->service_Template->set('memberlistTitle', t('system/admin/groups/memberlist'));
        
        if (in_array($this->get['id'], $this->a_systemGroups)) {
            $this->service_Template->set('editDisabled', 'style="color:grey; text-decoration: line-through; cursor:auto"');
        }
        
        /* Display users */
        $a_users = $obj_group->getMembersByGroup();
        foreach ($a_users as $a_user) {
            $this->service_Template->setBlock('userlist', array(
                'userid' => $a_user['id'],
                'user' => $a_user['username'],
                'level' => t('system/rights/level_' . $a_user['level'])
            ));
        }
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
        if (! isset($this->post['id']) || $this->post['id'] <= 0 || in_array($this->post['id'], $this->a_systemGroups))
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
        if (! isset($this->post['name']) || $this->post['name'] == '' || ! isset($this->post['description']) || $this->post['description'] == '' || ! isset($this->post['default']) || ($this->post['default'] != 0 && $this->post['default'] != 1) || ! isset($this->post['id']) || $this->post['id'] <= 0 || in_array($this->post['id'], $this->a_systemGroups)) {
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