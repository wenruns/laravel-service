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

class Modal extends Field
{
    protected $row = null;

    protected $disableCheck = true;

    public function build()
    {
        // TODO: Implement build() method.
        return <<<HTML
<div class="form-group" style="padding: 0px 0px 0px 15px;width: 100%;{$this->style}">
    <span class="grid-expand" data-toggle="modal" data-target="#grid-modal-{$this->getClass()}">
        <a href="javascript:void(0)"><i class="fa fa-clone"></i>&nbsp;&nbsp;{$this->value()}</a>
        <input type="hidden" id="{$this->getClass()}" class="{$this->getClass()} {$this->getColumnClass()}" name="{$this->getName()}" value="{$this->value()}" />
    </span>
</div>
HTML;
    }


    protected function buildEmpty(): string
    {
        // TODO: Implement buildEmpty() method.
        return <<<HTML
<div class="form-group" style="padding: 0px 0px 0px 15px;width: 100%;{$this->style}; display: flex;">
    <span class="grid-expand" data-toggle="modal" data-target="#grid-modal-{$this->getClass()}">
        <a href="javascript:void(0)"><i class="fa fa-clone"></i>&nbsp;&nbsp;</a>
    </span>
    <input type="text" id="{$this->getClass()}" class="{$this->getClass()} {$this->getColumnClass()}" name="{$this->getName()}"  value="" class="form-control text" placeholder="{$this->getPlaceholder()}" />
</div>
HTML;
    }

    protected function tdStart()
    {
        $colspan = $this->columnInstance->getMultiList()->getTableList()->getColumnLen();
        return '<td colspan="' . $colspan . '" class="' . $this->getClass() . '" style="padding:0 !important; border:0;">';
    }

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
        $expandContent = '';
        if ($this->callback) {
            $this->row = new Row($this->columnInstance->getMultiList(), '', '', '', $this->getSubName(), null, $this);
            $this->row->subItems($this->columnInstance->subItems)->createEmpty($this->createEmpty);
            $this->callback->call($this, $this->row);
            $expandContent .= $this->row->render();
        }
        $title = '--' . $this->value();
        if ($this->createEmpty) {
            $title = '';
        }
        return <<<HTML
{$this->trStart(true)}
{$this->tdStart()}
<div class="modal" id="grid-modal-{$this->getClass()}" tabindex="-1" role="dialog" aria-hidden="true">
    <div style="width: 100%;height: 100%;display: flex;justify-content: center;align-items: center;">
        <div class="modal-content" style="min-width: 50%;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">{$this->getPlaceholder()}<span class="{$this->getClass()} modal-title-text">{$title}</span></h4>
            </div>
            <div class="modal-body">{$expandContent}</div>
        </div>
    </div>
</div>
{$this->tdEnd()}
{$this->trEnd()}
HTML;
    }

}
