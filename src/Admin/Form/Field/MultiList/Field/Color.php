<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/3/18
 * Time: 18:12
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList\Field;



use WenRuns\Laravel\Admin\Form\Field\MultiList\Field;

class Color extends Field
{

    public function build()
    {
        $this->script = <<<SCRIPT
$("input.{$this->getColumnClass()}").parent().colorpicker([]);
$(".colorpicker.dropdown-menu").css("z-index", 9999999);
SCRIPT;
        // TODO: Implement build() method.
        return <<<HTML
<div class="input-group colorpicker-element" style="width: 100%;position: relative;">
    <span class="{$this->asterisk}"></span>
    <span class="input-group-addon"><i style="background-color: {$this->getValue()};"></i></span>
    <input style="{$this->style}" type="text" id="{$this->getClass()}" name="{$this->getName()}" value="{$this->getValue()}" class="form-control {$this->getColumnClass()} {$this->getClass()}" placeholder="输入{$this->getPlaceholder()}" {$this->buildAttribute()}>
</div>
HTML;
    }

    protected function buildEmpty(): string
    {
        $this->oneRowScript = <<<SCRIPT
$("input.{$this->getClass()}").parent().colorpicker([]);
$(".colorpicker.dropdown-menu").css("z-index", 9999999);
SCRIPT;
        // TODO: Implement buildEmpty() method.
        return <<<HTML
<div class="input-group colorpicker-element" style="width: 100%;position: relative;">
    <span class="{$this->asterisk}"></span>
    <span class="input-group-addon"><i style="background-color: {$this->getValue()};"></i></span>
    <input style="{$this->style}" type="text" id="{$this->getClass()}" name="{$this->getName()}" value="" class="form-control {$this->getColumnClass()} {$this->getClass()}" placeholder="输入{$this->getPlaceholder()}" {$this->buildAttribute()}>
</div>
HTML;

    }


}
