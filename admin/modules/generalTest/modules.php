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
        }
    }
    
    protected function init(){
        parent::init();
        
        $this->model_controlPanelModules = \core\Memory::models('ControlPanelModules');
    }
    
    private function index(){
        $this->service_Template->set('headerName','Module');
        $this->service_Template->set('headerAuthor','Author');
        $this->service_Template->set('headerVersion','Version');
        $this->service_Template->set('headerDescription','Description');
        
        $a_installedModules = $this->model_controlPanelModules->getInstalledModules();
        foreach( $a_installedModules AS $a_module ){
         $this->service_Template->setBlock('installedModule',array(
	            'id' => $a_module['id'],
	            'name' => $a_module['name'],
	            'author' => $a_module['author'],
	            'version' => $a_module['version'],
	            'description' => $a_module['description']
          ));   
        }
        
        $a_newModules = $this->model_controlPanelModules->getNewModules();
        if( count($a_newModules) == 0 ){
            return;
        }
        
        $this->service_Template->displayPart('availableModules');
        foreach( $a_installedModules AS $a_module ){
         $this->service_Template->setBlock('installedModule',array(
	            'id' => $a_module['id'],
	            'name' => $a_module['name'],
	            'author' => $a_module['author'],
	            'version' => $a_module['version'],
	            'description' => $a_module['description']
          ));   
        }
    }
}

$obj_Modules = new Modules;
unset($obj_Modules);