<?php
namespace core\helpers\html;

class Span extends HtmlItem
{

    /**
     * Generates a new span element
     *
     * @param String $s_content
     *            The content of the span
     */
    public function __construct($s_content)
    {
        $this->s_tag = '<span {between}>' . $this->parseContent($s_content) . '</span>';
    }
}
?>