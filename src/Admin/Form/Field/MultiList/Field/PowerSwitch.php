<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/1/26
 * Time: 10:32
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList\Field;

use WenRuns\Laravel\Admin\Form\Field\MultiList\Field;

class PowerSwitch extends Field
{

    public function build()
    {
        // TODO: Implement build() method.
        $changeEvent = $this->getEventListener('change');
        $value = $this->getValue();
        if ($value === 'off' || empty($value)) {
            $value = 0;
        }
        $ifChecked = $value ? 'checked' : '';
        $this->script = <<<SCRIPT
$(".{$this->getColumnClass()}.la_checkbox").bootstrapSwitch({
    size: "auto",
    onText: "ON",
    offText: "OFF",
    onColor: "primary",
    offColor: "default",
    onSwitchChange: function(event, state) {
        $(event.target).closest(".bootstrap-switch").next().val(state ? "on" : "off").change();
        let changeEvent = `{$changeEvent}`;
        if(changeEvent){
            eval(`var fn = ` + changeEvent);
            fn.call(this, event, state);
        }
    },
});
SCRIPT;

        return <<<HTML
<div class="input-group" style="width: 100%;{$this->style};position: relative;">
    <span class="{$this->asterisk}"></span>
    <input type="checkbox" class="{$this->getClass()} {$this->getColumnClass()} la_checkbox" {$ifChecked} {$this->buildAttribute()}>
    <input hidden class="{$this->getClass()} {$this->getColumnClass()}" id="{$this->getClass()}" name="{$this->getName()}" value="{$value}" {$this->buildAttribute()} >
</div>
HTML;
    }


    protected function buildEmpty(): string
    {
        // TODO: Implement buildEmpty() method.
        $changeEvent = $this->getEventListener('change');
        $this->oneRowScript = <<<SCRIPT
$(".{$this->getClass()}.la_checkbox").bootstrapSwitch({
    size: "auto",
    onText: "ON",
    offText: "OFF",
    onColor: "primary",
    offColor: "default",
    onSwitchChange: function(event, state) {
        $(event.target).closest(".bootstrap-switch").next().val(state ? "on" : "off").change();
        let changeEvent = `{$changeEvent}`;
        if(changeEvent){
            eval('var fn = '+changeEvent);
            fn.call(this, event, state);
        }
    },
});
SCRIPT;
        return <<<HTML
<div class="input-group" style="width: 100%;{$this->style};position: relative">
    <span class="{$this->asterisk}"></span>
    <input type="checkbox" class="{$this->getClass()} {$this->getColumnClass()} la_checkbox" {$this->buildAttribute()} />
    <input hidden class="{$this->getClass()} {$this->getColumnClass()}" id="{$this->getClass()}" name="{$this->getName()}" value=""  {$this->buildAttribute()} />
</div>
HTML;
    }


}
