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
 * Admin page rights configuration class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 2.0
 */
class Pages extends \core\AdminLogicClass
{

    /**
     *
     * @var \core\models\PrivilegeController
     */
    private $privilegeController;

    /**
     *
     * @var \core\models\Groups
     */
    private $groups;

    /**
     *
     * @var \core\services\File
     */
    private $file;

    /**
     * Starts the class Pages
     *
     * @param \Input $Input            
     * @param \Config $config            
     * @param \Language $language            
     * @param \Output $template            
     * @param \Logger $logs            
     * @param \core\models\PrivilegeController $privilegeController            
     * @param \core\models\Groups $groups            
     * @param \core\services\File $file            
     */
    public function __construct(\Input $Input, \Config $config, \Language $language, \Output $template, \Logger $logs, \core\models\PrivilegeController $privilegeController, \core\models\Groups $groups, \core\services\File $file)
    {
        parent::__construct($Input, $config, $language, $template, $logs);
        
        $this->privilegeController = $privilegeController;
        $this->groups = $groups;
        $this->file = $file;
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            switch ($s_command) {
                case 'edit':
                    $this->edit();
                    break;
                
                case 'delete':
                    $this->delete();
                    break;
                
                case 'addView':
                    $this->addView();
                    break;
                
                case 'deleteView':
                    $this->deleteView();
                    break;
                
                case 'reset':
                    $this->reset();
                    break;
            }
        } else {
            if ($s_command == 'index') {
                $this->index();
            } else 
                if ($s_command == 'view') {
                    $this->view();
                }
        }
    }

    /**
     * Inits the class Pages
     */
    protected function init()
    {
        $this->init_get = array(
            'url' => 'string-DB'
        );
        $this->init_post = array(
            'url' => 'string-DB',
            'rights' => 'int',
            'group' => 'int',
            'view' => 'string-DB',
            'id' => 'int'
        );
        
        parent::init();
    }

    /**
     * Shows the index
     */
    private function index()
    {
        $a_files = $this->privilegeController->getPages();
        
        $this->template->set('pageTitle', t('system/general/pages/pageTitle'));
        $this->template->set('pages', $this->indexDir($a_files[1], $a_files[0], ''));
    }

    /**
     * Shows the controller tree
     * 
     * @param array $a_files    The files
     * @param string $s_root    The files root
     * @param string $s_parent  The parent directory
     */
    private function indexDir($a_files, $s_root, $s_parent)
    {
        $s_pages = '';
        foreach ($a_files as $key => $file) {
            if (is_numeric($key)) {
                $s_url = str_replace($s_root, '', $file->getPathname());
                if (substr($s_url, 0, 1) != DIRECTORY_SEPARATOR) {
                    $s_url = DIRECTORY_SEPARATOR . $s_url;
                }
                $s_pages .= '<li data-url="' . $s_url . '" class="link">' . $file->getBaseName() . "</li>\n";
            } else {
                $s_pages .= '<li><span class="directory_pointer" data-url="' . $s_parent . DIRECTORY_SEPARATOR . $key . '">' . $key . '</span><ul>
              ' . $this->indexDir($a_files[$key], $s_root, $s_parent . DIRECTORY_SEPARATOR . $key) . '
              </ul>';
            }
        }
        
        return $s_pages;
    }

    /**
     * Shows the edit text
     */
    private function viewEditText()
    {
        $this->template->set('buttonBack', t('system/buttons/back'));
        $this->template->set('buttonDelete', t('system/buttons/delete'));
        $this->template->set('delete', t('system/buttons/delete'));
        $this->template->set('save', t('system/buttons/save'));
        $this->template->set('add', t('system/buttons/add'));
        
        $this->template->set('generalRightsHeader',t('system/general/pages/generalRightsHeader'));
        $this->template->set('groupLabel', t('system/general/pages/group'));
        $this->template->set('groupDefault', t('system/general/pages/groupDefault'));
        $this->template->set('accessLevelLabel', t('system/general/pages/accessLevel'));
        $this->template->set('viewRightsTitle', t('system/general/pages/viewRights'));
        $this->template->set('viewRightsDefault',t('system/general/pages/viewRightsDefault'));
        $this->template->set('pageTitle', t('system/general/pages/editPageTitle'));
        $this->template->set('viewLabel',t('system/general/pages/view'));
    }

    /**
     * Shows the edit page
     */
    private function view()
    {
        $this->viewEditText();
        
        $s_url = $this->prepareUrl($this->get->get('url'));
        
        $this->template->set('url', $s_url);
        
        $this->template->set('reset', t('system/buttons/reset'));
        $a_rights = $this->privilegeController->getRightsForPage($s_url);
        $this->setGroupList('groups', $a_rights['general']['groupID']);
        $this->setGroupList('groups2', - 1);
        
        $this->template->set('name', $a_rights['page']);
        $this->setAccessList('pageRight', $a_rights['general']['minLevel']);
        $this->setAccessList('templateRight', - 1);
        
        foreach ($a_rights['commands'] as $a_right) {
            $this->template->setBlock('template_rights', array(
                'id' => $a_right['id'],
                'command' => $a_right['command'],
                'group' => $this->groups->getGroup($a_right['groupID'])
                    ->getName(),
                'level' => t('system/rights/level_' . $a_right['minLevel'])
            ));
        }
    }

    /**
     * Shows the group select box
     * 
     * @param string $s_key     The field name
     * @param int $i_default    The default ID
     */
    private function setGroupList($s_key, $i_default)
    {
        $a_groups = $this->groups->getGroups();
        
        foreach ($a_groups as $model_Group) {
            ($model_Group->getID() == $i_default) ? $s_selected = 'selected="selected"' : $s_selected = '';
            
            $this->template->setBlock($s_key, array(
                'value' => $model_Group->getID(),
                'selected' => $s_selected,
                'text' => $model_Group->getName()
            ));
        }
    }

    /**
     * Shows the access list select box
     * 
     * @param string $s_key     The field name
     * @param int $i_default    The default ID
     */
    private function setAccessList($s_key, $i_default)
    {
        for ($i = 0; $i <= 2; $i ++) {
            ($i == $i_default) ? $s_selected = 'selected="selected"' : $s_selected = '';
            $this->template->setBlock($s_key, array(
                'value' => $i,
                'selected' => $s_selected,
                'text' => t('system/rights/level_' . $i)
            ));
        }
    }

    /**
     * Edits the page rights
     */
    private function edit()
    {
        if (! $this->post->validate(array(
            'url' => 'required',
            'rights' => 'required|set:0,1,2',
            'group' => 'required|type:int|min:0'
        ))) {
            return;
        }
        
        /* Check group */
        $a_groups = $this->groups->getGroups();
        if (! array_key_exists($this->post['group'], $a_groups)) {
            return;
        }
        
        $s_url = $this->prepareUrl($this->post->get('url'));
        
        $this->privilegeController->changePageRights($s_url, $this->post->get('rights'), $this->post->get('group'));
    }

    /**
     * Deletes the page.
     */
    private function delete()
    {
        $s_fileURI = NIV . $this->post['url'];
        $s_templatesURI = $this->config->getStylesDir() . str_replace('.php', '', $this->post->get('url'));
        
        $s_url = $this->prepareUrl($this->post->get('url'));
        $this->privilegeController->deletePageRights($s_url);
        if ($this->file->exists($s_fileURI)) {
            $this->file->deleteFile(NIV . $this->post->get('url'));
        }
        if ($this->file->exists($s_templatesURI)) {
            $this->file->deleteDirectory($s_templatesURI);
        }
    }

    /**
     * Adds a view access right
     */
    private function addView()
    {
        if (! $this->post->validate(array(
            'url' => 'required',
            'rights' => 'required|set:0,1,2',
            'group' => 'required|type:int|min:0',
            'view' => 'required'
        ))) {
            return;
        }
        
        $s_url = $this->prepareUrl($this->post->get('url'));
        $i_id = $this->privilegeController->addViewRight($s_url, $this->post->get('group'), $this->post->get('view'), $this->post->get('rights'));
        $group = $this->groups->getGroup($this->post->get('group'));
        
        $this->template->set('id', $i_id);
        $this->template->set('view', $this->post->get('view'));
        $this->template->set('level', $this->post->get('rights'));
        $this->template->set('deleteText', t('system/buttons/delete'));
        $this->template->set('group', $group->getName());
    }

    /**
     * Deletes the view access right
     */
    private function deleteView()
    {
        if (! $this->post->validate(array(
            'id' => 'required|type:int|min:1'
        ))) {
            return;
        }
        
        $this->privilegeController->deleteViewRight($this->post->get('id'));
    }

    /**
     * Removes the trailing slashes
     * 
     * @param string $s_url The page url
     * @return string
     */
    private function prepareUrl($s_url)
    {
        while ((substr($s_url, 0, 1) == '/') || (substr($s_url, 0, 1) == '\\')) {
            $s_url = substr($s_url, 1);
        }
        
        return $s_url;
    }

    /**
     * Resets the page access rights
     */
    private function reset()
    {
        if (! $this->post->validate(array(
            'url' => 'required'
        ))) {
            return;
        }
        
        $s_url = $this->prepareUrl($this->post->get('url'));
        $this->privilegeController->deletePageRights($s_url);
    }
}