<?php
/** 
 * Admin software updater                                                  
 *                                                                              
 * This file is part of Miniature-happiness                                    
 *                                                                              
 * @copyright Youconix                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0                                                               
 *                                                                              
 * Miniature-happiness is free software: you can redistribute it and/or modify 
 * it under the terms of the GNU Lesser General Public License as published by  
 * the Free Software Foundation, either version 3 of the License, or            
 * (at your option) any later version.                                          
 *                                                                              
 * Miniature-happiness is distributed in the hope that it will be useful,      
 * but WITHOUT ANY WARRANTY; without even the implied warranty of               
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                
 * GNU General Public License for more details.                                 
 *                                                                              
 * You should have received a copy of the GNU Lesser General Public License     
 * along with Miniature-happiness.  If not, see <http://www.gnu.org/licenses/>.
 */
define('NIV', '../');
include (NIV . 'include/AdminLogicClass.php');

class Software extends AdminLogicClass
{

    private $service_Software;

    /**
     * Starts the class Software
     */
    public function __construct()
    {
        $this->init();
        
        if (! Memory::isAjax())
            exit();
        
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->view();
        } else {
            $this->update();
        }
    }

    /**
     * Inits the class Stats
     */
    protected function init()
    {
        parent::init();
        
        $this->service_Software = Memory::services('Software');
    }

    /**
     * Displays the main view
     */
    private function view()
    {
        $i_status = $this->service_Software->checkUpdates();
        
        if ($i_status == - 1) {
            $this->service_Template->set('notice', '<span class="errorNotice">' . $this->service_Language->get('language/admin/software/noContact') . '</span>');
        } else 
            if ($i_status == 0) {
                $this->service_Template->set('notice', '<span class="Notice">' . $this->service_Language->get('language/admin/software/upToDate') . '</span>');
            } else {
                $this->service_Template->set('notice', '<a href="javascript:admin.update()" class="systemNotice">' . $this->service_Language->get('language/admin/software/updateAvailable') . '</a>');
            }
    }

    /**
     * Updates the software
     */
    private function update()
    {
        $i_status = $this->service_Software->update();
        
        if ($i_status < 0) {
            $this->service_Template->set('notice', '<span class="errorNotice">' . $this->service_Language->get('language/admin/software/updateFailure') . '</span>');
        } else 
            if ($i_status == 0) {
                $this->service_Template->set('notice', '<span class="Notice">' . $this->service_Language->get('language/admin/software/upToDate') . '</span>');
            } else {
                $this->service_Template->set('notice', '<span class="Notice">' . $this->service_Language->get('language/admin/software/updateSucceed') . '</span>');
            }
    }
}

$obj_Software = new Software();
unset($obj_Software);