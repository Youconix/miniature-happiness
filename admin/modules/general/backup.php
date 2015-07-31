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
class Backup extends \core\AdminLogicClass
{
    
    /**
     * 
     * @var \core\services\Backup
     */
    private $backup;

    /**
     * Starts the class Groups
     *
     * @param \Input $Input
     * @param \Config $config            
     * @param \Language $language            
     * @param \Output $template            
     * @param \Logger $logs
     * @param \core\services\Backup	$backup    
     */
    public function __construct(\Input $Input, \Config $config, \Language $language, \Output $template, 
        \Logger $logs,\core\services\Backup $backup)
    {
        parent::__construct($Input, $config, $language, $template, $logs);
        
        $this->backup = $backup;
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {        
        switch ($s_command) {       
        	case 'createBackupscreen' :
        		$this->createBackupscreen();
        		break;
        		
            case 'createBackup':
                $this->createBackup();
                break;
                
            case 'restoreBackup' :
            	$this->restoreBackup();
            	break;
        }
    }
    
    private function createBackupscreen(){
    	$this->template->set('moduleTitle','Create backup');
    	$this->template->set('title','Creating backup in progress');
    	$this->template->set('backupText','This screen wil automatically refresh. Please wait...');
    }

    /**
     * Creates the backup
     */
    private function createBackup()
    {
    	$s_backup = $this->backup->createBackupFull();
    
    	(is_null($s_backup)) ? $bo_result = 0 : $bo_result = 1;
    
    	$this->template->set('result', $bo_result);
    	$this->template->set('backup', $s_backup);
    }
    
    private function restoreBackup(){
    }
}