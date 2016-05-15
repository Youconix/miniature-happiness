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
     * @param \Input $Input
     * @param \Config $config            
     * @param \Language $language            
     * @param \Output $template            
     * @param \Logger $logs
     * @param \core\services\Maintenance    $maintenance            
     */
    public function __construct(\Input $Input, \Config $config, \Language $language, \Output $template, 
        \Logger $logs,\core\services\Maintenance $maintenance)
    {
        parent::__construct($Input, $config, $language, $template, $logs);
        
        $this->maintenance = $maintenance;
    }
    
    /**
     * Inits the class Maintenance
     */
    protected function init(){
    	$this->init_post = array(
    		'action' => 'string'
    	);
    	
    	parent::init();
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {        
    	switch ($this->post->getDefault('action')) {
            case 'checkDatabase':
            case 'optimizeDatabase':
            case 'cleanStats':
                $this->performAction($this->post->get('action'));
                break;
            	
            default :
            	$this->view();
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
}