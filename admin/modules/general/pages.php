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
     * Starts the class Users
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
            if ($s_command == 'edit') {
                $this->edit();
            } else 
                if ($s_command == 'delete') {
                    $this->delete();
                }
        } else {
            if ($s_command == 'index') {
                $this->index();
            }
            if ($s_command == 'view') {
                $this->view();
            }
        }
    }

    protected function init()
    {
        $this->init_get = array(
            'url' => 'string-DB'
        );
        $this->init_post = array(
            'url' => 'string-DB',
            'rights' => 'int',
            'group' => 'int'
        );
        
        parent::init();
    }

    private function index()
    {
        $a_files = $this->privilegeController->getPages();
        
        $this->template->set('pageTitle', 'Pagina controllers');
        $this->template->set('pages', $this->indexDir($a_files, ''));
    }

    private function indexDir($a_files, $s_parent)
    {
        $s_pages = '';
        foreach ($a_files as $key => $a_data) {
            if (is_numeric($key)) {
                $s_pages .= '<li data-url="' . $a_data[0] . '" class="link">' . $a_data[1] . "</li>\n";
            } else {
                $s_pages .= '<li><span class="directory_pointer" data-url="' . $s_parent . DIRECTORY_SEPARATOR . $key . '">' . $key . '</span><ul>
          ' . $this->indexDir($a_files[$key], $s_parent . DIRECTORY_SEPARATOR . $key) . '
          </ul>';
            }
        }
        
        return $s_pages;
    }

    private function viewEditText()
    {
        $this->template->set('buttonBack', t('system/buttons/back'));
        $this->template->set('buttonDelete', t('system/buttons/delete'));
        $this->template->set('delete', t('system/buttons/delete'));
        $this->template->set('save', t('system/buttons/save'));
        $this->template->set('add', t('system/buttons/add'));
        
        $this->template->set('groupLabel', 'Groep');
        $this->template->set('accessLevelLabel', 'Minimaal toegangslevel');
        $this->template->set('viewRightsTitle', 'View specifieke rechten (optioneel)');
    }

    private function view()
    {
        $this->viewEditText();
        
        $this->template->set('pageTitle', 'Pagina rechten bewerken');
        $this->template->set('url', $this->get['url']);
        
        $a_rights = $this->privilegeController->getRightsForPage($this->get['url']);
        $this->setGroupList('groups', $a_rights['general']['groupID']);
        
        $this->template->set('name', $a_rights['page']);
        $this->setAccessList('pageRight', $a_rights['general']['minLevel']);
        $this->setAccessList('templateRight', - 1);
        
        foreach ($a_rights['commands'] as $a_right) {
            $this->template->setBlock('template_rights', array(
                'command' => $a_right['command'],
                'level' => t('system/rights/level_' . $a_right['minLevel'])
            ));
        }
    }

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

    private function setAccessList($s_key, $i_default)
    {
        for ($i = - 1; $i <= 2; $i ++) {
            ($i == $i_default) ? $s_selected = 'selected="selected"' : $s_selected = '';
            $this->template->setBlock($s_key, array(
                'value' => $i,
                'selected' => $s_selected,
                'text' => t('system/rights/level_' . $i)
            ));
        }
    }

    private function edit()
    {
        if (! $this->post->validate(array(
            'url' => 'required',
            'rights' => 'required|set:-1,0,1,2',
            'group' => 'required|min:0'
        ))) {
            return;
        }
        
        /* Check group */
        $a_groups = $this->groups->getGroups();
        if (! array_key_exists($this->post['group'], $a_groups)) {
            return;
        }
        
        $this->privilegeController->changePageRights($this->post['url'], $this->post['rights'], $this->post['group']);
    }

    /**
     * Delete the page.
     */
    private function delete()
    {
        $s_fileURI = NIV . $this->post['url'];
        $s_templatesURI = $this->config->getStylesDir() . str_replace('.php', '', $this->post['url']);
        
        $this->privilegeController->deletePageRights($this->post['url']);
        if ($this->file->exists($s_fileURI)) {
            $this->file->deleteFile(NIV . $this->post['url']);
        }
        if ($this->file->exists($s_templatesURI)) {
            $this->file->deleteDirectory($s_templatesURI);
        }
    }
}

$obj_Pages = new Pages();
unset($obj_Pages);