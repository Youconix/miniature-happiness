<?php
if (! isset($_POST['accessToken']) || ! isset($_POST['username']) || ! isset($_POST['command'])) {
    die("FALSE");
}

define('NIV', '../');
define('PROCESS', '1');
define('DATA_DIR', '../../data/');

require (NIV . 'include/Memory.php');
Memory::startUp();

$_POST['username'] = Memory::services('Security')->secureStringDB($_POST['username']);

if ($_POST['command'] == 'checkLogin') {
    $service_Autorization = Memory::services('FacebookAuthorization');
    
    $a_data = $service_Autorization->loginOpenIDConfirm($_POST['accessToken'], $_POST['username']);
    if (is_null($a_data)) {
        echo ("FALSE");
    } else {
        Memory::services('Session')->setLogin($a_data['id'], $a_data['nick'], $a_data['lastLogin'], $a_data['userType']);
        echo ("TRUE");
    }
} else 
    if ($_POST['command'] == 'checkRegistration') {
        if (! isset($_POST['email']))
            die("FALSE");
        
        $service_Autorization = Memory::services('FacebookAuthorization');
        $service_Security = Memory::services('Security');
        
        $s_type = Memory::services('Session')->get('type');
        
        $s_accessToken = $service_Security->secureStringDB($_POST['accessToken']);
        $s_username = $service_Security->secureStringDB($_POST['username']);
        $s_email = $service_Security->secureStringDB($_POST['email']);
        
        $i_code = $service_Autorization->registerOpenID($s_accessToken, 'Facebook', $s_username, $s_email);
        echo ($i_code);
    }
?>
