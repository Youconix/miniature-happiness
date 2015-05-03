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
 * Admin settings configuration class
 *
 * This file is part of Miniature-happiness
 *
 * @copyright Youconix
 * @author Rachelle Scheijen
 * @since 1.0
 */
if (! defined('NIV')) {
    define('NIV', '../../../');
}

include (NIV . 'admin/modules/settings/settings.php');

class Database extends \admin\Settings
{    
    /**
     * Calls the functions
     */
    protected function menu(){
        if (isset($this->get['command'])) {
            switch ($this->get['command']) {
                  case 'database':
                    $this->database();
                    break;
            }
        } else
            if (isset($this->post['command'])) {
                switch ($this->post['command']) {
                    case 'databaseCheck':
                        $this->databaseCheck();
                        break;
        
                    case 'database':
                        $this->databaseSave();
                        break;
                }
            }
    }

    /**
     * Inits the class Settings
     */
    protected function init()
    {
        $this->init_post = array(
            'prefix' => 'string',
            'type' => 'string',
            'username' => 'string',
            'password' => 'string',
            'database' => 'string',
            'host' => 'string',
            'port' => 'int',
        );
        
        parent::init();
    }
    
    /**
     * Displays the database settings
     */
    private function database()
    {
        $this->service_Template->set('databaseTitle', t('system/admin/settings/database/title'));
        $this->service_Template->set('prefixText', t('system/admin/settings/database/prefix'));
        $this->service_Template->set('prefix', $this->getValue('SQL/prefix', 'MH_'));
        $this->service_Template->set('typeText', t('system/admin/settings/database/type'));
        $s_type = $this->getValue('SQL/type');
        
        $directory = $this->service_FileHandler->readDirectory(NIV . 'core' . DIRECTORY_SEPARATOR . 'database');
        $directory = $this->service_FileHandler->directoryFilterName($directory, array(
            '*.inc.php',
            '!_binded',
            '!Database.inc.php',
            '!builder_'
        ));
        foreach ($directory as $file) {
            $s_name = str_replace('.inc.php', '', $file->getFilename());
            ($s_name == $s_type) ? $selected = 'selected="selected"' : $selected = '';
            $this->service_Template->setBlock('type', array(
                'value' => $s_name,
                'selected' => $selected,
                'text' => $s_name
            ));
        }
        
        $this->service_Template->set('usernameText', t('system/admin/settings/username'));
        $this->service_Template->set('username', $this->getValue('SQL/' . $s_type . '/username'));
        $this->service_Template->set('passwordText', t('system/admin/settings/password'));
        $this->service_Template->set('password', $this->getValue('SQL/' . $s_type . '/password'));
        $this->service_Template->set('databaseText', t('system/admin/settings/database/database'));
        $this->service_Template->set('database', $this->getValue('SQL/' . $s_type . '/database'));
        $this->service_Template->set('hostText', t('system/admin/settings/host'));
        $this->service_Template->set('host', $this->getValue('SQL/' . $s_type . '/host'));
        $this->service_Template->set('portText', t('system/admin/settings/port'));
        $this->service_Template->set('port', $this->getValue('SQL/' . $s_type . '/port', 3306));
        
        $this->service_Template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Checks the database settings
     */
    private function databaseCheck()
    {
        if (! $this->service_Validation->validate(array(
            'type' => array(
                'required' => 1
            ),
            'username' => array(
                'required' => 1
            ),
            'password' => array(
                'required' => 1
            ),
            'database' => array(
                'required' => 1
            ),
            'host' => array(
                'required' => 1
            ),
            'port' => array(
                'required' => 1,
                'type' => 'int'
            )
        ), $this->post)) {
            echo ('0');
            die();
        }
        $bo_oke = false;
        switch ($this->post['type']) {
            case 'Mysqli':
                require_once (NIV . 'core/database/Mysqli.inc.php');
                $bo_oke = \core\database\Database_Mysqli::checkLogin($this->post['username'], $this->post['password'], $this->post['database'], $this->post['host'], $this->post['port']);
                break;
            
            case 'PostgreSql':
                require_once (NIV . 'core/database/PostgreSql.inc.php');
                $bo_oke = \core\database\Database_PostgreSql::checkLogin($this->post['username'], $this->post['password'], $this->post['database'], $this->post['host'], $this->post['port']);
                break;
        }
        
        if ($bo_oke) {
            echo ('1');
        } else {
            echo ('0');
        }
        die();
    }

    /**
     * Saves the database settings
     */
    private function databaseSave()
    {
        if (! $this->service_Validation->validate(array(
            'type' => array(
                'required' => 1
            ),
            'username' => array(
                'required' => 1
            ),
            'password' => array(
                'required' => 1
            ),
            'database' => array(
                'required' => 1
            ),
            'host' => array(
                'required' => 1
            ),
            'port' => array(
                'required' => 1,
                'type' => 'int'
            )
        ), $this->post)) {
            return;
        }
        
        $s_type = $this->post['type'];
        $this->setValue('SQL/prefix', $this->post['prefix']);
        $this->setValue('SQL/type', $s_type);
        $this->setValue('SQL/' . $s_type . '/username', $this->post['username']);
        $this->setValue('SQL/' . $s_type . '/password', $this->post['password']);
        $this->setValue('SQL/' . $s_type . '/database', $this->post['database']);
        $this->setValue('SQL/' . $s_type . '/host', $this->post['host']);
        $this->setValue('SQL/' . $s_type . '/port', $this->post['port']);
        
        $this->service_Settings->save();
    }
}

$obj_Database = new Database();
unset($obj_Database);