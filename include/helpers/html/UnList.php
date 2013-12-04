<?php
class HTML_UnList extends HtmlFormItem {
    private $a_fields;

    /**
     * Generates a new list element
     *
     * @param boolean	$bo_numberd True when a numberd list is needed, default false
     */
    public function __construct($bo_numberd) {
        if (!$bo_numberd) {
            $this->s_tag = "<ul {between}>{value}</ul>\n";
        } else {
            $this->s_tag = "<ol {between}>{value}</ol>\n";
        }

        $this->a_fields = array();
    }

    /**
     * Adds a list row item
     *
     * @param string/Html_ListItem	$s_row  The list row item
     */
    public function addRow($s_row) {
        if (is_object($s_row)) {
            if (get_class($s_row) != 'HTML_ListItem')
                throw new Exception("Unexpected input in UnList::addRow. Expect string or ListItem");

            $s_row = $s_row->generateItem();
        }

        $this->a_fields[] = $s_row;

        return $this;
    }

    /**
     * Creates a new row item and adds it
     *
     * @param	string	$s_content	The content of the row
     */
    public function createRow($s_content) {
        $s_content = $this->parseContent($s_content);

        return $this->addRow(new HTML_ListItem($s_content));
    }

    /**
     * Generates the (X)HTML-code
     *
     * @see HtmlFormItem::generateItem()
     * @return  string  The (X)HTML code
     */
    public function generateItem() {
        $this->s_value = '';
        foreach ($this->a_fields AS $s_row) {
            $this->s_value .= $s_row . "\n";
        }

        return parent::generateItem();
    }
}

class HTML_ListItem extends HtmlItem {
    /**
     * Generates a new list item element
     * 
     * @param string/CoreHtmLItem $s_content		The content
     */
    public function __construct($s_content) {
        $this->s_tag = "<li {between}>" . $this->parseContent($s_content) . "</li>\n";
    }
}
?>