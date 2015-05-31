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
 * Admin maintenance class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
class Maintenance extends \core\AdminLogicClass
{

    /**
     * 
     * @var \core\services\Maintenance
     */
    private $maintenance;

    /**
     * Starts the class Groups
     *
     * @param \core\Input $Input
     * @param \core\models\Config $config            
     * @param \core\services\Language $language            
     * @param \core\services\Template $template            
     * @param \core\services\Logs $logs
     * @param \core\services\Maintenance    $maintenance            
     */
    public function __construct(\core\Input $Input, \core\models\Config $config, \core\services\Language $language, \core\services\Template $template, 
        \core\services\Logs $logs,\core\services\Maintenance $maintenance)
    {
        parent::__construct($Input, $config, $language, $template, $logs);
        
        $this->maintenance = $maintenance;
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->view();
            return;
        }
        
        switch ($s_command) {
            case 'css':
            case 'js':
            case 'checkDatabase':
            case 'optimizeDatabase':
            case 'cleanStats':
                $this->performAction($s_command);
                break;
            
            case 'createBackup':
                $this->createBackup();
                break;
        }
    }

    /**
     * Generates the action menu
     */
    private function view()
    {
        $this->template->set('moduleTitle', t('system/admin/general/maintenance'));
        $this->template->set('checkDatabase', t('system/admin/maintenance/checkDatabase'));
        $this->template->set('optimizeDatabase', t('system/admin/maintenance/optimizeDatabase'));
        $this->template->set('cleanStatsYear', t('system/admin/maintenance/stats'));
        $this->template->set('backup', t('system/admin/maintenance/createBackup'));
        $this->template->set('ready', t('system/admin/maintenance/ready'));
    }

    /**
     * Performs the maintenance action
     *
     * @param string $s_action
     *            The action to take
     */
    private function performAction($s_action)
    {
        $bo_result = false;
        
        switch ($s_action) {
            case 'css':
                $bo_result = $this->maintenance->compressCSS();
                break;
            
            case 'js':
                $bo_result = $this->maintenance->compressJS();
                break;
            
            case 'checkDatabase':
                $bo_result = $this->maintenance->checkDatabase();
                break;
            
            case 'optimizeDatabase':
                $bo_result = $this->maintenance->optimizeDatabase();
                break;
            
            case 'cleanStats':
                $bo_result = $this->maintenance->cleanStatsYear();
                break;
        }
        
        if ($bo_result) {
            $this->template->set('result', 1);
        } else {
            $this->template->set('result', 0);
        }
    }

    /**
     * Creates the backup
     */
    private function createBackup()
    {
        $s_backup = $this->maintenance->createBackup();
        
        (is_null($s_backup)) ? $bo_result = 0 : $bo_result = 1;
        
        $this->template->set('result', $bo_result);
        $this->template->set('backup', $s_backup);
    }
}

$obj_Maintenance = new Maintenance();
unset($obj_Maintenance);