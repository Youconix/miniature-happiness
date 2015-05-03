<?php
$a_routes = array(
    'forgot_password/verifyCode' => array(
        'regex' =>  '#^forgot_password/verifyCode/([a-zA-Z0-9]+)$#',
        'fields' => array(
            'code'
        ),
        'page' => 'forgot_password',
        'command' => 'verifyCode'
    )
);

?>