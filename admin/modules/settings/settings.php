<?php
namespace admin;

/**
 * Admin settings configuration class
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 1.0
 *
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
if (! defined('NIV')) {
    define('NIV', '../');
}

include (NIV . 'core/AdminLogicClass.php');

class Settings extends \core\AdminLogicClass
{

    private $service_XmlSettings;

    private $obj_settingsMain;

    /**
     * Inits the class Settings
     */
    protected function init()
    {
        $this->init_post = array(
            'base' => 'string',
            'url' => 'string',
            'timezone' => 'string',
            'sessionName' => 'string',
            'sessionPath' => 'string',
            'sessionExpire' => 'int',
            'language' => 'string',
            'template' => 'string',
            'sqlUsername' => 'string',
            'sqlPassword' => 'string',
            'sqlDatabase' => 'string',
            'sqlHost' => 'string',
            'sqlPort' => 'int',
            'databaseType' => 'string'
        );
        
        parent::init();
        
        require_once (NIV . 'admin/SettingsMain.php');
        $this->obj_settingsMain = new \settingsMain();
        
        $this->service_XmlSettings = \core\Memory::services('XmlSettings');
    }

    /**
     * Generates the settings view
     */
    protected function index()
    {
        $this->service_Template->set('settingsTitle', $this->service_Language->get('admin/settings/title'));
        $this->service_Template->set('basedir', $this->service_Language->get('admin/settings/basedir'));
        $this->service_Template->set('base', $this->service_XmlSettings->get('settings/main/base'));
        
        $this->service_Template->set('siteUrl', $this->service_Language->get('admin/settings/siteUrl'));
        $this->service_Template->set('url', $this->service_XmlSettings->get('settings/main/url'));
        
        $this->service_Template->set('timezoneText', $this->service_Language->get('admin/settings/timezone'));
        $this->service_Template->set('timezone', $this->service_XmlSettings->get('settings/main/timeZone'));
        
        $this->service_Template->set('sessionTitle', $this->service_Language->get('admin/settings/sessionTitle'));
        $this->service_Template->set('sessionNameText', $this->service_Language->get('admin/settings/sessionName'));
        $this->service_Template->set('sessionName', $this->service_XmlSettings->get('settings/session/sessionName'));
        
        $this->service_Template->set('sessionPathText', $this->service_Language->get('admin/settings/sessionPath'));
        $this->service_Template->set('sessionPath', $this->service_XmlSettings->get('settings/session/sessionPath'));
        
        $this->service_Template->set('sessionExpireText', $this->service_Language->get('admin/settings/sessionExpire'));
        $this->service_Template->set('sessionExpire', $this->service_XmlSettings->get('settings/session/sessionExpire'));
        
        $this->service_Template->set('siteSettings', $this->service_Language->get('admin/settings/siteSettings'));
        $this->service_Template->set('defaultLanguage', $this->service_Language->get('admin/settings/defaultLanguage'));
        $this->service_Template->set('posibleLanguages', $this->obj_settingsMain->generateList($this->obj_settingsMain->getLanguages(), $this->service_XmlSettings->get('settings/defaultLanguage')));
        
        $this->service_Template->set('templateDir', $this->service_Language->get('admin/settings/templateDir'));
        $this->service_Template->set('templates', $this->obj_settingsMain->generateList($this->obj_settingsMain->getTemplates(), $this->service_XmlSettings->get('settings/templates/dir')));
        
        $this->service_Template->set('databaseSettings', $this->service_Language->get('admin/settings/databaseSettings'));
        $this->service_Template->set('username', $this->service_Language->get('admin/settings/username'));
        $this->service_Template->set('password', $this->service_Language->get('admin/settings/password'));
        $this->service_Template->set('database', $this->service_Language->get('admin/settings/database'));
        $this->service_Template->set('host', $this->service_Language->get('admin/settings/host'));
        $this->service_Template->set('port', $this->service_Language->get('admin/settings/port'));
        $this->service_Template->set('type', $this->service_Language->get('admin/settings/type'));
        $this->service_Template->set('disable', $this->service_Language->get('admin/settings/disable'));
        
        /* SQL */
        $s_type = $this->service_XmlSettings->get('settings/SQL/type');
        $this->service_Template->set('sqlUsername', $this->service_XmlSettings->get('settings/SQL/' . $s_type . '/username'));
        $this->service_Template->set('sqlPassword', $this->service_XmlSettings->get('settings/SQL/' . $s_type . '/password'));
        $this->service_Template->set('sqlDatabase', $this->service_XmlSettings->get('settings/SQL/' . $s_type . '/database'));
        $this->service_Template->set('sqlHost', $this->service_XmlSettings->get('settings/SQL/' . $s_type . '/host'));
        $this->service_Template->set('sqlPort', $this->service_XmlSettings->get('settings/SQL/' . $s_type . '/port'));
        $this->service_Template->set('databases', $this->obj_settingsMain->generateList($this->obj_settingsMain->getDatabases(), $s_type));
        
        $this->service_Template->set('buttonSave', $this->service_Language->get('buttons/save'));
    }

    /**
     * Checks the SQL login data
     */
    private function checkSQL()
    {
        $a_data = array(
            'sqlUsername' => $this->post['sqlUsername'],
            'sqlPassword' => $this->post['sqlPassword'],
            'sqlDatabase' => $this->post['sqlDatabase'],
            'sqlHost' => $this->post['sqlHost'],
            'sqlPort' => $this->post['sqlPort'],
            'databaseType' => $this->post['databaseType']
        );
        
        if ($a_data['sqlPort'] == 0)
            $a_data['sqlPort'] = - 1;
        
        if (! $this->obj_settingsMain->checkDatabase($a_data)) {
            $this->service_Template->set('code', 0);
        } else {
            $this->service_Template->set('code', 1);
        }
    }

    /**
     * Saves the settings
     */
    private function save()
    {
        $this->service_XmlSettings->set('settings/main/base', $this->post['base']);
        $this->service_XmlSettings->set('settings/main/url', $this->post['url']);
        $this->service_XmlSettings->set('settings/main/timeZone', $this->post['timezone']);
        $this->service_XmlSettings->set('settings/session/sessionName', $this->post['sessionName']);
        $this->service_XmlSettings->set('settings/session/sessionPath', $this->post['sessionPath']);
        if ($this->post['sessionExpire'] != 0) {
            $this->service_XmlSettings->set('settings/session/sessionExpire', $this->post['sessionExpire']);
        } else {
            $this->service_XmlSettings->set('settings/session/sessionExpire', '');
        }
        $this->service_XmlSettings->set('settings/defaultLanguage', $this->post['language']);
        $this->service_XmlSettings->set('settings/templates/dir', $this->post['template']);
        
        /* SQL */
        $s_type = $this->post['databaseType'];
        $this->service_XmlSettings->set('settings/SQL/type', $s_type);
        $this->service_XmlSettings->set('settings/SQL/' . $s_type . '/username', $this->post['sqlUsername']);
        $this->service_XmlSettings->set('settings/SQL/' . $s_type . '/password', $this->post['sqlPassword']);
        $this->service_XmlSettings->set('settings/SQL/' . $s_type . '/database', $this->post['sqlDatabase']);
        $this->service_XmlSettings->get('settings/SQL/' . $s_type . '/host', $this->post['sqlHost']);
        if ($this->post['sqlPort'] == 0) {
            $this->service_XmlSettings->set('settings/SQL/' . $s_type . '/port', '');
        } else {
            $this->service_XmlSettings->set('settings/SQL/' . $s_type . '/port', $this->post['sqlPort']);
        }
        
        $this->service_XmlSettings->save();
    }
}

$obj_Settings = new Settings();
unset($obj_Settings);
