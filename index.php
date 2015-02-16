<?php
/**
 * General landing page.
 *
 * @author:		Rachelle Scheijen <rachelle.scheijen@unixerius.nl>
 * @copyright	Rachelle Scheijen
 * @version	1.0
 * @since		1.0
 * @date made	24/09/12
 *
 */
use \core\Memory;

include (NIV . 'core/BaseLogicClass.php');

class Index extends \core\BaseLogicClass
{

    /**
     * Sets the index content
     */
    protected function view()
    {
        $this->service_Template->set('content', Memory::helpers('IndexInstall'));
    }
}
?>