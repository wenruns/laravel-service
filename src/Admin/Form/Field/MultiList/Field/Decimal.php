<?php


namespace WenRuns\Laravel\Admin\Form\Field\MultiList\Field;


use WenRuns\Laravel\Admin\Form\Field\MultiList\Field;

class Decimal extends Field
{
    public function build()
    {
        // TODO: Implement build() method.
        $this->script = <<<SCRIPT
$("input.{$this->getColumnClass()}").inputmask({"alias": "decimal", "rightAlign":true});
SCRIPT;
        return <<<HTML
<div style="position: relative;">
    <span class="{$this->asterisk}"></span>
    <input style="width: 100px; text-align: center;{$this->style}" type="text" id="{$this->getClass()}" name="{$this->getName()}" value="{$this->getValue()}" class="form-control {$this->getColumnClass()} {$this->getClass()}" placeholder="输入 {$this->getPlaceholder()}" {$this->buildAttribute()}>
</div>
HTML;

    }

    protected function buildEmpty(): string
    {
        // TODO: Implement buildEmpty() method.
        $this->oneRowScript = <<<SCRIPT
$("input.{$this->getClass()}").inputmask({"alias": "decimal", "rightAlign":true});
SCRIPT;
        return <<<HTML
<div style="position: relative">
    <span class="{$this->asterisk}"></span>
    <input style="width: 100px; text-align: center;{$this->style}" type="text" id="{$this->getClass()}" name="{$this->getName()}" value="" class="form-control {$this->getColumnClass()} {$this->getClass()}" placeholder="输入 {$this->getPlaceholder()}" {$this->buildAttribute()}>
</div>
HTML;
    }
}
