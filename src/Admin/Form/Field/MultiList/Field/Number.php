<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/3/12
 * Time: 15:12
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList\Field;


use WenRuns\Laravel\Admin\Form\Field\MultiList\Field;

class Number extends Field
{

    public function build()
    {
        // TODO: Implement build() method.
        $this->script=<<<SCRIPT
$('input.{$this->getColumnClass()}:not(.initialized)').addClass('initialized').bootstrapNumber({
    upClass: 'success',
    downClass: 'primary',
    center: true
});
SCRIPT;
        return <<<HTML
<div style="width: 100%;{$this->style}; position: relative;">
    <span class="{$this->asterisk}"></span>
    <input style="text-align: center;" type="text" id="{$this->getClass()}" name="{$this->getName()}" value="{$this->getValue()}" class="form-control {$this->getClass()} {$this->getColumnClass()}" placeholder="输入{$this->getPlaceholder()}" {$this->buildAttribute()}>
</div>
HTML;
    }

    protected function buildEmpty(): string
    {
        // TODO: Implement buildEmpty() method.
        $this->oneRowScript =<<<SCRIPT
$('input.{$this->getClass()}:not(.initialized)').addClass('initialized').bootstrapNumber({
    upClass: 'success',
    downClass: 'primary',
    center: true
});
SCRIPT;

        return <<<HTML
<div style="width: 100%;{$this->style};position: relative;">
    <span class="{$this->asterisk}"></span>
    <input style="text-align: center;" type="text" id="{$this->getClass()}" name="{$this->getName()}" value=""  class="form-control {$this->getClass()} {$this->getColumnClass()}" placeholder="输入{$this->getPlaceholder()}" {$this->buildAttribute()}>
</div>
HTML;
    }


}
