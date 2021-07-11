<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/3/23
 * Time: 9:37
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList\Field;


use WenRuns\Laravel\Admin\Form\Field\MultiList\Field;

class TextArea extends Field
{

    public function build()
    {
        // TODO: Implement build() method.
        return <<<HTML
<span style="position: relative">
    <span class="{$this->asterisk}"></span>
    <textarea rows="5" id="{$this->getClass()}" class="form-control {$this->getClass()} {$this->getColumnClass()}"  name="{$this->getName()}" placeholder="输入{$this->getPlaceholder()}" style="{$this->style}" {$this->buildAttribute()}>{$this->getValue()}</textarea>
</span>
HTML;
    }


    protected function buildEmpty(): string
    {
        // TODO: Implement buildEmpty() method.
        return <<<HTML
<span style="position: relative">
    <span class="{$this->asterisk}"></span>
    <textarea rows="5" id="{$this->getClass()}" class="form-control {$this->getClass()} {$this->getColumnClass()}" name="{$this->getName()}" placeholder="输入{$this->getPlaceholder()}"  style="{$this->style}" {$this->buildAttribute()}></textarea>
</span>
HTML;
    }
}
