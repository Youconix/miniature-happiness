<?php

namespace core;

/**
 * Site footer
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2012,2013,2014  Rachelle Scheijen
 * @author    Rachelle Scheijen
 * @since     1.0
 * @changed   12/07/12
 *
 *
 * Scripthulp framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Scripthulp framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Scripthulp framework.  If not, see <http://www.gnu.org/licenses/>.
 */
class Footer {

    private $service_Language;
    private $service_Template;
    private $service_Settings = null;

    /**
     * Starts the class footer
     */
    public function __construct(\core\services\Language $service_Language, \core\services\Template $service_Template, \core\services\Settings $service_Settings) {
        $this->service_Language = $service_Language;
        $this->service_Template = $service_Template;
        $this->service_Settings = $service_Settings;

        $this->createFooter();
    }

    /**
     * Generates the footer
     */
    private function createFooter() {
        $this->service_Template->loadTemplate('footer', 'footer.tpl');
    }

}

?>
