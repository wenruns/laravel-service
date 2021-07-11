<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2021/7/4
 * Time: 15:46
 */

namespace WenRuns\Laravel;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Show;
use Illuminate\Support\Arr;
use phpDocumentor\Reflection\Types\Self_;

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
 * @package WenRuns\Services
 */
class Button
{
    /**
     * button text
     * @var
     */
    protected $text;

    /**
     * custom class name
     *
     * @var string
     */
    protected $class = '';

    /**
     * button size type
     *
     * @var string
     */
    protected $size = 'xs';

    /**
     * button type
     *
     * @var string
     */
    protected $type = 'default';

    /**
     * button icon
     *
     * @var string
     */
    protected $icon = '';

    /**
     * button's style
     *
     * @var string
     */
    protected $style = '';

    /**
     * button's attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * button's id attribute
     *
     * @var string
     */
    protected $id = '';

    /**
     * button jump link
     *
     * @var string
     */
    protected $url = 'javascript:void(0)';

    /**
     * whether the button is hidden
     *
     * @var bool
     */
    protected $hide = false;

    /**
     * button click event
     *
     * @var null
     */
    protected $eventFn = null;

    /**
     * button size type options
     */
    const SIZE_SM = 'sm';
    const SIZE_XS = 'xs';

    /**
     * button type options
     */
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
        $this->checkAssets();
    }

    public function checkAssets()
    {
        Laravel::loadJs('layui/layui.js');
        Laravel::loadCss('layui/css/layui.css');
        return $this;
    }

    /**
     * to set whether the button is hidden
     *
     * @param bool $value
     * @return $this
     */
    public function hide($value = true)
    {
        $this->hide = $value;
        return $this;
    }

    /**
     * to get the button url
     * @return Button
     */
    protected function getUrl()
    {
        if (empty($this->eventFn())) {
            return $this->url();
        }
        return 'javascript:void(0)';
    }

    /**
     * to get the button type
     *
     * @return Button
     */
    protected function getType()
    {
        return $this->type();
    }

    /**
     * to get button size type
     *
     * @return Button
     */
    protected function getSize()
    {
        return $this->size();
    }

    /**
     * to get the custom class
     *
     * @return Button
     */
    protected function getClass()
    {
        $class = $this->class();
        return is_array($class) ? implode(' ', $class) : $class;
    }

    /**
     * to get the button id attribute
     *
     * @return Button
     */
    protected function getId()
    {
        if (empty($this->id())) {
            $this->id('btn-' . md5($this->text()) . '-' . mt_rand(0, 10000));
        }
        return $this->id();
    }

    /**
     * to get the button text
     *
     * @return Button
     */
    protected function getText()
    {
        return $this->text();
    }

    /**
     * to get the button style
     *
     * @return string|Button
     */
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

    /**
     * to get the button attributes
     *
     * @return string|Button
     */
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

    /**
     * to get the button icon
     *
     * @return string
     */
    protected function getIcon()
    {
        $icon = $this->icon();
        if (empty($icon)) {
            return '';
        }
        if (is_array($icon)) {
            $icon = implode(' ', $icon);
        }
        return '<i class="fa ' . $icon . '"></i>' . (empty($this->text()) ? '' : '&nbsp;&nbsp;');

    }

    /**
     * to set the button click event
     *
     * @return $this
     */
    protected function addScript()
    {
        $event = $this->eventFn();
        if ($event) {
            if (is_callable($event)) {
                $event = $event->call($this);
            }
            $pjax = self::pJax();
            $script = <<<SCRIPT
$(function(){
    $(".{$this->getId()}").click(function(e){
        e.preventDefault();
        var pJax = {$pjax};
        var fn = {$event};
        fn.call(this, e, pJax);
    });
});
SCRIPT;
            Admin::script($script);
        }
        return $this;
    }

    /**
     * the pJax request function
     *
     * @return string
     */
    public static function pJax()
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

    /**
     * output the button generates result
     *
     * @return string
     */
    public function render()
    {
        if ($this->hide) {
            return '';
        }

        $this->addScript();

        return <<<HTML
<a href="{$this->getUrl()}"
   data-uri="{$this->url()}"
   class="btn btn-{$this->getType()} btn-{$this->getSize()} {$this->getClass()} {$this->getId()}"
   id="{$this->getId()}"
   title="{$this->getText()}"
   style="margin:2px;{$this->getStyle()}"
   {$this->getAttribute()}>
        {$this->getIcon()}{$this->getText()}
</a>
HTML;
    }

    /**
     *
     * @param $name
     * @param $arguments
     * @return $this|null
     */
    public function __call($name, $arguments)
    {
        if (empty($arguments)) {
            return $this->$name ?? null;
        }
        $this->$name = $arguments[0] ?? null;
        return $this;
    }

    /**
     * statically method to create the button
     *
     * @param array $options
     * @param bool $toString
     * @return array|string|static
     */
    public static function create(array $options, $toString = true)
    {
        $buttons = $toString ? '' : null;
        if (isset($options[0])) {
            foreach ($options as $k => $item) {
                $button = new static(Arr::get($item, 'text'), $item);
                if ($toString) {
                    $buttons .= $button->render();
                } else {
                    $buttons[] = $button;
                }
            }
        } else {
            $button = new static(Arr::get($options, 'text'), $options);
            if ($toString) {
                $buttons = $button->render();
            } else {
                $buttons = $button;
            }
        }
        return $buttons;
    }

    const SWAL_OPTIONS = [
        'type' => 'info',
        'title' => '确认删除？',
        'confirmButtonText' => '确定',
        'showCancelButton' => true,
        'cancelButtonText' => '取消',
    ];

    /**
     * 弹窗，执行pJax
     * @param array $swalOptions
     * @param array $pJaxOptions
     * @param string $script
     * @return string
     */
    public static function eventSwal(array $swalOptions = self::SWAL_OPTIONS, array $pJaxOptions = [], $script = '')
    {
        $swalOptions = json_encode($swalOptions);
        $pJaxOptions = json_encode($pJaxOptions);
        return <<<SCRIPT
function(e, pJax){
    swal.fire({$swalOptions}).then(function(isConfirm){
        if(isConfirm.value){
            let options = {$pJaxOptions};
            if(options.url){
                if(typeof options.callback == 'undefined'){
                    options.callback = function(res){
                        if(res.status){
                            $.pjax.reload("#pjax-container");
                            toastr.success(res.message);
                        }else{
                            toastr.error(res.message);
                        }
                    }
                }
                pJax(options);
            }
        }
    });
}
SCRIPT;
    }


    /**
     * html代码处理
     * @param $html
     * @return array|string|string[]|null
     */
    public static function htmlHandle($html)
    {
        return preg_replace(array("/\r\n|\r|\n/m", '/<\//m'), array(' ', '<\/'), $html);
    }

    /**
     * 自定义css加载
     */
    protected static function cssHandle()
    {
        $script = <<<SCRIPT
$(function(){
    let styleEle = document.createElement("style");
    styleEle.innerHTML = `
        @keyframes swal2loading{
            0%{
                transform: rotate(0deg);
            }
            100%{
                transform: rotate(-360deg);
            }
        }
        @-webkit-keyframes loading{
            0%{
                transform: rotate(0deg);
            }
            100%{
                transform: rotate(-360deg);
            }
        }
        .select2-container.select2-container--default.select2-container--open{
            z-index: 999999999999;
        }
        .layui-layer-content .iframe-loading svg{
            animation: swal2loading 1.2s infinite ease-in-out;
            -webkit-animation: swal2loading 1.2s infinite ease-in-out;
        }

        .layui-layer-content .icheck{
            margin: 0px 20px;
        }
    `;
    document.querySelector('head').append(styleEle);
});
SCRIPT;
        Admin::script($script);
    }

    /**
     * 弹出form表单
     * @param Form $form
     * @param string $script
     * @param string $width
     * @param string $height
     * @return string
     */
    public static function eventForm(Form $form, $options = [])
    {
        $options['title'] = $options['title'] ?? '表单';
        $options = self::checkLayerOptions($options);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();
        });
        $form->footer(function (Form\Footer $footer) {
            $footer->disableEditingCheck();
            $footer->disableViewCheck();
            $footer->disableCreatingCheck();
        });
        self::cssHandle();
        $fields = $form->builder()->fields();
        $formHtml = self::htmlHandle($form->render());
        $fields->each(function ($field) use (&$options) {
            $field->render();
            $options['script'] .= $field->getScript() . ' ';
        });
        $area = is_array($options['area']) ? json_encode($options['area']) : $options['area'];

        return <<<SCRIPT
