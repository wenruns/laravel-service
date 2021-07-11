<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/1/26
 * Time: 10:31
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList\Field;


use WenRuns\Laravel\Admin\Form\Field\MultiList\Field;
use Encore\Admin\Facades\Admin;

class Date extends Field
{
    protected $defaultFormat = 'YYYY-MM-DD';

    public function build()
    {
        // TODO: Implement build() method.
//        $key = md5(mt_rand(1000, 9999));
        $format = $this->options['format'] ?? $this->defaultFormat;
        $this->script = <<<SCRIPT
$("input.{$this->getColumnClass()}").parent().datetimepicker({"format":"{$format}","locale":"zh-CN","allowInputToggle":true});
SCRIPT;
        $this->modifyDate($this->getColumnClass());
        return <<<HTML
<div class="input-group" style="width: 100%;{$this->style};position: relative;">
    <span class="{$this->asterisk}"></span>
    <span class="input-group-addon" ><i class="fa fa-calendar fa-fw"></i></span>
    <input type="text" id="{$this->getClass()}" name="{$this->getName()}" value="{$this->getValue()}" class="form-control {$this->getColumnClass()} {$this->getClass()}" placeholder="输入{$this->getPlaceholder()}" {$this->buildAttribute()}>
</div>
HTML;
    }

    protected function modifyDate($column)
    {
        $container = $this->columnInstance->parent->getTableKey();
        $s = <<<SCRIPT
$(function(){
    $("body").on("blur", "#{$container} .{$column}", function(e){
        var box = document.querySelector("#{$container}").parentElement;
        box.style.height = "auto";
    });
    $("body").on("focus", "#{$container} .{$column}", function(e){
        var parent = e.currentTarget.parentElement, cObj = e.currentTarget;
        var box = document.querySelector("#{$container}").parentElement;
        var h = setInterval(function(){
            var datetimepicker = parent.querySelector(".bootstrap-datetimepicker-widget");
            if(datetimepicker){
                clearInterval(h);
                var inset = datetimepicker.style.inset;
                if(inset){
                    inset = inset.split(" ");
                    if(inset[0] && inset[0] != 'auto'){
                        let height = box.scrollHeight + (box.offsetHeight - box.clientHeight);
                        if(height > box.offsetHeight){
                            box.style.height = height + 'px';
                        }
                    }
                }else{
                    let top = datetimepicker.style.top;
                    if(top != 'auto'){
                         let height = box.scrollHeight + (box.offsetHeight - box.clientHeight);
                        if(height > box.offsetHeight){
                            box.style.height = height + 'px';
                        }
                    }
                }
            }
        }, 10);
    });
});
SCRIPT;
        Admin::script($s);
    }


    protected function buildEmpty(): string
    {
        // TODO: Implement buildEmpty() method.
//        $key = md5(mt_rand(1000, 9999));
        $format = $this->options['format'] ?? $this->defaultFormat;
        $this->oneRowScript = <<<SCRIPT
$("input.{$this->getClass()}").parent().datetimepicker({"format":"{$format}","locale":"zh-CN","allowInputToggle":true});
SCRIPT;

        return <<<HTML
<div class="input-group" style="width: 100%;{$this->style};position: relative;">
    <span class="{$this->asterisk}"></span>
    <span class="input-group-addon" ><i class="fa fa-calendar fa-fw"></i></span>
    <input type="text" id="{$this->getClass()}" name="{$this->getName()}"  value="" class="form-control {$this->getColumnClass()} {$this->getClass()}" placeholder="输入{$this->getPlaceholder()}" {$this->buildAttribute()}>
</div>
HTML;
    }
}
