<?php
namespace admin;

/**
 * Admin page rights configuration class
 *
 * This file is part of miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 2.0
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
 */
define('NIV', '../../../');
include (NIV . 'core/AdminLogicClass.php');

class Pages extends \core\AdminLogicClass
{

    private $model_PrivilegeController;

    /**
     * Starts the class Users
     */
    public function __construct()
    {
        $this->init();
        
        if (! \core\Memory::models('Config')->isAjax()) {
            exit();
        }
        
        if (isset($this->get['command'])) {
            if ($this->get['command'] == 'index') {
                $this->index();
            }
            if ($this->get['command'] == 'view') {
                $this->view();
            }
        }
    }

    protected function init()
    {
        $this->init_get = array(
            'url' => 'string-DB'
        );
        
        parent::init();
        
        $this->model_PrivilegeController = \Loader::Inject('\core\models\PrivilegeController');
    }

    private function index()
    {
        $a_files = $this->model_PrivilegeController->getPages();
        
        $this->service_Template->set('pageTitle', 'Pagina controllers');
        $this->service_Template->set('pages', $this->indexDir($a_files, ''));
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
        $this->service_Template->set('buttonBack', t('system/buttons/back'));
        $this->service_Template->set('buttonDelete', t('system/buttons/delete'));
        $this->service_Template->set('delete', t('system/buttons/delete'));
        $this->service_Template->set('save', t('system/buttons/save'));
        $this->service_Template->set('add', t('system/buttons/add'));
        
        $this->service_Template->set('groupLabel', 'Groep');
        $this->service_Template->set('accessLevelLabel', 'Minimaal toegangslevel');
        $this->service_Template->set('viewRightsTitle', 'View specifieke rechten (optioneel)');
    }

    private function view()
    {
        $this->viewEditText();
        
        $this->service_Template->set('pageTitle', 'Pagina rechten bewerken');
        
        $a_rights = $this->model_PrivilegeController->getRightsForPage($this->get['url']);
        $this-> setGroupList('groups', $a_rights['general']['groupID']);
        
        $this->service_Template->set('name', $a_rights['page']);
        $this->setAccessList('pageRight', $a_rights['general']['minLevel']);
        $this->setAccessList('templateRight', - 1);
        
        foreach ($a_rights['commands'] as $a_right) {
            $this->service_Template->setBlock('template_rights', array(
                'command' => $a_right['command'],
                'level' => t('system/rights/level_' . $a_right['minLevel'])
            ));
        }
    }

    private function setGroupList($s_key, $i_default)
    {
        $a_groups = \Loader::inject('\core\models\Groups')->getGroups();
        
        foreach ($a_groups as $model_Group) {
            ($model_Group->getID() == $i_default) ? $s_selected = 'selected="selected"' : $s_selected = '';
            
            $this->service_Template->setBlock($s_key, array(
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
            $this->service_Template->setBlock($s_key, array(
                'value' => $i,
                'selected' => $s_selected,
                'text' => t('system/rights/level_' . $i)
            ));
        }
    }
}

$obj_Pages = new Pages();
unset($obj_Pages);