function(e, pJax){
    layer.open({
        type: 1,
        title: '{$options['title']}',
        offset: '{$options['offset']}',
        area: {$area},
        shadeClose: {$options['shadeClose']}, //点击遮罩关闭
        content: '{$formHtml}',
        anim: {$options['anim']}, //平滑放大。默认
        shade: {$options['shade']},
        maxmin: {$options['maxmin']},
        success: function(layer0, index){
            $('.layui-layer-content form button[type="submit"]').click(function(event){
                try{
                    let form = document.querySelector(".layui-layer-content form");
                    let requiredEles = form.querySelectorAll("[name][required]");
                    requiredEles.forEach(ele=>{
                        if(!ele.value){
                            throw new Error(ele.name+'的值不能为空');
                        }
                    });
                    var e = event || window.event;
                    e.preventDefault();
                    let formData = new FormData(form);
                    let url = form.getAttribute("action");
                    pJax({
                        url: url,
                        data: formData,
                        method: form.getAttribute("method"),
                        callback: function(res){
                            if(res.status){
                                layer.closeAll();
                                $.pjax.reload("#pjax-container");
                                toastr.success(res.message);
                            }else{
                                toastr.error(res.message);
                            }
                        },
                    });
                    return false;
                }catch(e){
                }
            });
            {$options['script']}
        },
    });
}
SCRIPT;
    }


    /**
     * 弹出show表单
     * @param Show $show
     * @param string $script
     * @param string $width
     * @param string $height
     * @return string
     */
    public static function eventShow(Show $show, $options = [])
    {
        $options['title'] = $options['title'] ?? '详情';
        $options = self::checkLayerOptions($options);
        $show->panel()->tools(function (Show\Tools $tools) {
            $tools->disableDelete();
            $tools->disableList();
            $tools->disableEdit();
        });
        $html = self::htmlHandle($show->render());
        $area = is_array($options['area']) ? json_encode($options['area']) : $options['area'];
        return <<<SCRIPT
function(e, pJax){
    layer.open({
        type: 1,
        title: '{$options['title']}',
        offset: '{$options['offset']}',
        area: {$area},
        shadeClose: {$options['shadeClose']}, //点击遮罩关闭
        content: '{$html}',
        anim: {$options['anim']}, //平滑放大。默认
        shade: {$options['shade']},
        maxmin: {$options['maxmin']},
        success: function(layer0, index){
            {$options['script']}
        }
    });
}
SCRIPT;
    }

    protected static function checkLayerOptions(array $options)
    {
        return [
            'submitEvent' => $options['submit'] ?? '',
            'script' => $options['script'] ?? '',
            'area' => $options['area'] ?? ['95%', '95%'],
            'offset' => $options['offset'] ?? '2%',
            'anim' => $options['anim'] ?? 2,
            'title' => $options['title'] ?? false,
            'shadeClose' => $options['shadeClose'] ?? true,
            'shade' => $options['shade'] ?? 0.3,
            'maxmin' => $options['maxmin'] ?? true, //开启最大化最小化按钮
        ];
    }


    /**
     * iframe方式加载页面
     * @param $url
     * @param string $submitEvent
     * @param string $width
     * @param string $height
     * @return string
     */
    public static function eventIframe($url, $options = [])
    {
        $options['title'] = $options['title'] ?? $url;
        $options = self::checkLayerOptions($options);
        $area = is_array($options['area']) ? json_encode($options['area']) : $options['area'];
        self::cssHandle();
        return <<<SCRIPT
function(e, pJax){
    let index = layer.open({
        type: 2,
        title: '{$options['title']}',
        offset: '{$options['offset']}',
        area: {$area},
        shadeClose: {$options['shadeClose']}, //点击遮罩关闭
        content: '{$url}',
        anim: {$options['anim']}, //平滑放大。默认
        shade: {$options['shade']},
        maxmin: {$options['maxmin']},
        success: function(layer0, index){
            let ele = layer0[0];
            let iframe = ele.querySelector('iframe');
            let iframeDocument = iframe.contentDocument || window.frames[iframe.name].document || ele;
            $(iframeDocument).on('click','form button[type="submit"]',function(event){
                var submit = function(){
                    try{
                        let form = event.currentTarget.parentElement;
                        while(form.tagName != 'FORM'){
                            form = form.parentElement;
                        }
                        let requiredEles = form.querySelectorAll("[name][required]");
                        requiredEles.forEach(ele=>{
                            if(!ele.value){
                                throw new Error(ele.name+'的值不能为空');
                            }
                        });
                        var e = event || window.event;
                        e.preventDefault();
                        let formData = new FormData(form);
                        let url = form.getAttribute("action");
                        pJax({
                            url: url,
                            data: formData,
                            method: form.getAttribute("method"),
                            callback: function(res){
                                if(res.status){
                                    layer.closeAll();
                                    $.pjax.reload("#pjax-container");
                                    toastr.success(res.message);
                                }else{
                                    toastr.error(res.message);
                                }
                            },
                        });
                        return false;
                    }catch(e){
                        console.error(e);
                    }
                }
                let submitEvent = `{$options['submitEvent']}`;
                if(submitEvent){
                    eval(`var fn = ` + submitEvent);
                    var close = function(){
                        layer.close(index);
                    }
                    fn.call(this, {e:event, pJax, submit, close});
                }else{
                    submit();
                }
            });
        }
    });
}
SCRIPT;
    }


}
