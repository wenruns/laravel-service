<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/1/26
 * Time: 10:31
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList\Field;


use WenRuns\Laravel\Admin\Form\Field\MultiList\Field;

class MultiSelect extends Field
{

    public function build()
    {
        // TODO: Implement build() method.
        $values = $this->getValue();
        if (!is_array($values)) {
            $values = explode(',', $values);
        }
        $optionStr = '<option></option>';
        foreach ($this->options as $value => $label) {
            $optionStr .= '<option value="' . $value . '" ' . (in_array($value, $values) ? 'selected' : '') . '>' . $label . '</option>';
        }
        $values = implode(',', $values);
        $this->script = <<<SCRIPT
$("select.{$this->getColumnClass()}").select2({
    "allowClear":true,
    "placeholder":{
        "id":"",
        "text":"选择{$this->getPlaceholder()}"
    }
});
SCRIPT;
        return <<<HTML
<div style="width: 100%;{$this->style};position: relative;" class="input-group">
    <span class="{$this->asterisk}"></span>
    <select id="{$this->getClass()}" class="form-control {$this->getClass()} {$this->getColumnClass()} select2-hidden-accessible" style="width: 100%;" name="{$this->getName()}[]" multiple data-placeholder="{$this->getPlaceholder()}" data-value="{$values}" tabindex="-1" aria-hidden="true" {$this->buildAttribute()}>{$optionStr}</select>
</div>
HTML;
    }



    protected function buildEmpty(): string
    {
        // TODO: Implement buildEmpty() method.
        $optionStr = '<option></option>';
        foreach ($this->options as $value => $label) {
            $optionStr .= '<option value="' . $value . '">' . $label . '</option>';
        }
        $this->oneRowScript=<<<SCRIPT
$("select.{$this->getClass()}").select2({
"allowClear":true,
"placeholder":{
    "id":"",
    "text":"选择{$this->getPlaceholder()}"
}
SCRIPT;

        return <<<HTML
<div style="width: 100%;{$this->style};position: relative;" class="input-group">
    <span class="{$this->asterisk}"></span>
    <select id="{$this->getClass()}" class="form-control {$this->getClass()} {$this->getColumnClass()} select2-hidden-accessible" style="width: 100%;" name="{$this->getName()}[]"  multiple data-placeholder="{$this->getPlaceholder()}" data-value="" tabindex="-1" aria-hidden="true" {$this->buildAttribute()}>{$optionStr}</select>
</div>
HTML;
    }
}
