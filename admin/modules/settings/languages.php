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

class Languages extends \admin\Settings
{    
    /**
     * Calls the functions
     */
    protected function menu(){
        if (isset($this->get['command'])) {
            switch ($this->get['command']) {        
                case 'language':
                    $this->language();
                    break;
            }
        } else
            if (isset($this->post['command'])) {
                switch ($this->post['command']) {        
                    case 'language':
                        $this->languageSave();
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
            'default_language' => 'string',            
            'language' => 'string',
        );
        
        parent::init();
    }

    /**
     * Displays the languages
     */
    private function language()
    {
        $this->service_Template->set('languageTitle', t('system/admin/settings/languages/title'));
        $this->service_Template->set('defaultLanguageText', 'Standaard taal');
        
        $s_defaultLanguage = $this->getValue('defaultLanguage', 'nl_NL');
        
        $languages = $this->service_FileHandler->readDirectory(NIV . 'language');
        $languages = $this->service_FileHandler->directoryFilterName($languages, array(
            '*_*|*.lang'
        ));
        
        foreach ($languages as $language) {
            $s_filename = $language->getFilename();
            ($s_filename == $s_defaultLanguage) ? $selected = 'selected="selected"' : $selected = '';
            
            $this->service_Template->setBlock('defaultLanguage', array(
                'value' => $s_filename,
                'text' => $s_filename,
                'selected' => $selected
            ));
        }
        
        $this->service_Template->set('saveButton', t('system/buttons/save'));
    }

    /**
     * Saves the languages
     */
    private function languageSave()
    {
        if (! $this->service_Validation->validate(array(
            'default_language' => array(
                'required' => 1,
                'type' => 'string'
            )
        ), $this->post)) {
            return;
        }
        
        $this->setValue('defaultLanguage', $this->post['default_language']);
        $this->service_Settings->save();
    }
}

$obj_Languages = new Languages();
unset($obj_Languages);