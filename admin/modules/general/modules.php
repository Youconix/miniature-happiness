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
 * Admin page module management page
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 2.0
 */
class Modules extends \core\AdminLogicClass
{

    /**
     *
     * @var \core\models\ControlPanelModules
     */
    private $controlPanelModules;

    /**
     * Starts the class Modules
     *
     * @param \Input $Input            
     * @param \Config $config            
     * @param \Language $language            
     * @param \Output $template            
     * @param \Logger $logs            
     * @param \core\models\ControlPanelModules $controlPanelModules            
     */
    public function __construct(\Input $Input, \Config $config, \Language $language, \Output $template, \Logger $logs, \core\models\ControlPanelModules $controlPanelModules)
    {
        parent::__construct($Input, $config, $language, $template, $logs);
        
        $this->controlPanelModules = $controlPanelModules;
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->index();
        }
        
        switch ($s_command) {
            case 'delete':
                $this->delete();
                break;
            
            case 'upgrade':
                $this->upgrade();
                break;
            case 'install':
                $this->install();
                break;
        }
    }

    /**
     * Inits the class Modules
     *
     * @see \core\AdminLogicClass::init()
     */
    protected function init()
    {
        $this->init_post = array(
            'id' => 'int',
            'name' => 'string-DB'
        );
        
        parent::init();
    }

    /**
     * Displays the modules
     */
    private function index()
    {
        $this->template->set('moduleTitle', t('system/admin/modules/moduleTitle'));
        $this->template->set('headerName', t('system/admin/modules/headerName'));
        $this->template->set('headerAuthor', t('system/admin/modules/headerAuthor'));
        $this->template->set('headerVersion', t('system/admin/modules/headerVersion'));
        $this->template->set('headerDescription', t('system/admin/modules/headerDescription'));
        
        $a_installedModules = $this->controlPanelModules->getInstalledModules();
        $this->installedModules($a_installedModules['installed']);
        
        if (count($a_installedModules['upgrades']) > 0) {
            $this->upgradableModules($a_installedModules['upgrades']);
        }
        
        $a_newModules = $this->controlPanelModules->getNewModules();
        if (count($a_newModules) > 0) {
            $this->newModules($a_newModules);
        }
    }

    /**
     * Displays the installed modules
     *
     * @param array $a_modules
     *            The modules
     */
    private function installedModules($a_modules)
    {
        $this->template->set('installedModulesTitle', t('system/admin/modules/installedModulesTitle'));
        
        foreach ($a_modules as $a_module) {
            $this->template->setBlock('installedModule', array(
                'id' => $a_module['id'],
                'name' => $a_module['name'],
                'author' => $a_module['author'],
                'version' => $a_module['version'],
                'description' => $a_module['description']
            ));
        }
    }

    /**
     * Displays the upgradable modules
     *
     * @param array $a_modules
     *            The modules
     */
    private function upgradableModules($a_modules)
    {
        $this->template->displayPart('upgradableModules');
        $this->template->set('upgradableModulesTitle', t('system/admin/modules/upgradableModulesTitle'));
        $this->template->set('headerVersionAvaiable', t('system/admin/modules/headerVersionAvaiable'));
        
        foreach ($a_modules as $a_module) {
            $this->template->setBlock('upgradeModule', array(
                'id' => $a_module['id'],
                'name' => $a_module['name'],
                'author' => $a_module['author'],
                'version' => $a_module['version'],
                'versionNew' => $a_module['versionNew'],
                'description' => $a_module['description']
            ));
        }
    }

    /**
     * Displays the not installed modules
     *
     * @param array $a_modules
     *            The modules
     */
    private function newModules($a_modules)
    {
        $this->template->displayPart('availableModules');
        $this->template->set('newModulesTitle', t('system/admin/modules/newModulesTitle'));
        
        foreach ($a_modules as $a_module) {
            $this->template->setBlock('newModule', array(
                'name' => $a_module['name'],
                'author' => $a_module['author'],
                'version' => $a_module['version'],
                'description' => $a_module['description']
            ));
        }
    }

    /**
     * Deletes the given module
     */
    private function delete()
    {
        if (! $this->post->validate(array(
            'id' => 'required|min:1'
        ))) {
            return;
        }
        
        try {
            $this->controlPanelModules->removeModule($this->post['id']);
        } catch (\Exception $e) {
            $this->logs->exception($e);
        }
    }

    /**
     * Upgrades the given module
     */
    private function upgrade()
    {
        if (! $this->post->validate(array(
            'id' => 'required|min:1'
        ))) {
            return;
        }
        
        try {
            $this->controlPanelModules->updateModule($this->post['id']);
        } catch (\Exception $e) {
            $this->logs->exception($e);
        }
    }

    /**
     * Installs the given module
     */
    private function install()
    {
        if (! $this->post->validate(array(
            'name' => 'required'
        ))) {
            return;
        }
        
        try {
            $this->controlPanelModules->installModule($this->post['name']);
        } catch (\Exception $e) {
            $this->logs->exception($e);
        }
    }
}