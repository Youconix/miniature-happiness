<?php
namespace core\helpers;

/**
 * Helper for generating capchas
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @since 1.0
 *        @changed 06/05/2014
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
class Captcha extends Helper
{

    private $service_Session;

    private $service_Random;

    private $i_length = 8;

    /**
     * PHP 5 constructor
     *
     * @param \core\services\Session $service_Session
     *            The session service
     * @param \core\services\Random $service_Random
     *            The random service
     */
    public function __construct(\core\services\Session $service_Session, \core\services\Random $service_Random)
    {
        $this->service_Session = $service_Session;
        $this->service_Random = $service_Random;
    }

    /**
     * Generates the capcha image
     */
    public function generateCapcha()
    {
        $s_code = $this->generateCode();
        
        $this->service_Session->set('capcha', $s_code);
        /* Image from 150 at 50 px */
        $s_image = imagecreatetruecolor(280, 40);
        
        /* Background color white */
        $background = imagecolorallocate($s_image, 255, 255, 255);
        
        $a_fonts = array(
            NIV . "fonts/route_3.ttf"
        );
        
        /* Text size 5 */
        $size = 5;
        
        /* Text color red */
        $color = imagecolorallocate($s_image, 255, 0, 0);
        
        /* Build image */
        $s_white = imagecolorallocate($s_image, 255, 255, 255);
        $s_black = imagecolorallocate($s_image, 0, 0, 0);
        $i_left = - 25;
        for ($i = 0; $i < $this->i_length; $i ++) {
            $i_text1 = mt_rand(1, 255); // RGB
            $i_text2 = mt_rand(0, 255); // RGB
            $i_text3 = mt_rand(0, 255); // RGB
            $s_text = imagecolorallocate($s_image, $i_text1, $i_text2, $i_text3);
            
            $i_size = mt_rand(18, 22); // Font-size?
            $i_angle = mt_rand(0, 45); // angle
            if (mt_rand(0, 1) == 1) {
                $i_angle *= - 1;
            }
            
            $i_up1 = mt_rand(25, 35); // How much pixels from up?
            $i_up2 = $i_up1 - 1; // Schade
            $i_up3 = $i_up1 + 2; // Schade
            
            $i_left = $i_left + 34; // Letters zijn nu eenmaal breed...
            $i_left1 = $i_left; // Hoeveel pixels van links?
            $i_left2 = $i_left1 - 3; // Schaduw
            $i_left3 = $i_left1 + 3; // Schaduw
            
            $i_random_font = array_rand($a_fonts);
            
            imagettftext($s_image, $i_size, $i_angle, $i_left3, $i_up2, $s_white, $a_fonts[$i_random_font], $s_code[$i]);
            imagettftext($s_image, $i_size, $i_angle, $i_left2, $i_up3, $s_black, $a_fonts[$i_random_font], $s_code[$i]);
            imagettftext($s_image, $i_size, $i_angle, $i_left1, $i_up1, $s_text, $a_fonts[$i_random_font], $s_code[$i]);
        }
        
        ob_clean();
        /* Write image */
        header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-type: image/png');
        ImagePng($s_image);
        
        ImageDestroy($s_image);
    }

    /**
     * Generates the capcha code
     *
     * @return String The code
     */
    private function generateCode()
    {
        $s_code = $this->service_Random->numberLetterCaptcha($this->i_length);
        
        return $s_code;
    }

    /**
     * Checks if the given code is correct
     *
     * @param String $s_code
     *            The filled in code, case insensitive
     * @return Boolean True if the code is correct, otherwise false
     */
    public function checkCaptcha($s_code)
    {
        $s_code = strtolower($s_code);
        
        if (! $this->service_Session->exists('capcha')) {
            return false;
        }
        
        if ($this->service_Session->get('capcha') != $s_code) {
            $this->service_Session->delete('capcha');
            return false;
        }
        
        $this->service_Session->delete('capcha');
        return true;
    }
}
?>
