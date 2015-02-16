<?php

/**
 * Framework installer language parser
 * 
 * This file is part of Scripthulp framework
 * 
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 *
 * 
 * Scripthulp framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Scripthulp framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with Scripthulp framework.  If not, see <http://www.gnu.org/licenses/>.
 */
class Install_Language
{

    private $a_data;

    private $a_languages = array(
        'NL',
        'DE',
        'EN'
    );

    private $s_language = 'NL';

    /**
     * PHP 5 constructor
     */
    public function __construct()
    {
        $this->populateNL();
        
        $this->populateEN();
        
        $this->checkLanguage();
    }

    /**
     * Checks the preset language
     */
    private function checkLanguage()
    {
        if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], array(
            'NL'
        ))) {
            $this->s_language = $_COOKIE['lang'];
        }
    }

    /**
     * Sets the language
     *
     * @param String $s_lang
     *            language
     */
    public function setLanguage($s_lang)
    {
        if (in_array($s_lang, $this->a_languages))
            $this->s_language = $s_lang;
    }

    /**
     * Populates the Dutch language strings
     */
    private function populateNL()
    {
        $this->a_data['NL'] = array(
            'step2' => array(
                'title' => 'Framework bestands controle'
            ),
            'step3' => array(
                'title' => 'Instellingen',
                'basedir' => 'Submap',
                'siteUrl' => 'Site url',
                'timezoneText' => 'Tijd zone',
                'sessionTitle' => 'Sessie data (laat leeg voor standaard)',
                'sessionNameText' => 'Sessie naam',
                'sessionPathText' => 'Sessie map',
                'sessionExpireText' => 'Sessie verloop tijd (seconden)',
                'siteSettings' => 'Site instellingen',
                'defaultLanguage' => 'Standaard taal',
                'templateDir' => 'Stijl map',
                'databaseSettings' => 'Database instellingen',
                'databasePrefixText' => 'Database voorvoegsel',
                'username' => 'Gebruikersnaam',
                'password' => 'Wachtwoord',
                'database' => 'Database',
                'host' => 'Server (host)',
                'port' => 'Poort (laat leeg voor standaard)',
                'type' => 'Type',
                'ftpSettings' => 'FTP instellingen',
                'buttonSave' => 'Opslaan',
                'fieldsEmpty' => 'Niet alle velden zijn ingevuld.',
                'sessionExpireInvalid' => 'De sessie verloop tijd kan alleen een cijfer zijn.',
                'languageInvalid' => 'De taal is ongeldig.',
                'templateInvalid' => 'De stijl map is ongeldig.',
                'databaseTypeInvalid' => 'Het database type is ongeldig.',
                'databaseInvalid' => 'De database gegevens is ongeldig.',
                'ftpTypeInvalid' => 'Het FTP type is ongeldig.',
                'ftpInvalid' => 'De FTP gegevens zijn ongeldig.',
                'permissionFailure' => 'Kan niet schrijven in map'
            ),
            'step4' => array(
                'error' => 'De database kon niet gevuld worden.'
            ),
            'step5' => array(
                'headerText' => 'Standaard gebruiker aanmaken',
                'nickText' => 'Gebruikersnaam',
                'emailText' => 'E-mail adres',
                'password' => 'Wachtwoord',
                'password2' => 'Wachtwoord herhalen',
                'buttonSubmit' => 'Aanmaken',
                'fieldsEmpty' => 'Niet alle velden zijn ingevuld.',
                'passwordInvalid' => 'De wachtwoorden zijn niet gelijk.',
                'error' => 'De admin gebruiker kon niet aangemaakt worden.'
            ),
            'step6' => array(
                'complete' => 'De installatie is voltooid.',
                'removeDir' => 'Verwijder de map install!'
            )
        );
    }

    /**
     * Populates the Englisch language strings
     */
    private function populateEN()
    {
        $this->a_data['EN'] = array(
            'step2' => array(
                'title' => 'Framework file check'
            ),
            'step3' => array(
                'title' => 'Settings',
                'basedir' => 'Submap',
                'siteUrl' => 'Site url',
                'timezoneText' => 'Time zone',
                'sessionTitle' => 'Session data (leave empty for default)',
                'sessionNameText' => 'Session name',
                'sessionPathText' => 'Sessipm map',
                'sessionExpireText' => 'Session expire time (seconds)',
                'siteSettings' => 'Site settings',
                'defaultLanguage' => 'Default language',
                'templateDir' => 'Style map',
                'databaseSettings' => 'Database settings',
                'databasePrefixText' => 'Database prefix',
                'username' => 'Username',
                'password' => 'Password',
                'database' => 'Database',
                'host' => 'Server (host)',
                'port' => 'Port (leave empty for default)',
                'type' => 'Type',
                'ftpSettings' => 'FTP settings',
                'buttonSave' => 'Save',
                'fieldsEmpty' => 'Not all the fields are filled in.',
                'sessionExpireInvalid' => 'The session expire time can only be a number.',
                'languageInvalid' => 'The language is invalid.',
                'templateInvalid' => 'The template directory is invalid.',
                'databaseTypeInvalid' => 'The database type is invalid.',
                'databaseInvalid' => 'The database data is invalid.',
                'ftpTypeInvalid' => 'The FTP type is invalid.',
                'ftpInvalid' => 'The FTP data is invalid.',
                'permissionFailure' => 'Can not write directory'
            ),
            'step4' => array(
                'error' => 'The database could not be filled.'
            ),
            'step5' => array(
                'headerText' => 'Generate default user',
                'nickText' => 'Username',
                'emailText' => 'E-mail address',
                'password' => 'Password',
                'password2' => 'Repeat password',
                'buttonSubmit' => 'Generate',
                'fieldsEmpty' => 'Not all the fields are filled in.',
                'passwordInvalid' => 'The passwords are not equal.',
                'error' => 'The admin user could not be made.'
            ),
            'step6' => array(
                'complete' => 'The installation is complete.',
                'removeDir' => 'Remove the directory install!'
            )
        );
    }

    /**
     * Returns the requests language string
     *
     * @param String $s_key
     *            language key
     * @return String language string
     */
    public function get($s_key)
    {
        $a_block = $this->a_data[$this->s_language];
        $a_parts = explode('/', $s_key);
        
        $i_num = count($a_parts) - 1;
        for ($i = 0; $i <= $i_num; $i ++) {
            if ($i < $i_num) {
                $a_block = $a_block[$a_parts[$i]];
            } else {
                return $a_block[$a_parts[$i]];
            }
        }
    }
}
?>