<?php

class DummyCoreHtmlItem extends core\helpers\html\CoreHtmlItem
{

    public function __construct($s_tag)
    {
        $this->s_tag = $s_tag;
        $this->s_htmlType = 'html5';
    }
}