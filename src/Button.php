<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2021/7/4
 * Time: 15:46
 */

namespace WenRuns\Service;

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
 * @package WenRuns\Service
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
            z-index: 10000;
        }
        .swal2-popup #swal2-content{
            text-align: left !important;
        }
        .swal2-popup .iframe-loading svg{
            animation: swal2loading 1.2s infinite ease-in-out;
            -webkit-animation: swal2loading 1.2s infinite ease-in-out;
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
     * @return string
     */
    public static function eventForm(Form $form, $script = '', $width = '80%')
    {
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
        $fields->each(function ($field) use (&$script) {
            $field->render();
            $script .= $field->getScript() . ' ';
        });
        $moveHeader = self::moveHeader();
        $closeButton = self::closeButton();
        $formSubmit = self::formSubmit();
        return <<<SCRIPT
function(e, pJax){
    swal.fire({
        showConfirmButton: false,
        showCancelButton: false,
        html: '{$formHtml}',
        width: '{$width}',
        animation: "slide-from-top",
    });
    {$closeButton}
    {$moveHeader}
    {$formSubmit}
    {$script}
}
SCRIPT;
    }

    protected static function formSubmit()
    {
        return <<<SCRIPT
function formSubmit(){
    $('.swal2-content form button[type="submit"]').click(function(event){
        try{
            let form = document.querySelector(".swal2-content form");
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
                        swal.close();
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
}
formSubmit();
SCRIPT;

    }

    /**
     * 将show表单头或form表单头移动到swal2的头元素中
     * @return string
     */
    protected static function moveHeader()
    {
        return <<<SCRIPT
function moveHeader(){
    let swalHeader = document.querySelector(".swal2-header");
    let formHeader = document.querySelector(".swal2-content .box-header");
    swalHeader.append(formHeader);
    formHeader.style.width = '100 % ';
}
moveHeader();
SCRIPT;

    }

    /**
     * 弹出show表单
     * @param Show $show
     * @param string $script
     * @param string $width
     * @return string
     */
    public static function eventShow(Show $show, $script = '', $width = '80%')
    {
        $show->panel()->tools(function (Show\Tools $tools) {
            $tools->disableDelete();
            $tools->disableList();
            $tools->disableEdit();
        });
        $html = self::htmlHandle($show->render());
        $moveHeader = self::moveHeader();
        $closeButton = self::closeButton();
        return <<<SCRIPT
function(e, pJax){
    swal.fire({
        showConfirmButton: false,
        showCancelButton: false,
        html: '{$html}',
        width: '{$width}',
        animation: "slide-from-top",
    });
    {$closeButton}
    {$moveHeader}
    {$script}
}
SCRIPT;
    }

    /**
     * 关闭按钮
     * @return string
     */
    protected static function closeButton($top = '-20px', $right='-20px')
    {
        return <<<SCRIPT
function closeButton(){
    let swalHeader = document.querySelector(".swal2-header");
    swalHeader.style.position = 'relative';
    swalHeader.innerHTML = '';
    let closeBtn = document.createElement('i');
    swalHeader.append(closeBtn);
    closeBtn.classList.value = 'fa fa-times';
    closeBtn.style.position = 'absolute';
    closeBtn.style.top = "{$top}";
    closeBtn.style.right = "{$right}";
    closeBtn.style.padding = "4px 5px";
    closeBtn.style.border = "1px solid rgba(0, 0, 0, 0.4)";
    closeBtn.style["border-radius"] = "20px";
    closeBtn.style.background = "white";
    closeBtn.style.cursor = "pointer";
    closeBtn.style["z-index"] = "999";
    closeBtn.style.color = "red";
    $(closeBtn).click(function(e){
        swal.close();
    });
}
closeButton();
SCRIPT;

    }

    /**
     * iframe方式加载页面
     * @param $url
     * @param string $script
     * @param string $width
     * @return string
     */
    public static function eventIframe($url, $script = '', $width = '80%')
    {
        self::cssHandle();
        $html = self::htmlHandle(self::iframeHtml($url));
        $iframeScript = self::iframeScript();
        $closeButton = self::closeButton();
        return <<<SCRIPT
function(e, pJax){
    swal.fire({
        showConfirmButton: false,
        showCancelButton: false,
        html: `{$html}`,
        width: '{$width}',
        animation: "slide-from-top",
    });
    {$iframeScript}
    {$closeButton}
    {$script}
}
SCRIPT;
    }

    /**
     * iframe加载完成后js处理
     * @return string
     */
    protected static function iframeScript()
    {
        $formSubmit = self::formSubmit();
        return <<<SCRIOPT
function iframeScript(){
    let iframeContent = document.querySelector(".swal2-content #iframe-content");
    let iframe = iframeContent.querySelector("iframe");
    let loadingEle = document.querySelector(".swal2-content .iframe-loading");

    function afterOnload(e){
        // 获取iframe的document对象
        let iframeDocument = iframe.contentDocument || window.frames["iframe-swal2"].document;
        // 获取需要执行的script脚本
        let scripts = iframeDocument.querySelectorAll(".content-wrapper script[data-exec-on-popstate]");
        // 获取用户自定义css
        let styles = iframeDocument.querySelectorAll(".content-wrapper style[type='text/css']");
        // 加载用户自定义css
        styles.forEach((ele)=>{
            iframeContent.append(ele);
        });
        // 新建iframe内容容器
        let content = document.createElement("div");
        // 获取iframe的内容元素
        let iframeContentEle = iframeDocument.querySelector("#app .content .box.box-info");
        // 更新iframe内容容器的属性
        Array.from(iframeContentEle.attributes).forEach((item)=>{
            content.setAttribute(item.name, item.value);
        });
        // 更新iframe内容容器的内容
        content.innerHTML = iframeContentEle.innerHTML;
        // 加载iframe内容
        iframeContent.append(content);
        loadingEle.style.display = 'none';
        // form表单select控件元素
        let selectEles = content.querySelectorAll("select[name]");
        // form表单number控件元素
        let numberEles = content.querySelectorAll("input.initialized[name]");
        // 去掉iframe中生成的select控件代码
        selectEles.forEach((ele)=>{
            ele.parentElement.innerHTML = ele.outerHTML;
        });
        // 去掉iframe中生成的number控件代码
        numberEles.forEach(ele=>{
            ele.classList.remove("initialized");
            ele.parentElement.innerHTML = ele.outerHTML;
        });
        // 执行script脚本
        scripts.forEach((script)=>{
            eval(script.innerHTML);
            console.log(script.innerHTML);
        });
        iframe.remove();
        {$formSubmit}
    }
    if (iframe.attachEvent) {
        iframe.attachEvent("onload", function(e) {
            //iframe加载完成后你需要进行的操作
            afterOnload(e);
        });
    } else {
        iframe.onload = function(e) {
            //iframe加载完成后你需要进行的操作
            afterOnload(e);
        };
    }
}
iframeScript();
SCRIOPT;

    }

    /**
     *  iframe页面
     * @param $url
     * @return string
     */
    protected static function iframeHtml($url)
    {
        return <<<HTML
<div id="iframe-content" style="min-height: 300px;display: flex;align-items: center;justify-content: center;">
    <iframe name="iframe-swal2" id="iframe-swal2" src="{$url}" frameborder="0" width="100%" hidden></iframe>
    <div class="iframe-loading" style="text-align: center;padding-top: 20px;">
        <svg t="1625624104640"
             class="icon"
             viewBox="0 0 1024 1024"
             version="1.1"
             xmlns="http://www.w3.org/2000/svg"
             p-id="1900"
             width="64"
             height="64">
            <path d="M511.505 0C238.13 0 16.516 222.175 16.516 496.244c0 274.068 221.614 496.243 494.989 496.243 273.374 0 494.988-222.175 494.988-496.243C1006.493 222.175 784.913 0 511.505 0z m0 942.84c-246.058 0-445.507-199.945-445.507-446.63 0-246.684 199.45-446.629 445.507-446.629 246.057 0 445.506 199.945 445.506 446.63 0 246.684-199.45 446.629-445.506 446.629z" p-id="1901"></path><path d="M514.709 281.963c-51.167 0-102.235 19.853-141.246 59.558-38.945 39.671-58.5 91.73-58.5 143.723 0 51.993 19.522 104.051 58.5 143.723 39.01 39.705 90.079 59.557 141.246 59.557 51.1 0 102.168-19.852 141.18-59.557 38.945-39.672 58.467-91.73 58.467-143.723 0-51.993-19.522-104.052-58.467-143.723-39.012-39.705-90.08-59.558-141.18-59.558z m14.336 47.6c3.336-12.618 16.152-20.183 28.606-16.78 12.486 3.402 19.819 16.416 16.483 29.068-3.337 12.684-16.087 20.249-28.54 16.846-12.486-3.402-19.886-16.417-16.55-29.134zM373.198 546.618c-12.387 3.435-25.203-4.063-28.54-16.748-3.336-12.684 3.997-25.732 16.484-29.134 12.486-3.37 25.27 4.129 28.606 16.846 3.336 12.652-4.063 25.633-16.55 29.036z m16.417-154.327c-9.116-9.282-9.116-24.312 0-33.594 9.15-9.282 23.883-9.282 33 0 9.084 9.282 9.084 24.312 0 33.594-9.117 9.315-23.883 9.315-33 0zM496.31 638.282c-3.337 12.619-16.153 20.183-28.54 16.78-12.486-3.402-19.886-16.416-16.55-29.068 3.337-12.684 16.153-20.248 28.607-16.846 12.42 3.402 19.819 16.417 16.483 29.134z m-52.224-84.463c-37.888-38.648-37.888-101.145 0-139.727a95.9 95.9 0 0 1 137.249 0c37.888 38.582 37.888 101.112 0 139.727a95.9 95.9 0 0 1-137.25 0z m191.752 55.329c-9.15 9.249-23.948 9.249-33 0-9.116-9.282-9.116-24.345 0-33.594 9.085-9.315 23.883-9.315 33 0 9.05 9.25 9.05 24.279 0 33.594z m44.924-171.173c3.336 12.717-4.063 25.732-16.55 29.134-12.386 3.37-25.203-4.129-28.54-16.846-3.335-12.652 4.064-25.7 16.484-29.102 12.486-3.336 25.27 4.13 28.606 16.814zM210.085 197.4l21.67 142.006 36.6-5.681-21.67-142.039-36.6 5.715zM398.006 863.794l23.221 29.431 109.238-89.484-23.222-29.432-109.237 89.485zM777.546 308.984l-13.41 35.18 129.981 51.265 13.411-35.18-129.982-51.265zM115.514 606.637l131.303 51.795 13.411-35.18-131.303-51.794-13.411 35.18zM627.613 103.853l-23.189-29.431-105.769 86.643 23.189 29.399 105.769-86.61zM756.934 636.201l21.273 139.925 36.699-5.78L793.6 630.387l-36.666 5.813z" p-id="1902"></path>
        </svg>
        <div style="color: grey;padding: 20px;font-size: 18px;">努力加载中...</div>
    </div>
</div>
HTML;
    }

    public static function eventPageIframe($url = null, $submitEvent = '', $width = '80%')
    {
        self::cssHandle();
        $closeButton = self::closeButton('-10px', '-10px');
        $html = self::htmlHandle(self::pageIframeHtml());
        return <<<SCRIPT
function(e, pJax){
    let url = `{$url}`;
    if(!url){
        url = e.currentTarget.href;
    }
    console.log(url, e.currentTarget);
    swal.fire({
        showConfirmButton: false,
        showCancelButton: false,
        html: `{$html}`,
        width: '{$width}',
    });
    document.querySelector(".swal2-popup.swal2-modal.swal2-show").style.padding = '2px';
    let iframe = document.getElementById("swal2-page-iframe");
    function iframeLoaded(e){
        let iframeDocument = iframe.contentDocument || window.frames["iframe-swal2"].document;
        let pJaxContainerEle = iframeDocument.querySelector("#pjax-container #app");
        let documentHeight = pJaxContainerEle.offsetHeight;
        iframe.height = documentHeight + 'px';

//
//      // Firefox和Chrome早期版本中带有前缀
//var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver
//
//// 选择目标节点
//var target = document.querySelector('#some-id');
//
//// 创建观察者对象
//var observer = new MutationObserver(function(mutations) {
//    console.log(111, mutations);
//});
//
//// 配置观察选项:
//var config = { attributes: true, childList: true, characterData: true }
//
//// 传入目标节点和观察选项
//observer.observe(pJaxContainerEle, config);
//
//// 随后,你还可以停止观察
////observer.disconnect();


        document.querySelector(".swal2-page-iframe-loading").style.display = 'none';
        let submitEvent = `{$submitEvent}`
        if(submitEvent){
            let submitButtons = iframeDocument.querySelectorAll("button[type='submit']");
            submitButtons.forEach(button=>{
                button.addEventListener('click', function(event){
                    try{
                        let form = event.currentTarget.parentElement;
                        while(form.tagName != 'FORM'){
                            form = form.parentElement;
                        }
                        let requiredEles = form.querySelectorAll("[name][required]");
                        requiredEles.forEach(ele => {
                            if(!ele.value){
                                throw new Error(ele.name+' 不能为空！');
                            }
                        });
                        e = event || window.event;
                        e.preventDefault();
                        eval('let fn = '+submitEvent);
                        fn.call(this, form, pJax);
                        return false;
                    }catch(e){
                        console.info(e);
                    }
                });
            });
        }
    }
    if (iframe.attachEvent) {
        iframe.attachEvent("onload", function(e) {
            //iframe加载完成后你需要进行的操作
            iframeLoaded(e);
        });
    } else {
        iframe.onload = function(e) {
            //iframe加载完成后你需要进行的操作
            iframeLoaded(e);
        };
    }
    {$closeButton}
}
SCRIPT;
    }

    protected static function pageIframeHtml(){
        return <<<HTML
<iframe name="swal2-page-iframe" id="swal2-page-iframe" src="`+url+`" frameborder="0"  width="100%" scrolling="no" height="0px"></iframe>
<div class="swal2-page-iframe-loading" style="min-height: 50vh;display: flex;justify-content: center;align-items: center;">
     <div class="iframe-loading" style="text-align: center;padding-top: 20px;">
         <svg t="1625624104640"
              class="icon"
              viewBox="0 0 1024 1024"
              version="1.1"
              xmlns="http://www.w3.org/2000/svg"
              p-id="1900"
              width="64"
              height="64">
             <path d="M511.505 0C238.13 0 16.516 222.175 16.516 496.244c0 274.068 221.614 496.243 494.989 496.243 273.374 0 494.988-222.175 494.988-496.243C1006.493 222.175 784.913 0 511.505 0z m0 942.84c-246.058 0-445.507-199.945-445.507-446.63 0-246.684 199.45-446.629 445.507-446.629 246.057 0 445.506 199.945 445.506 446.63 0 246.684-199.45 446.629-445.506 446.629z" p-id="1901"></path><path d="M514.709 281.963c-51.167 0-102.235 19.853-141.246 59.558-38.945 39.671-58.5 91.73-58.5 143.723 0 51.993 19.522 104.051 58.5 143.723 39.01 39.705 90.079 59.557 141.246 59.557 51.1 0 102.168-19.852 141.18-59.557 38.945-39.672 58.467-91.73 58.467-143.723 0-51.993-19.522-104.052-58.467-143.723-39.012-39.705-90.08-59.558-141.18-59.558z m14.336 47.6c3.336-12.618 16.152-20.183 28.606-16.78 12.486 3.402 19.819 16.416 16.483 29.068-3.337 12.684-16.087 20.249-28.54 16.846-12.486-3.402-19.886-16.417-16.55-29.134zM373.198 546.618c-12.387 3.435-25.203-4.063-28.54-16.748-3.336-12.684 3.997-25.732 16.484-29.134 12.486-3.37 25.27 4.129 28.606 16.846 3.336 12.652-4.063 25.633-16.55 29.036z m16.417-154.327c-9.116-9.282-9.116-24.312 0-33.594 9.15-9.282 23.883-9.282 33 0 9.084 9.282 9.084 24.312 0 33.594-9.117 9.315-23.883 9.315-33 0zM496.31 638.282c-3.337 12.619-16.153 20.183-28.54 16.78-12.486-3.402-19.886-16.416-16.55-29.068 3.337-12.684 16.153-20.248 28.607-16.846 12.42 3.402 19.819 16.417 16.483 29.134z m-52.224-84.463c-37.888-38.648-37.888-101.145 0-139.727a95.9 95.9 0 0 1 137.249 0c37.888 38.582 37.888 101.112 0 139.727a95.9 95.9 0 0 1-137.25 0z m191.752 55.329c-9.15 9.249-23.948 9.249-33 0-9.116-9.282-9.116-24.345 0-33.594 9.085-9.315 23.883-9.315 33 0 9.05 9.25 9.05 24.279 0 33.594z m44.924-171.173c3.336 12.717-4.063 25.732-16.55 29.134-12.386 3.37-25.203-4.129-28.54-16.846-3.335-12.652 4.064-25.7 16.484-29.102 12.486-3.336 25.27 4.13 28.606 16.814zM210.085 197.4l21.67 142.006 36.6-5.681-21.67-142.039-36.6 5.715zM398.006 863.794l23.221 29.431 109.238-89.484-23.222-29.432-109.237 89.485zM777.546 308.984l-13.41 35.18 129.981 51.265 13.411-35.18-129.982-51.265zM115.514 606.637l131.303 51.795 13.411-35.18-131.303-51.794-13.411 35.18zM627.613 103.853l-23.189-29.431-105.769 86.643 23.189 29.399 105.769-86.61zM756.934 636.201l21.273 139.925 36.699-5.78L793.6 630.387l-36.666 5.813z" p-id="1902"></path>
         </svg>
         <div style="color: grey;padding: 20px;font-size: 18px;">努力加载中...</div>
     </div>
</div>
HTML;
    }

}
