<?php
class DummyHtmlFormItem extends core\helpers\html\HtmlFormItem
{

    public function __construct($s_tag)
    {
        $this->s_tag = $s_tag;
        $this->s_htmlType = 'html5';
    }
}