<?php
class HTML_Textarea extends HtmlFormItem {
    private $s_name;
    private $i_rows = 0;
    private $i_cols = 0;

    /**
     * Generates a new textarea item
     * 
     * @param string $s_name	The name
     * @param string $s_value	The content
     */
    public function __construct($s_name, $s_value) {
        $this->s_name = $s_name;
        $this->s_value = $this->parseContent($s_value);

        $this->s_tag = '<textarea rows="{rows}" cols="{cols}" name="{name}" {between}>{value}</textarea>';
    }

    /**
     * Destructor
     */
    public function __destruct() {
        $this->s_name = null;
        $this->i_rows = null;
        $this->i_cols = null;

        parent::__destruct();
    }

    /**
     * Sets the number of rows
     * 
     * @param int $i_rows	The number of rows
     */
    public function setRows($i_rows) {
        if ($i_rows >= 0)
            $this->i_rows = $i_rows;

        return $this;
    }

    /**
     * Sets the number of cols
     * 
     * @param int $i_cols	The number of cols
     */
    public function setCols($i_cols) {
        if ($i_cols >= 0)
            $this->i_cols = $i_cols;

        return $this;
    }

    /**
     * Generates the (X)HTML-code
     *
     * @see HtmlFormItem::generateItem()
     * @return  string  The (X)HTML code
     */
    public function generateItem() {
        $this->s_tag = str_replace(array('{rows}', '{cols}', '{name}'), array($this->i_rows, $this->i_cols, $this->s_name), $this->s_tag);

        return parent::generateItem();
    }
}
?>