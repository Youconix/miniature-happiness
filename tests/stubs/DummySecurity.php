<?php
if (! class_exists('\core\services\Service')) {
    require (NIV . 'include/services/Service.inc.php');
}
if (! class_exists('\core\services\Security')) {
    require_once (NIV . 'include/services/Security.inc.php');
}

class DummySecurity extends \core\services\Security
{

    public function secureString($s_input)
    {
        return $s_input;
    }
}
?>