<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/1/26
 * Time: 10:30
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList\Field;


use WenRuns\Laravel\Admin\Form\Field\MultiList\Field;

class Text extends Field
{

    public function build()
    {
        // TODO: Implement build() method.
        return <<<HTML
<div class="input-group" style="width: 100%;{$this->style};position: relative;">
    <span class="{$this->asterisk}"></span>
    <span class="input-group-addon" style="display: {$this->hideIconBool};"><i class="fa fa-pencil fa-fw"></i></span>
    <input type="text" id="{$this->getClass()}" name="{$this->getName()}" value="{$this->getValue()}" class="form-control {$this->getClass()} {$this->getColumnClass()} text" placeholder="输入{$this->getPlaceholder()}" {$this->buildAttribute()}>
</div>
HTML;
    }


    protected function buildEmpty(): string
    {
        // TODO: Implement buildEmpty() method.
        return <<<HTML
<div class="input-group" style="width: 100%;{$this->style};position: relative;">
    <span class="{$this->asterisk}"></span>
    <span class="input-group-addon" style="display: {$this->hideIconBool};"><i class="fa fa-pencil fa-fw"></i></span>
    <span class="{$this->asterisk}">
        <input type="text" id="{$this->getClass()}" name="{$this->getName()}" value="" class="form-control {$this->getClass()} {$this->getColumnClass()} text" placeholder="输入{$this->getPlaceholder()}"  {$this->buildAttribute()}>
    </span>
</div>
HTML;
    }

}
