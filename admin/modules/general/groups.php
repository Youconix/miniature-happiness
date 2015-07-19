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
    private $groups;
    private $a_systemGroups = array(0,1);

    /**
     * Groups constructor
     *
     * @param \Input $Input
     *            The input parser
     * @param \Config $config            
     * @param \Language $language 
     * @param \Output $template            
     * @param \Logger $logs            
     * @param \core\models\Groups $groups            
     */
    public function __construct(\Input $Input, \Config $config, \Language $language, \Output $template, \Logger $logs, \core\models\Groups $groups)
    {
        parent::__construct($Input, $config, $language, $template, $logs);
        
        $this->groups = $groups;
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
        $a_groups = $this->groups->getGroups();
        
        $this->template->set('groupTitle', t('system/admin/groups/groups'));
        $this->template->set('headerID', t('system/admin/groups/id'));
        
        foreach ($a_groups as $obj_group) {
            $a_data = array(
                'id' => $obj_group->getID(),
                'name' => $obj_group->getName(),
                'description' => $obj_group->getDescription(),
                'default' => ($obj_group->isDefault() ? 1 : 0)
            );
            $this->template->setBlock('group', $a_data);
        }
        
        $this->template->set('buttonDelete', t('system/buttons/delete'));
        $this->template->set('addButton', t('system/buttons/add'));
    }

    /**
     * Displays the group
     */
    private function view()
    {
        try {
            $obj_group = $this->groups->getGroup($this->get['id']);
        } catch (\Exception $e) {
            $this->logs->info('Call to unknown group ' . $this->get['id'] . '.',array('type'=>'securityLog'));
            exit();
        }
        
        $this->setHeader();
        $this->template->set('nameDefault', $obj_group->getName());
        $this->template->set('descriptionDefault', $obj_group->getDescription());
        ($obj_group->isDefault()) ? $s_automatic = t('system/yes') : $s_automatic = t('system/no');
        $this->template->set('automatic', $s_automatic);
        $this->template->set('id', $this->get['id']);
        
        $this->template->set('groupTitle', t('system/admin/groups/headerView') . ' ' . $obj_group->getName());
        
        $this->template->set('buttonBack', t('system/buttons/back'));
        $this->template->set('buttonDelete', t('system/buttons/delete'));
        $this->template->set('buttonEdit', t('system/buttons/edit'));
        $this->template->set('memberlistTitle', t('system/admin/groups/memberlist'));
        
        if (in_array($this->get['id'], $this->a_systemGroups)) {
            $this->template->set('editDisabled', 'style="color:grey; text-decoration: line-through; cursor:auto"');
        }
        
        /* Display users */
        $a_users = $obj_group->getMembersByGroup();
        foreach ($a_users as $a_user) {
            $this->template->setBlock('userlist', array(
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
            $obj_group = $this->groups->getGroup($this->get['id']);
        } catch (\Exception $e) {
            $this->logs->info('Call to unknown group ' . $this->get['id'] . '.',array('type'=>'securityLog'));
            exit();
        }
        
        $this->setHeader();
        $this->template->set('nameDefault', $obj_group->getName());
        $this->template->set('descriptionDefault', $obj_group->getDescription());
        if ($obj_group->isDefault())
            $this->template->set('defaultChecked', 'checked="checked"');
        $this->template->set('id', $this->get['id']);
        
        $this->template->set('editTitle', t('system/admin/groups/headerEdit'));
        $this->template->set('buttonCancel', t('system/buttons/cancel'));
        $this->template->set('buttonSubmit', t('system/buttons/edit'));
        
        $this->template->set('buttonBack', t('system/buttons/back'));
        $this->template->set('delete', t('system/buttons/delete'));
    }

    /**
     * Displays the add screen
     */
    private function addScreen()
    {
        $this->setHeader();
        $this->template->set('groupTitle', t('system/admin/groups/headerAdd'));
        $this->template->set('buttonCancel', t('system/buttons/cancel'));
        $this->template->set('buttonSubmit', t('system/buttons/save'));
        $this->template->set('buttonBack', t('system/buttons/back'));
    }

    /**
     * Adds a new group
     */
    private function add()
    {
        if (! $this->post->validate(array(
            'name' => 'required',
            'description' => 'required',
            'default' => 'required|set:0,1'
        ))) {
            return;
        }
        
        $obj_Group = $this->groups->generateGroup();
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
        if (! $this->post->validate(array(
            'id' => 'required|min:2'
        ))) {
            return;
        }
        
        /* Get group */
        try {
            $obj_Group = $this->groups->getGroup($this->post['id']);
            $obj_Group->deleteGroup();
        } catch (\Exception $e) {
            $this->logs->info('Call to unknown group ' . $this->post['id'] . '.',array('type'=>'securityLog'));
            exit();
        }
    }

    /**
     * Edits the group
     */
    private function edit()
    {
        if (! $this->post->validate(array(
            'name' => 'required',
            'description' => 'required',
            'default' => 'required|set:0,1'
        ))) {
            return;
        }
        
        /* Get group */
        try {
            $obj_Group = $this->groups->getGroup($this->post['id']);
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
        } catch (\Exception $e) {
            $this->logs->info('Call to unknown group ' . $this->post['id'] . '.',array('type'=>'securityLog'));
            exit();
        }
    }

    /**
     * Sets the headers
     */
    private function setHeader()
    {
        $this->template->set('headerName', t('system/admin/groups/name'));
        $this->template->set('headerDescription', t('system/admin/groups/description'));
        $this->template->set('headerAutomatic', t('system/admin/groups/standard'));
    }
}