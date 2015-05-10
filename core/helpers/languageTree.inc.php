<?php
namespace core\helpers;

class languageTree extends Helper {
    /**
     * @var \DOMDocument
     */
    private $dom_document;
    private $s_language;
    /**
     * @var TreeRoot
     */
    private $obj_root;
    
    public function init($s_language,$s_file){
        $this->s_language = $s_language;
        $s_file = NIV.'language'.DIRECTORY_SEPARATOR.$s_language.DIRECTORY_SEPARATOR.'LC_MESSAGES'.DIRECTORY_SEPARATOR.$s_file;
        
        $this->dom_document = new \DOMDocument('1.0', true);
        
        // We don't want to bother with white spaces
        $this->dom_document->preserveWhiteSpace = false;
        
        $this->dom_document->resolveExternals = true; // for character entities
        
        $this->dom_document->formatOutput = true; // keep output alignment
        
        if (! $this->dom_document->Load($s_file)) {
            throw new \IOException("Can not load XML-file " . $s_file . ". Check the address");
        }
    }
    
    public function parse(){
        $this->obj_root = new TreeRoot($this->dom_document);
        $this->obj_root->parse();
    }
    
    public function build(){
        return $this->obj_root->build();
    }
}

class TreeRoot {
    /**
     * @var \DOMDocument
     */
    protected $dom_document;
    /**
     * @var DOMElement
     */
    protected $obj_root;
    protected $s_path;
    protected $a_children = array();
    
    public function __construct($dom_document){
        $this->dom_document = $dom_document;
        $this->obj_root = $dom_document->documentElement;
        $this->s_path = $this->obj_root->nodeName;
    }
    
    public function parse(){
        $children = $this->obj_root->childNodes;
        
        foreach ($children as $child) {
            if( $child instanceof \DOMText ){
                continue;
            }
            
            $obj_child =  new TreeBranch($child,$this->s_path);
            
            $obj_child->parse();
            $this->a_children[] = $obj_child;
        }
    }
    
    public function build(){
        $s_tree = '<ul id="language_tree" class="closed">'."\n
        <li data-path=\"".$this->obj_root->tagName."\" data-type=\"tree\" class=\"closed\" style=\"display:inline-block\"><span class=\"tree\">+</span>".$this->obj_root->tagName."<ul class=\"closed\">\n";
                
        foreach($this->a_children AS $child ){
            $s_tree .= $child->build()."\n";
            
        }
        $s_tree .= "</ul>\n
        </li>\n
        </ul>\n";
        
        return $s_tree;
    }
}

class TreeBranch extends TreeRoot {
    public function __construct($obj_root,$s_path){        
        $this->obj_root = $obj_root;
        $this->s_path = $s_path.'/'.$obj_root->tagName;
    }
    
    public function build(){
        if( count($this->a_children) > 0 ){
            $s_type = 'tree';
            $s_span = '<span class="tree">+</span>';
        }
        else {
            $s_type = 'leaf';
            $s_span = '';
        }
        
        $s_tree = '<li data-path="'.$this->s_path.'" data-type="'.$s_type.'" class="closed">'.$s_span.$this->obj_root->tagName."\n";
        
        if( count($this->a_children) > 0 ){
            $s_tree .= "<ul class=\"closed\">\n";
        
            foreach($this->a_children AS $child ){
                $s_tree .= $child->build()."\n";
        
            }
            $s_tree .= "</ul>\n";
         }
        $s_tree .= "</li>\n";
    
        return $s_tree;
    }
}