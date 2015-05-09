<?php
namespace tests\stubs\services;

class Security extends \core\services\Security
{

    public function secureString($s_input)
    {
        return $s_input;
    }
}
?>