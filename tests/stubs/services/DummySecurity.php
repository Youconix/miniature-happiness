<?php
namespace tests\stubs\services;

class DummySecurity extends \core\services\Security
{

    public function secureString($s_input)
    {
        return $s_input;
    }
}
?>