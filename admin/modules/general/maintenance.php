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
 * Admin maintenance class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
define('NIV', '../../../');

include (NIV . 'core/AdminLogicClass.php');

class Maintenance extends \core\AdminLogicClass
{

    private $service_Maintenance;

    /**
     * Starts the class Groups
     */
    public function __construct()
    {
        $this->init();
        
        if (! $this->model_Config->isAjax()) {
            exit();
        }
        
        if (isset($this->get['action'])) {
            switch ($this->get['action']) {
                default:
                    $this->view();
                    break;
            }
        } else 
            if (isset($this->post['action'])) {
                $this->performAction();
            } else 
                if (isset($this->post['command'])) {
                    switch ($this->post['command']) {
                        case 'createBackup':
                            $this->createBackup();
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
            'action' => 'string'
        );
        $this->init_post = array(
            'action' => 'string'
        );
        
        parent::init();
        
        $this->service_Maintenance = \core\Memory::services('Maintenance');
    }

    /**
     * Generates the action menu
     */
    private function view()
    {
        $this->service_Template->set('moduleTitle', t('system/admin/general/maintenance'));
        $this->service_Template->set('checkDatabase', t('system/admin/maintenance/checkDatabase'));
        $this->service_Template->set('optimizeDatabase', t('system/admin/maintenance/optimizeDatabase'));
        $this->service_Template->set('cleanStatsYear', t('system/admin/maintenance/stats'));
        $this->service_Template->set('backup', t('system/admin/maintenance/createBackup'));
        $this->service_Template->set('ready', t('system/admin/maintenance/ready'));
    }

    /**
     * Performs the maintenance action
     */
    private function performAction()
    {
        $bo_result = false;
        
        switch ($this->post['action']) {
            case 'css':
                $bo_result = $this->service_Maintenance->compressCSS();
                break;
            
            case 'js':
                $bo_result = $this->service_Maintenance->compressJS();
                break;
            
            case 'checkDatabase':
                $bo_result = $this->service_Maintenance->checkDatabase();
                break;
            
            case 'optimizeDatabase':
                $bo_result = $this->service_Maintenance->optimizeDatabase();
                break;
            
            case 'cleanStats':
                $bo_result = $this->service_Maintenance->cleanStatsYear();
                break;
        }
        
        if ($bo_result) {
            $this->service_Template->set('result', 1);
        } else {
            $this->service_Template->set('result', 0);
        }
    }

    /**
     * Creates the backup
     */
    private function createBackup()
    {
        $s_backup = $this->service_Maintenance->createBackup();
        
        (is_null($s_backup)) ? $bo_result = 0 : $bo_result = 1;
        
        $this->service_Template->set('result', $bo_result);
        $this->service_Template->set('backup', $s_backup);
    }
}

$obj_Maintenance = new Maintenance();
unset($obj_Maintenance);