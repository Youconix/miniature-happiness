<?php 
namespace core\interfaces;

interface Output {
    /**
     * (re)loads the parser
     */
    public function load();
    
    /**
     * Prints the page to the screen and pushes it to the visitor
     */
    public function printToScreen();
}
?>