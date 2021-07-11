<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/1/26
 * Time: 18:25
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList;


use Encore\Admin\Facades\Admin;

abstract class Field
{
    /**
     * @var null
     */
    protected $value = null;

    /**
     * @var string
     */
    protected $column = '';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var string
     */
    protected $parentColumn = '';

    /**
     * @var \Closure|null
     */
    protected $callback = null;

    /**
     * @var string
     */
    protected $width = 'auto';

    /**
     * @var BaseColumn|null
     */
    protected $columnInstance = null;

    /**
     * @var string
     */
    protected $class = '';


    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $subName = '';

    /**
     * @var bool
     */
    protected $showLabelBool = false;

    /**
     * @var bool
     */
    protected $isRowBool = false;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var bool
     */
    protected $hideIconBool = '';

    /**
     * @var bool
     */
    protected $disabledBool = '';

    /**
     * @var bool
     */
    protected $readonlyBool = '';

    /**
     * @var string
     */
    protected $style = '';

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var bool
     */
    protected $required = '';

    /**
     * @var string
     */
    protected $uniqueKey = '';

    /**
     * @var bool
     */
    protected $createEmpty = false;

    /**
     * @var bool
     */
    protected $keyIsVariable = false;

    /**
     * @var string
     */
    protected $keyVariableName = 'key';

    /**
     * @var bool
     */
    protected $disableCheck = false;

    /**
     * @var string
     */
    protected $script = '';

    /**
     * @var string
     */
    protected $oneRowScript = '';

    /**
     * @var string
     */
    protected $asterisk = '';

    /**
     * @return string
     */
    public function getOneRowScript()
    {
        return $this->oneRowScript;
    }

    /**
     * @return string
     */
    public function getColumnClass()
    {
        if (empty($this->column)) {
            return empty($this->label) ? 'empty_column' : $this->label;
        }
        return $this->column;
    }

    /**
     * @return string
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $this->registerEvent();
        if ($content = $this->checkIfCreateEmpty()) {
            return $content;
        }
        if ($this->disableCheck) {
            return $this->build();
        }
        return $this->checkIfShowLabel($this->checkIsRow($this->build()));
    }

    /**
     * @return mixed
     */
    abstract public function build();

