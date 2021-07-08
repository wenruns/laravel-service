<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/1/26
 * Time: 10:31
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList\Field;


use WenRuns\Laravel\Admin\Form\Field\MultiList\Field;

class Select extends Field
{
    public function build()
    {
        // TODO: Implement build() method.
        $optionStr = '<option></option>';
        foreach ($this->options as $value => $label) {
            $optionStr .= '<option value="' . $value . '" ' . ($this->getValue() == $value ? 'selected' : '') . '>' . $label . '</option>';
        }
        $this->script = <<<SCRIPT
$("select.{$this->getColumnClass()}").select2({
    "allowClear":true,
    "placeholder":{
        "id":"",
        "text":"选择{$this->getPlaceholder()}"
    },
});
SCRIPT;

        return <<<HTML
<div style="width: 100%;{$this->style};position: relative" class="input-group">
    <span class="{$this->asterisk}"></span>
    <select class="form-control {$this->getClass()} {$this->getColumnClass()} select2-hidden-accessible" id="{$this->getClass()}" name="{$this->getName()}" data-value="{$this->getValue()}" tabindex="-1" aria-hidden="true" data-placeholder="{$this->getPlaceholder()}" style="width:100%;" {$this->buildAttribute()}>{$optionStr}</select>
</div>
HTML;
    }


    protected function buildEmpty(): string
    {
        // TODO: Implement buildEmpty() method.
        $optionStr = '<option></option>';
        foreach ($this->options as $value => $label) {
            $optionStr .= '<option value="' . $value . '" >' . $label . '</option>';
        }
        $this->oneRowScript = <<<SCRIPT
$("select.{$this->getClass()}").select2({
    "allowClear":true,
    "placeholder":{
        "id":"",
        "text":"选择{$this->getPlaceholder()}"
    },
});
SCRIPT;
        return <<<HTML
<div style="width: 100%;{$this->style};position: relative;" class="input-group">
    <span class="{$this->asterisk}"></span>
    <select class="form-control {$this->getClass()} {$this->getColumnClass()} select2-hidden-accessible" id="{$this->getClass()}" name="{$this->getName()}" data-value="" tabindex="-1" aria-hidden="true" data-placeholder="{$this->getPlaceholder()}" style="width:100%;"  {$this->buildAttribute()}>{$optionStr}</select>
</div>
HTML;
    }


}
