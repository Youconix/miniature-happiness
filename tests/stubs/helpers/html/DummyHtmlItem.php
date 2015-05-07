<?php
namespace tests\stubs\helpers\html;

class DummyHtmlItem extends core\helpers\html\HtmlItem
{

    public function __construct($s_tag)
    {
        $this->s_tag = $s_tag;
        $this->s_htmlType = 'html5';
    }
}