    /**
     * Field constructor.
     * @param $instance
     * @param $value
     * @param $column
     * @param $label
     * @param $parentColumn
     * @param \Closure|null $callback
     * @param string $name
     */
    public function __construct($instance, $value, $column, $label, $parentColumn, \Closure $callback = null, $name = null)
    {
        $this->value = $value;
        $this->column = $column;
        $this->parentColumn = $parentColumn;
        $this->callback = $callback;
        $this->width = $instance->width;
        $this->columnInstance = $instance;
        $this->label = $label;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUniqueKey()
    {
        if (empty($this->uniqueKey)) {
            $this->uniqueKey = md5($this->getName() . mt_rand(1000000, 9999999));
        }
        return $this->uniqueKey;
    }


    /**
     * @param bool $yes
     * @param string $name
     * @return $this
     */
    public function keyIsVariable($yes = true, $name = 'key')
    {
        $this->keyIsVariable = $yes;
        $this->keyVariableName = $name;
        return $this;
    }

    /**
     * @param bool $createEmpty
     * @return $this
     */
    public function createEmpty($createEmpty = true)
    {
        $this->createEmpty = $createEmpty;
        return $this;
    }

    /**
     * with回调
     * @param \Closure $closure
     * @return $this
     */
    public function with(\Closure $closure)
    {
        $this->callback = $closure;
        return $this;
    }

    /**
     * @return string
     */
    public function index()
    {
        return $this->column;
    }

    /**
     * @return bool
     */
    public function checkIfCreateEmpty()
    {
        if ($this->createEmpty) {
            if ($this->disableCheck) {
                return $this->buildEmpty();
            }
            return $this->checkIfShowLabel($this->checkIsRow($this->buildEmpty()));
        }
        return false;
    }

    abstract protected function buildEmpty(): string;


    /**
     * @return mixed|null
     */
    public function value()
    {
        if (is_array($this->value)) {
            return $this->value['value'];
        }
        return $this->value;
    }

    /**
     * @param $style
     * @return $this
     */
    public function style($style)
    {
        $this->style = $style;
        return $this;
    }

    /**
     * @param array $attr
     * @return $this
     */
    public function attribute($attr = [])
    {
        $this->attributes = array_merge($this->attributes, $attr);
        return $this;
    }

    /**
     * @param bool $required
     * @return $this
     */
    public function required($required = true)
    {
//        $this->required = $bool ? 'required' : '';
        if ($required) {
            $this->attributes['required'] = true;
            $this->asterisk = 'asterisk';
        }
        return $this;
    }

    /**
     * @param bool $event
     * @return array|null
     */
    public function getEventListener($event = false)
    {
        return $this->columnInstance->getEventListener($event);
    }

    /**
     * 移除事件
     * @param $event
     * @return $this
     */
    public function removeEvent($event)
    {
        $this->columnInstance->removeEvent($event);
        return $this;
    }

    /**
     * @param bool $disabled
     * @return $this
     */
    public function disabled($disabled = true)
    {
//        $this->disabledBool = $disabled ? 'disabled' : '';
        $disabled ? $this->attributes['disabled'] = true : null;
        return $this;
    }

    /**
     * @param bool $readonly
     * @return $this
     */
    public function readonly($readonly = true)
    {
//        $this->readonlyBool = $readonly ? 'readonly' : '';
        $readonly ? $this->attributes['readonly'] = true : null;
        return $this;
    }

    /**
     * @param bool $hide
     * @return $this
     */
    public function hideIcon($hide = true)
    {
        $this->hideIconBool = $hide ? 'none' : '';
        return $this;
    }

    /**
     * 获取当前类名称
     * @return string|null
     */
    public function getClassName()
    {
        if (empty($this->className)) {
            $class = get_class($this);
            $this->className = mb_substr($class, strrpos($class, '\\') + 1);
        }
        return $this->className;
    }

    /**
     * 注册事件监听
     * @return $this
     */
    public function registerEvent()
    {
        $events = $this->getEventListener();
        if ($this->getClassName() == 'PowerSwitch') {
            unset($events['change']);
        }
        $events = json_encode($events);
        $uniqueKey = $this->getUniqueKey();
        $script = <<<SCRIPT
$(function(){
    let events_{$uniqueKey} = {$events};
    for(var event in events_{$uniqueKey}){
        $(document).on(event, "#{$this->getClass()}", function(e){
            var eventFunc = events_{$uniqueKey}[event];
            if(eventFunc){
                eval(`var func = ` + eventFunc + `; func.call(this, e);`)
            }
        });
    }
});
SCRIPT;
        Admin::script($script);
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function options($options = [])
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param bool $show
     * @return $this
     */
    public function showLabel($show = true)
    {
        $this->showLabelBool = $show;
        return $this;
    }

    /**
     * @param bool $bool
     * @return $this
     */
    public function isRow($bool = true)
    {
        $this->isRowBool = $bool;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        $key = $this->columnInstance->key;
        if ($this->keyIsVariable) {
            $key = MultiList::SYMBOL_BEGIN . $this->keyVariableName . MultiList::SYMBOL_END;
        }
        if (empty($this->class)) {
            $this->class = str_replace('[', '-', str_replace(']', '', $this->parentColumn))
                . '-' . $this->column . '-' . $key;
        }
        return $this->class;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        if (empty($this->name)) {
            $this->initName();
        }
        return $this->name;
    }

    /**
     * 初始化字段名称
     * @return $this
     */
    protected function initName()
    {

        if ($this->ifColumnInstance()) {
            return $this;
        }
        $key = $this->columnInstance->key;
        if ($this->keyIsVariable) {
            $key = MultiList::SYMBOL_BEGIN . $this->keyVariableName . MultiList::SYMBOL_END;
        }
        if ($this->parentColumn) {
            $this->name = $this->parentColumn . '[' . $key . '][' . $this->column . ']';
        } else {
            $this->name = $this->column . '[' . $key . ']';
        }
        return $this;
    }

    /**
     * @return bool
     */
    protected function ifColumnInstance()
    {
        if ($this->columnInstance->getClassName() !== 'Column') {
            return false;
        }
        if ($this->parentColumn) {
            $this->name = $this->parentColumn . '[' . $this->column . ']';
        } else {
            $this->name = $this->column;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getSubName()
    {
        $key = $this->columnInstance->key;
        if ($this->keyIsVariable) {
            $key = MultiList::SYMBOL_BEGIN . $this->keyVariableName . MultiList::SYMBOL_END;
        }
        if (empty($this->subName)) {
            if ($this->parentColumn) {
                $this->subName = $this->parentColumn . '[' . $key . '][subs][' . $this->column . ']';
            } else {
                $this->subName = $this->column . '[' . $key . ']';
            }
        }
        return $this->subName;
    }

    /**
     * @return string
     */
    public function renderSub()
    {
        return '';
    }


    /**
     * @param $value
     * @return $this
     */
    public function default($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getValue()
    {
        if ($this->callback) {
            return $this->callback->call($this, $this->value);
        }
        return $this->value;
    }

    /**
     * 获取属性
     * @return array
     */
    public function getAttribute()
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function buildAttribute()
    {
        $attr = '';
        foreach ($this->attributes as $key => $val) {
            $attr .= $key . '="' . $val . '" ';
        }
        return $attr;
    }

    /**
     * @param $content
     * @return string
     */
    public function checkIsRow($content)
    {
        if ($this->isRowBool) {
            $len = $this->columnInstance->getWidth();
            return <<<HTML
<div class="col-sm-{$len}">
    <label class="control-label">{$this->label}</label>
    <div class="col-sm-12" style="padding: 0px;">{$content}</div>
</div>
HTML;
        }
        return $content;
    }

    /**
     * @param $content
     * @return string
     */
    public function checkIfShowLabel($content)
    {
        if ($this->showLabelBool && !$this->isRowBool) {
            return <<<HTML
<div class="form-group">
    <label class="control-label col-sm-2">{$this->label}</label>
    <div class="col-sm-8" >{$content}</div>
</div>
HTML;
        }
        return $content;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->label ? $this->label : $this->column;
    }

    /**
     * @param $name
     * @return |null
     */
    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->$name ?? null;
    }

}
