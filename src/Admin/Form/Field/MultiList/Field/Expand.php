<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/1/26
 * Time: 10:41
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList\Field;


use WenRuns\Laravel\Admin\Form\Field\MultiList\Field;
use WenRuns\Laravel\Admin\Form\Field\MultiList\Row;

class Expand extends Field
{

    protected $disableCheck = true;

    protected $row = null;


    public function build()
    {
        // TODO: Implement build() method.
        return <<<HTML
<div class="form-group" style="padding: 0px 0px 0px 15px; width: 100%;{$this->style}">
    <span class="grid-expand-grid-row" data-inserted="0" data-key="{$this->getClass()}" data-toggle="collapse" data-target="#grid-collapse-{$this->getClass()}" aria-expanded="false">
        <a href="javascript:void(0)"><i class="fa fa-angle-double-down"></i>&nbsp;&nbsp;{$this->value()}</a>
        <input type="hidden" id="{$this->getClass()}" class="{$this->getClass()} {$this->getColumnClass()}" name="{$this->getName()}" value="{$this->value()}" />
    </span>
</div>
HTML;
    }

    protected function buildEmpty(): string
    {
        // TODO: Implement buildEmpty() method.
        return <<<HTML
<div class="form-group" style="padding: 0px 0px 0px 15px; width: 100%;{$this->style}">
    <span class="grid-expand-grid-row" data-inserted="0" data-key="{$this->getClass()}" data-toggle="collapse" data-target="#grid-collapse-{$this->getClass()}" aria-expanded="false" style="display: flex;">
        <a href="javascript:void(0)" style="display: flex;"><i class="fa fa-angle-double-down"></i>&nbsp;&nbsp;</a>
        <input type="text" id="{$this->getClass()}" name="{$this->getName()}" value="" placeholder="{$this->getPlaceholder()}" class="form-control text {$this->getClass()} {$this->getColumnClass()}" />
    </span>
</div>
HTML;
    }

    protected function tdStart()
    {
        $colspan = $this->columnInstance->getMultiList()->getTableList()->getColumnLen();
        return '<td colspan="' . $colspan . '" class="' . $this->getClass() . '" style="padding:0 !important; border:0;">';
    }

    /**
     * @return string
     */
    protected function tdEnd()
    {
        return '</td>';
    }

    protected function trStart($isSub = false)
    {
        if ($isSub) {
            return '<tr data-sub="true">';
        }
        return '<tr>';
    }

    protected function trEnd()
    {
        return '</tr>';
    }

    public function renderSub()
    {
        $expandContent = '<div class="collapse" id="grid-collapse-' . $this->getClass() . '" aria-expanded="true"><div style="padding: 10px 10px 0px 10px !important;">';
        if ($this->callback) {
            $this->row = new Row($this->columnInstance->getMultiList(), '', '', '', $this->getSubName(), null, $this);
            $this->row->subItems($this->columnInstance->subItems)->createEmpty($this->createEmpty);
            $this->callback->call($this, $this->row);
            $expandContent .= $this->row->render();
        }
        $expandContent .= '</div></div>';
        return $this->trStart(true) . $this->tdStart() . $expandContent . $this->tdEnd() . $this->trEnd();
    }
}
