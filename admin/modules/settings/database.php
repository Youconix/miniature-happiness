<?php
namespace admin\modules\settings;

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
class Database extends \admin\modules\settings\Settings
{

    /**
     *
     * @var \core\services\FileHandler
     */
    private $fileHandler;

    /**
     * Constructor
     *
     * @param \Input $Input            
     * @param \Config $config            
     * @param \Language $language            
     * @param \Output $template            
     * @param \Logger $logs            
     * @param \Settings $settings            
     * @param \core\services\FileHandler $fileHandler            
     */
    public function __construct(\Input $Input, \Config $config, \Language $language, \Output $template, \Logger $logs, \Settings $settings, \core\services\FileHandler $fileHandler)
    {
        parent::__construct($Input, $config, $language, $template, $logs, $settings);
        
        $this->fileHandler = $fileHandler;
    }

    /**
     * Routes the controller
     *
     * @see Routable::route()
     */
    public function route($s_command)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->database();
        } else {
            switch ($s_command) {
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
            'port' => 'int'
        );
        
        parent::init();
    }

    /**
     * Displays the database settings
     */
    private function database()
    {
        $this->template->set('databaseTitle', t('system/admin/settings/database/title'));
        $this->template->set('prefixText', t('system/admin/settings/database/prefix'));
        $this->template->set('prefix', $this->getValue('SQL/prefix', 'MH_'));
        $this->template->set('typeText', t('system/admin/settings/database/type'));
        $s_type = $this->getValue('SQL/type');
        
        $directory = $this->fileHandler->readDirectory(NIV . 'core' . DIRECTORY_SEPARATOR . 'database');
        $directory = $this->fileHandler->directoryFilterName($directory, array(
            '*.inc.php',
            '!_binded',
            '!Database.inc.php',
            '!builder_'
        ));
        foreach ($directory as $file) {
            $s_name = str_replace('.inc.php', '', $file->getFilename());
            ($s_name == $s_type) ? $selected = 'selected="selected"' : $selected = '';
            $this->template->setBlock('type', array(
                'value' => $s_name,
                'selected' => $selected,
                'text' => $s_name
            ));
        }
        
        $this->template->set('usernameText', t('system/admin/settings/username'));
        $this->template->set('username', $this->getValue('SQL/' . $s_type . '/username'));
        $this->template->set('passwordText', t('system/admin/settings/password'));
        $this->template->set('password', $this->getValue('SQL/' . $s_type . '/password'));
        $this->template->set('databaseText', t('system/admin/settings/database/database'));
        $this->template->set('database', $this->getValue('SQL/' . $s_type . '/database'));
        $this->template->set('hostText', t('system/admin/settings/host'));
        $this->template->set('host', $this->getValue('SQL/' . $s_type . '/host'));
        $this->template->set('portText', t('system/admin/settings/port'));
        $this->template->set('port', $this->getValue('SQL/' . $s_type . '/port', 3306));
        
        $this->template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Checks the database settings
     */
    private function databaseCheck()
    {
        if (! $this->post->validate(array(
            'type' => 'required',
            'username' => 'required',
            'password' => 'required',
            'database' => 'required',
            'host' => 'required',
            'port' => 'required|type:port'
        ))) {
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
        if (! $this->post->validate(array(
            'type' => 'required',
            'username' => 'required',
            'password' => 'required',
            'database' => 'required',
            'host' => 'required',
            'port' => 'required|type:port'
        ))) {
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