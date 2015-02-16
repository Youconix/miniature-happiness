<?php
if (! class_exists('\core\services\Random')) {
    require (NIV . 'include/services/Random.inc.php');
}

class DummyRandom extends \core\services\Random
{

    /**
     * Generates a random code of letters
     *
     * @param int $i_length
     *            The length of the code
     * @param boolean $bo_uppercase
     *            Set to true to use also uppercase letters
     * @return String A random letter-string
     */
    public function letter($i_length, $bo_uppercase = false)
    {
        $s_codeString = 'abcdfhijmorsuwyz';
        if ($bo_uppercase) {
            $s_codeString = 'aAbBcEFhHIjJKlLMnOpqQrtuUwxyzZ';
        }
        
        return $s_codeString;
    }

    /**
     * Generates a random code of numbers
     *
     * @param int $i_length
     *            The length of the code
     * @return String A random number-string
     */
    public function number($i_length)
    {
        $s_codeString = '13470';
        
        return $s_codeString;
    }

    /**
     * Generates a random code of numbers and letters
     *
     * @param int $i_length
     *            The length of the code
     * @param boolean $bo_uppercase
     *            Set to true to use also uppercase letters
     * @return String A random letter and number-string
     */
    public function numberLetter($i_length, $bo_uppercase)
    {
        $s_codeString = 'abcdejklmpqtvwxz1480';
        if ($bo_uppercase) {
            $s_codeString = 'aAbBcEfFiIjJKlLmnNoOpPqQStTuUvVwWXyYzZ345690';
        }
        
        return $s_codeString;
    }

    /**
     * Generates a random code of numbers and letters for a captcha
     *
     * @param int $i_length
     *            The length of the code
     * @return String A random letter and number-string
     */
    public function numberLetterCaptcha($i_length)
    {
        $s_codeString = 'abcdhpqrsvwx34569';
        
        return $s_codeString;
    }

    /**
     * Generates a random code of all signs
     *
     * @param int $i_length
     *            The length of the code
     * @return String A random sign-string
     */
    public function randomAll($i_length)
    {
        $s_codeString = 'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ1234567890`~!@#$%^&*()-_+={[}];:\|<,>.?/';
        
        $i_letters = strlen($s_codeString);
        $s_code = '';
        for ($i = 1; $i <= $i_length; $i ++) {
            $s_num = rand(0, $i_letters);
            
            $s_code .= $s_codeString[$s_num];
        }
        
        return $s_code;
    }
}