<?php
namespace admin;

/**
 * Admin log management class
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 1.0
 *        @changed 25/09/10
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
define('NIV', '../');
include (NIV . 'include/AdminLogicClass.php');

class Logs extends \core\AdminLogicClass
{

    private $service_Logs;

    private $s_type;

    private $s_date;

    /**
     * PHP 5 constructor
     */
    public function __construct()
    {
        $this->init();
        
        if (! Memory::isAjax()) {
            exit();
        }
        
        if (isset($this->get['command'])) {
            if ($this->get['command'] == 'index') {
                $this->index();
            } else 
                if ($this->get['command'] == 'view') {
                    $this->view();
                } else 
                    if ($this->get['command'] == 'download') {
                        $this->download();
                    }
        } else 
            if (isset($this->post['command'])) {
                if ($this->post['command'] == 'delete') {
                    $this->delete();
                }
            }
    }

    /**
     * Inits the class Logs
     */
    protected function init()
    {
        $this->init_get = array(
            'name' => 'string'
        );
        
        $this->init_post = array(
            'name' => 'string'
        );
        
        parent::init();
        
        $this->service_Logs = Memory::services('Logs');
    }

    /**
     * Displays the index view of the logs
     */
    private function index()
    {
        $a_logsPre = $this->service_Logs->indexLogs();
        
        $this->service_Template->set('logHeader', $this->service_Language->get('admin/logs/title'));
        $this->service_Template->set('nameHeader', $this->service_Language->get('admin/logs/name'));
        $this->service_Template->set('dateHeader', $this->service_Language->get('admin/logs/date'));
        
        $a_logs = array();
        foreach ($a_logsPre as $s_log) {
            if (substr($s_log, 0, 1) == '.')
                continue;
            
            $s_type = substr($s_log, 0, strpos($s_log, '_'));
            $s_date = substr($s_log, (strpos($s_log, '_') + 1), strlen($s_log));
            $s_date = substr($s_date, 0, 4) . '-' . substr($s_date, 4, 2);
            
            if (! array_key_exists($s_type, $a_logs)) {
                $a_logs[$s_type] = array();
            }
            
            $a_logs[$s_type][$s_date] = array(
                'log' => $s_log,
                'date' => $s_date
            );
        }
        
        $a_types = array_keys($a_logs);
        foreach ($a_types as $s_type) {
            $this->service_Template->setBlock('log', array(
                'logtype' => $s_type
            ));
            
            $a_keys = array_keys($a_logs[$s_type]);
            rsort($a_keys, SORT_STRING);
            
            foreach ($a_keys as $s_key) {
                $a_data = array(
                    'name' => $a_logs[$s_type][$s_key]['log'],
                    'logDate' => $a_logs[$s_type][$s_key]['date']
                );
                
                $this->service_Template->setBlock($s_type, $a_data);
            }
        }
        
        $this->service_Template->set('viewText', $this->service_Language->get('buttons/view'));
        $this->service_Template->set('deleteText', $this->service_Language->get('buttons/delete'));
        $this->service_Template->set('downloadText', $this->service_Language->get('admin/logs/download'));
    }

    /**
     * Displays the content of the given log
     */
    private function view()
    {
        if (! $this->checkLog($this->get['name'])) {
            header('Status: 400 Bad Request');
            exit();
        }
        
        $s_log = $this->service_Logs->readLog($this->s_type, $this->s_date);
        $s_date = substr($this->s_date, 0, 4) . '-' . substr($this->s_date, 4, 2);
        
        $this->service_Template->set('logHeader', $this->get['name']);
        $this->service_Template->set('name', $this->get['name']);
        $this->service_Template->set('log', nl2br($s_log));
        
        $this->service_Template->set('backText', $this->service_Language->get('buttons/back'));
        $this->service_Template->set('viewText', $this->service_Language->get('buttons/view'));
        $this->service_Template->set('deleteText', $this->service_Language->get('buttons/delete'));
        $this->service_Template->set('downloadText', $this->service_Language->get('admin/logs/download'));
    }

    /**
     * Gives the log as a forced download
     */
    private function download()
    {
        if (! $this->checkLog($this->get['name'])) {
            header('Status: 400 Bad Request');
            exit();
        }
        
        $this->service_Logs->downloadLog($this->s_type, $this->s_date);
        die();
    }

    /**
     * Deletes the given log
     */
    private function delete()
    {
        if (! $this->checkLog($this->post['name'])) {
            header('Status: 400 Bad Request');
            exit();
        }
        
        $this->service_Logs->deleteLog($this->s_type, $this->s_date);
    }

    /**
     * Checks if the log exists
     *
     * @param string $s_logName
     *            The name of the log
     * @return boolean True if the log exists, otherwise false
     */
    private function checkLog($s_logName)
    {
        /* Check log */
        $a_log = explode('_', $s_logName);
        if (count($a_log) == 1) {
            return false;
        }
        
        $this->s_type = $a_log[0];
        $this->s_date = str_replace('.log', '', $a_log[1]);
        if (! $this->service_Logs->checkLog($this->s_type, $this->s_date)) {
            return false;
        }
        
        return true;
    }
}

$obj_logs = new Logs();
unset($obj_logs);
?>