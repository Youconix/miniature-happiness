
<?php
$s_username = 'rachelle';
$s_password = 'crazyrambo';
$s_salt = 'fcc8fb01dc800fa60d3eebbb9685c587dcd465853aaa05677a74cd7bf133';

$s_text = substr(md5(strtolower($s_username)), 5, 30) . $s_password;

$a_options = array( 'salt' => $s_salt );

$s_hash = password_hash($s_text, PASSWORD_BCRYPT, $a_options);
echo($s_hash);
?>