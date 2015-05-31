<?php
namespace core;

class Input implements \ArrayAccess {
    /**
     * @var \core\services\Security
     */
    protected $service_Security;
    
    /**
     * @var \core\services\Validation
     */
    protected $service_Validation;
    
    protected $s_type;
    
    protected $a_container = array();
    
    public function __construct(\core\services\Security $service_Security,\core\services\Validation $service_Validation){
        $this->service_Security = $service_Security;
        $this->service_Validation = $service_Validation;
    }
    
    public function parse($s_type,$a_fields){
        $this->s_type = $s_type;
        
        $this->a_container = $this->service_Security->secureInput($s_type, $a_fields);
    }
    
    public function has($s_key){
        return array_key_exists($s_key,$this->a_container);
    }
    
    public function get($s_key){
        if( !$this->has($s_key) ){
            throw new \OutOfBoundsException('Key '.$s_key.' is not present in collection '.$s_type.'.');
        }
        
        return $this->a_container[$s_key];
    }
    
    public function validate($a_rules){
        $a_errors = $this->validateErrors($a_rules);
        
        return (count($a_errors) == 0 );
    }
    
    public function validateErrors($a_rules){
        if( $this->service_Validation->validate($a_rules, $this->a_container) ){
            return array();
        }
        
        return $this->service_Validation->getErrors();
    }
    
    public function getValidation(){
        return $this->service_Validation;
    }
    
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
    
    public function offsetExists($offset) {
        return $this->has($offset);
    }
    
    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }
    
    public function offsetGet($offset) {
        return $this->get($offset);
    }
}