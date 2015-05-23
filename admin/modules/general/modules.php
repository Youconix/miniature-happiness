<?php
namespace admin;

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
define('NIV', '../../../');
include (NIV . 'core/AdminLogicClass.php');

class Modules extends \core\AdminLogicClass
{

    private $model_controlPanelModules;

    /**
     * Starts the class Modules
     */
    public function __construct()
    {
        $this->init();
        
        if (! $this->model_Config->isAjax()) {
            exit();
        }
        
        if (isset($this->get['command'])) {
            if ($this->get['command'] == 'index') {
                $this->index();
            }
        } else 
            if (isset($this->post['command'])) {
                if ($this->post['command'] == 'delete') {
                    $this->delete();
                } else 
                    if ($this->post['command'] == 'upgrade') {
                        $this->upgrade();
                    } else 
                        if ($this->post['command'] == 'install') {
                            $this->install();
                        }
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
        
        $this->model_controlPanelModules = \Loader::inject('\core\models\ControlPanelModules');
    }

    /**
     * Displays the modules
     */
    private function index()
    {
        $this->service_Template->set('moduleTitle', t('system/admin/modules/moduleTitle'));
        $this->service_Template->set('headerName', t('system/admin/modules/headerName'));
        $this->service_Template->set('headerAuthor', t('system/admin/modules/headerAuthor'));
        $this->service_Template->set('headerVersion', t('system/admin/modules/headerVersion'));
        $this->service_Template->set('headerDescription', t('system/admin/modules/headerDescription'));
        
        $a_installedModules = $this->model_controlPanelModules->getInstalledModules();
        $this->installedModules($a_installedModules['installed']);
        
        if (count($a_installedModules['upgrades']) > 0) {
            $this->upgradableModules($a_installedModules['upgrades']);
        }
        
        $a_newModules = $this->model_controlPanelModules->getNewModules();
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
        $this->service_Template->set('installedModulesTitle', t('system/admin/modules/installedModulesTitle'));
        
        foreach ($a_modules as $a_module) {
            $this->service_Template->setBlock('installedModule', array(
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
        $this->service_Template->displayPart('upgradableModules');
        $this->service_Template->set('upgradableModulesTitle', t('system/admin/modules/upgradableModulesTitle'));
        $this->service_Template->set('headerVersionAvaiable', t('system/admin/modules/headerVersionAvaiable'));
        
        foreach ($a_modules as $a_module) {
            $this->service_Template->setBlock('upgradeModule', array(
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
        $this->service_Template->displayPart('availableModules');
        $this->service_Template->set('newModulesTitle', t('system/admin/modules/newModulesTitle'));
        
        foreach ($a_modules as $a_module) {
            $this->service_Template->setBlock('newModule', array(
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
        if (! array_key_exists('id', $this->post) || $this->post['id'] <= 0) {
            return;
        }
        
        try {
            $this->model_controlPanelModules->removeModule($this->post['id']);
        } catch (\Exception $e) {
            $this->service_Logs->exception($e);
        }
    }

    /**
     * Upgrades the given module
     */
    private function upgrade()
    {
        if (! array_key_exists('id', $this->post) || $this->post['id'] <= 0) {
            return;
        }
        
        try {
            $this->model_controlPanelModules->updateModule($this->post['id']);
        } catch (\Exception $e) {
            $this->service_Logs->exception($e);
        }
    }

    /**
     * Installs the given module
     */
    private function install()
    {
        if (! empty($this->post['name'])) {
            try {
                $this->model_controlPanelModules->installModule($this->post['name']);
            } catch (\Exception $e) {
                $this->service_Logs->exception($e);
            }
        }
    }
}

$obj_Modules = new Modules();
unset($obj_Modules);