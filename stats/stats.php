<?php
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
 * Statistics collection class
 * Collects the information from the visiting user                                            
 *                                                                              
 * This file is part of Miniature-happiness                                    
 *                                                                              
 * @copyright Youconix                                
 * @author    Rachelle Scheijen                                                
 * @since     1.0
 */
define('PROCESS', '1');
define('NIV', '../');
define('DATA_DIR', '../../data/');

class Stats
{

    private $s_page;

    private $s_ip;

    private $s_reference = '';

    private $s_browser;

    private $s_browserVersion;

    private $s_OS;

    private $s_OsType;

    private $i_colors = null;

    private $i_width = null;

    private $i_height = null;

    /**
     * PHP 5 constructor
     */
    public function __construct()
    {
        $this->init();
        
        $this->save();
        
        $this->image();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        Memory::endProgram();
    }

    /**
     * Inits the class Stats
     * Collects the information from the client
     */
    private function init()
    {
        require (NIV . 'include/Memory.php');
        Memory::startUp();
        
        $a_init_get = array(
            'colors' => 'int',
            'width' => 'int',
            'height' => 'int',
            'page' => 'string-DB'
        );
        
        $service_Security = Memory::services('Security');
        $a_get = $service_Security->secureInput('GET', $a_init_get);
        
        if (array_key_exists('colors', $a_get)) {
            $this->i_colors = $a_get['colors'];
            $this->i_width = $a_get['width'];
            $this->i_height = $a_get['height'];
        }
        
        $this->s_ip = $_SERVER['REMOTE_ADDR'];
        
        if (array_key_exists('HTTP_REFERER', $_SERVER))
            $this->s_reference = $service_Security->secureStringDB($_SERVER['HTTP_REFERER']);
        
        $s_useragent = $service_Security->secureStringDB($_SERVER['HTTP_USER_AGENT']);
        $this->s_page = $a_get['page'];
        
        /* Get OS */
        if (stripos($s_useragent, 'Linux') !== false) {
            if (stripos($s_useragent, 'Android') !== false) {
                $this->s_OS = 'Android';
                $this->s_OsType = 'Linux';
            } else 
                if (stripos($s_useragent, 'Ubuntu') !== false) {
                    $this->s_OS = 'Ubuntu';
                    $this->s_OsType = 'Linux';
                } else {
                    $this->s_OS = 'Linux';
                    $this->s_OsType = 'Linux';
                }
        } else 
            if (stripos($s_useragent, 'Windows') != false) {
                $this->s_OsType = 'Windows';
                
                if (stripos($s_useragent, 'Windows NT 5.1') != false) {
                    $this->s_OS = 'Windows XP';
                } else 
                    if (stripos($s_useragent, 'Windows NT 5.2') != false) {
                        $this->s_OS = 'Windows XP 64-Bit';
                    } else 
                        if (stripos($s_useragent, 'Windows NT 6.0') != false) {
                            $this->s_OS = 'Windows Vista';
                        } else 
                            if (stripos($s_useragent, 'Windows NT 6.1') != false) {
                                $this->s_OS = 'Windows 7';
                            } else 
                                if (stripos($s_useragent, 'Windows NT 6.2') != false) {
                                    $this->s_OS = 'Windows 8';
                                } else 
                                    if (stripos($s_useragent, 'Windows Phone') !== false) {
                                        $i_start = stripos($s_useragent, 'Windows Phone OS');
                                        $this->s_OS = trim(substr($s_useragent, $i_start, (strpos($s_useragent, ';', $i_start) - $i_start)));
                                    }
            } else 
                if (stripos($s_useragent, 'iPhone') !== false) {
                    $this->s_OS = 'iPhone';
                    $this->s_OsType = 'OS X';
                } else 
                    if (stripos($s_useragent, 'iPad') !== false) {
                        $this->s_OS = 'iPad';
                        $this->s_OsType = 'OS X';
                    } else 
                        if (stripos($s_useragent, 'Mac OS X') !== false) {
                            $this->s_OS = 'OS X';
                            $this->s_OSType = 'OS X';
                        } else {
                            $this->s_OS = 'Unknown';
                            $this->s_OsType = 'Unknown';
                        }
        
        /* Get browser */
        if (stripos($s_useragent, 'Firefox') !== false) {
            $a_version = explode('Firefox/', $s_useragent);
            $this->s_browserVersion = end($a_version);
            $this->s_browser = 'Firefox';
        } else 
            if (stripos($s_useragent, 'Opera') !== false) {
                $a_version = explode('Version/', $s_useragent);
                $this->s_browserVersion = end($a_version);
                $this->s_browser = 'Opera';
            } else 
                if (stripos($s_useragent, 'MSIE') !== false) {
                    $i_start = stripos($s_useragent, 'MSIE') + 4;
                    $this->s_browserVersion = trim(substr($s_useragent, $i_start, (stripos($s_useragent, ';', $i_start) - $i_start)));
                    $this->s_browser = 'Internet Explorer';
                } else 
                    if (stripos($s_useragent, 'Chrome') !== false) {
                        $this->s_browser = 'Chrome';
                        $i_start = stripos($s_useragent, 'Chrome') + 7;
                        
                        $s_version = substr($s_useragent, $i_start, (strpos($s_useragent, ' ', $i_start) - $i_start));
                        $a_versionPre = explode('.', $s_version);
                        $a_version = array(
                            0 => $a_versionPre[0],
                            1 => $a_versionPre[1]
                        );
                        $this->s_browserVersion = implode('.', $a_version);
                    } else 
                        if (stripos($s_useragent, 'Safari') !== false) {
                            $a_version = explode('Version/', $s_useragent);
                            $a_version = explode(' ', $a_version[(count($a_version) - 1)]);
                            $this->s_browserVersion = trim($a_version[0]);
                            $this->s_browser = 'Safari';
                        } else {
                            $this->s_browser = 'Unknown';
                            $this->s_browserVersion = 'Unknown';
                        }
    }

    /**
     * Saves the stats
     */
    private function save()
    {
        $model_Stats = Memory::models('Stats');
        
        if (! $model_Stats->saveIP($this->s_ip, $this->s_page)) {
            return;
        }
        
        /* Unique visitor */
        $model_Stats->saveOS($this->s_OS, $this->s_OsType);
        $model_Stats->saveBrowser($this->s_browser, $this->s_browserVersion);
        $model_Stats->saveReference($this->s_reference);
        
        if (! is_null($this->i_colors)) {
            $model_Stats->saveScreenSize($this->i_width, $this->i_height);
            $model_Stats->saveScreenColors($this->i_colors . '');
        }
    }

    /**
     * Displays the dummy image
     */
    private function image()
    {
        $s_styledir = Memory::services('XmlSettings')->get('settings/templates/dir');
        $s_file = NIV . 'styles/' . $s_styledir . '/images/stats.png';
        
        header('Content-type: image/png');
        header('Content-Transfer-Encoding: binary');
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        header('Content-Length: ' . filesize($s_file));
        readfile($s_file);
    }
}

$obj_Stats = new Stats();
unset($obj_Stats);