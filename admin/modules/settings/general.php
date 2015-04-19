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

class General extends \admin\Settings
{    
    /**
     * Calls the functions
     */
    protected function menu(){
        if (isset($this->get['command'])) {
            switch ($this->get['command']) {        
                case 'general':
                    $this->general();
                    break;
                    
                case 'ssl' :
                    $this->ssl();
                    break;
            }
        } else
            if (isset($this->post['command'])) {
                switch ($this->post['command']) {        
                    case 'general':
                        $this->generalSave();
                        break;
                        
                    case 'ssl' :
                        $this->sslSave();
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
            'name_site' => 'string',
            'site_url' => 'string',
            'site_base' => 'string',
            'timezone' => 'string',
            'template' => 'string',
            'logger' => 'string',
            'log_location' => 'string',
            'log_size' => 'int',
        );
        
        parent::init();
    }

    /**
     * Displays the general settings
     */
    private function general()
    {
        $this->service_Template->set('generalTitle', t('system/admin/settings/general/title'));
        
        $this->service_Template->set('nameSiteText', t('system/admin/settings/general/nameSite'));
        $this->service_Template->set('nameSite', $this->getValue('main/nameSite'));
        $this->service_Template->set('nameSiteError', t('system/admin/settings/general/siteNameEmpty'));
        $this->service_Template->set('siteUrlText', t('system/admin/settings/general/siteUrl'));
        $this->service_Template->set('siteUrl', $this->getValue('main/url'));
        $this->service_Template->set('siteUrlError', t('system/admin/settings/general/urlEmpty'));
        $this->service_Template->set('siteBaseText', t('system/admin/settings/general/basedir'));
        $this->service_Template->set('siteBase', $this->getValue('main/base'));
        $this->service_Template->set('timezoneText', t('system/admin/settings/general/timezone'));
        $this->service_Template->set('timezone', $this->getValue('main/timeZone'));
        $this->service_Template->set('timezoneError', t('system/admin/settings/general/timezoneInvalid'));
        
        /* Templates */
        $this->service_Template->set('templatesHeader', t('system/admin/settings/general/templates'));
        $this->service_Template->set('templateText', 'Template set');
        $s_template = $this->getValue('templates/dir', 'default');
        
        $directory = $this->service_FileHandler->readDirectory(NIV . 'styles');
        $directory = $this->service_FileHandler->directoryFilterName($directory, array(
            '!.',
            '!..'
        ));
        $templates = new \core\classes\OnlyDirectoryFilterIteractor($directory);
        
        foreach ($templates as $dir) {
            ($dir->getFilename() == $s_template) ? $selected = 'selected="selected"' : $selected = '';
            
            $this->service_Template->setBlock('template', array(
                'value' => $dir->getFilename(),
                'selected' => $selected,
                'text' => $dir->getFilename()
            ));
        }
        
        /* Logs */
        $this->service_Template->set('loggerText', t('system/admin/settings/general/logger') . ' (\Psr\Log\LoggerInterface)');
        $this->service_Template->set('logger', $this->getValue('main/logs', 'default'));
        $this->service_Template->set('loggerError', t('system/admin/settings/general/loggerInvalid'));
        if ($this->getValue('main/logs', 'default') != 'default') {
            $this->service_Template->set('location_log_default', 'style="display:none"');
        }
        
        $this->service_Template->set('logLocationText', t('system/admin/settings/general/logLocation'));
        $this->service_Template->set('logLocation', $this->getValue('main/log_location', str_replace(array(
            '../',
            './'
        ), array(
            '',
            ''
        ), DATA_DIR) . 'logs' . DIRECTORY_SEPARATOR));
        $this->service_Template->set('logLocationError', t('system/admin/settings/general/logLocationInvalid'));
        $this->service_Template->set('logSizeText', t('system/admin/settings/general/logSize'));
        $model_Config = $this->model_Config;
        $this->service_Template->set('logSize', $this->getValue('main/log_max_size', $model_Config::LOG_MAX_SIZE));
        $this->service_Template->set('logSizeError', t('system/admin/settings/general/logSizeInvalid'));
        
        $this->service_Template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Saves the general settings
     */
    private function generalSave()
    {
        if (! $this->service_Validation->validate(array(
            'name_site' => array(
                'required' => 1
            ),
            'site_url' => array(
                'required' => 1,
                'pattern' => 'url'
            ),
            "timezone" => array(
                'required' => 1,
                'pattern' => '#^[a-zA-Z]+/[a-zA-Z]+$#'
            ),
            "template" => array(
                'required' => 1
            ),
            'logger' => array(
                'required' => 1
            ),
            'log_location' => array(
                'type' => 'string'
            ),
            'log_size' => array(
                'required' => 1,
                'type' => 'int',
                'min-value' => 1000
            )
        ), $this->post)) {
            return;
        }
        
        if ($this->post['logger'] == 'default' && empty($this->post['log_location'])) {
            return;
        }
        
        $this->setValue('main/nameSite', $this->post['name_site']);
        $this->setValue('main/url', $this->post['site_url']);
        $this->setValue('main/base', $this->post['site_base']);
        $this->setValue('main/timeZone', $this->post['timezone']);
        $this->setValue('templates/dir', $this->post['template']);
        $this->setValue('main/logs', $this->post['logger']);
        $this->setValue('main/log_location', str_replace(DATA_DIR, '', $this->post['log_location']));
        $this->setValue('main/log_max_size', $this->post['log_size']);
        
        $this->service_Settings->save();
    }
    
    private function ssl(){
        
    }
    
    private function sslSave(){
        
    }
}

$obj_General = new General();
unset($obj_General);