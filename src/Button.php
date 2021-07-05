<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2021/7/4
 * Time: 15:46
 */

namespace WenRuns\Service;

use Encore\Admin\Facades\Admin;

/**
 * Class Button
 * @method Button text($value = null)
 * @method Button class($value = null)
 * @method Button size($value = null)
 * @method Button type($value = null)
 * @method Button icon($value = null)
 * @method Button style($value = null)
 * @method Button attributes($value = null)
 * @method Button url($value = null)
 * @method Button id($value = null)
 * @method Button eventFn($value = null)
 * @package WenRuns\Service
 */
class Button
{
    protected $text;

    protected $class = '';

    protected $size = 'xs';

    protected $type = 'default';

    protected $icon = '';

    protected $style = '';

    protected $attributes = [];

    protected $id = '';

    protected $url = 'javascript:void(0)';

    protected $hide = false;

    protected $eventFn = null;

    const SIZE_SM = 'sm';
    const SIZE_XS = 'xs';

    const TYPE_DEFAULT = 'default';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';
    const TYPE_SUCCESS = 'success';
    const TYPE_PRIMARY = 'primary';
    const TYPE_INFO = 'info';

    /**
     * Button constructor.
     * @param $text
     * @param array $options
     */
    public function __construct($text, $options = [])
    {
        $this->text = $text;
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    }

    public function hide($value = true)
    {
        $this->hide = $value;
        return $this;
    }

    protected function getUrl()
    {
        return $this->url();
    }

    protected function getType()
    {
        return $this->type();
    }

    protected function getSize()
    {
        return $this->size();
    }

    protected function getClass()
    {
        return $this->class();
    }

    protected function getId()
    {
        if (empty($this->id())) {
            $this->id('btn-' . md5($this->text()) . '-' . mt_rand(0, 10000));
        }
        return $this->id();
    }

    protected function getText()
    {
        return $this->text();
    }

    protected function getStyle()
    {
        $style = $this->style();
        if (is_array($style)) {
            $str = '';
            foreach ($style as $name => $value) {
                $str .= $name . '=' . $value . ';';
            }
            return $str;
        }
        return $style;
    }

    protected function getAttribute()
    {
        $attributes = $this->attributes();
        if (is_array($attributes)) {
            $str = '';
            foreach ($attributes as $key => $value) {
                $str .= $key . '="' . $value . '" ';
            }
            return $str;
        }
        return $attributes;
    }

    protected function getIcon()
    {
        $icon = $this->icon();
        if (empty($icon)) {
            return '';
        }
        if (is_array($icon)) {
            $icon = implode(' ', $icon);
        }
        return '<i class="fa ' . $icon . '"></i>&nbsp;&nbsp;';

    }

    protected function addScript()
    {
        $event = $this->event();
        if ($event) {
            if (is_callable($event)) {
                $event = $event->call($this);
            }
            $script = <<<SCRIPT
$(function(){
    $(".{$this->getId()}").click(function(e){
        console.log(e);
        var pJax = {$this->pJax()};
        var fn = {$event};
        fn.call(this, e, pJax);        
    });
});
SCRIPT;
            Admin::script($script);
        }
        return $this;
    }

    protected function pJax()
    {
        $token = csrf_token();
        return <<<SCRIPT
function ({
    data = null,
    url = null,
    method = 'POST',
    dataType = 'json',
    callback = null,
}) {
    if (data instanceof FormData) {
        data.append('_token', '{$token}');
    } else {
        let form = document.createElement('form');
        form.innerHTML += `<input type="text" name="_token" value="{$token}">`;
        if (data) {
            for (var i in data) {
                form.innerHTML += '<input type="text" name="'+i+'" value="'+data[i]+'">';
            }
        }
        data = new FormData(form);
    }
    $.ajax({
        url: url,
        data: data,
        method: method,
        dataType: dataType,
        contentType: false,
        processData: false,
        success: res => {
            callback && callback(res);
        },
        fail: err => {
            callback && callback(err);
        },
    })
}
SCRIPT;

    }

    public function render()
    {
        if ($this->hide) {
            return '';
        }

        $this->addScript();

        return <<<HTML
<a href="{$this->getUrl()}" class="btn btn-{$this->getType()} btn-{$this->getSize()} {$this->getClass()} {$this->getId()}" id="{$this->getId()}" title="{$this->getText()}" style="margin:2px;{$this->getStyle()}" {$this->getAttribute()}>{$this->getIcon()}{$this->getText()}</a>
HTML;
    }

    public function __call($name, $arguments)
    {
        if (empty($arguments)) {
            return $this->$name ?? null;
        }
        $this->$name = $arguments[0] ?? null;
        return $this;
    }
}