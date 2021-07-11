<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/1/26
 * Time: 18:27
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList\Field;


use WenRuns\Laravel\Admin\Form\Field\MultiList\Field;

class Display extends Field
{

    public function build()
    {
        // TODO: Implement build() method.
        $class = '';
        if ($this->isRowBool || $this->showLabelBool) {
            $class = 'form-control text';
        }
        return <<<HTML
<div class="{$class} {$this->getClass()} {$this->getColumnClass()}" id="{$this->getClass()}" style="{$this->style}" {$this->buildAttribute()}>{$this->getValue()}</div>
HTML;
    }


    protected function buildEmpty(): string
    {
        // TODO: Implement buildEmpty() method.
        return <<<HTML
<div class="input-group" style="width: 100%;{$this->style}">
    <span class="input-group-addon" style="display: {$this->hideIconBool};"><i class="fa fa-pencil fa-fw"></i></span>
    <input type="text" id="{$this->getClass()}" name="{$this->getName()}"  value="" class="form-control text {$this->getColumnClass()} {$this->getClass()}" placeholder="输入{$this->getPlaceholder()}" {$this->buildAttribute()}>
</div>
HTML;
    }

}
