<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/1/26
 * Time: 16:54
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList;


use WenRuns\Laravel\Admin\Form\Field\MultiList\Field\Color;
use WenRuns\Laravel\Admin\Form\Field\MultiList\Field\Date;
use WenRuns\Laravel\Admin\Form\Field\MultiList\Field\Decimal;
use WenRuns\Laravel\Admin\Form\Field\MultiList\Field\Display;
use WenRuns\Laravel\Admin\Form\Field\MultiList\Field\Expand;
use WenRuns\Laravel\Admin\Form\Field\MultiList\Field\Hidden;
use WenRuns\Laravel\Admin\Form\Field\MultiList\Field\Modal;
use WenRuns\Laravel\Admin\Form\Field\MultiList\Field\MultiSelect;
use WenRuns\Laravel\Admin\Form\Field\MultiList\Field\Number;
use WenRuns\Laravel\Admin\Form\Field\MultiList\Field\PowerSwitch;
use WenRuns\Laravel\Admin\Form\Field\MultiList\Field\Select;
use WenRuns\Laravel\Admin\Form\Field\MultiList\Field\Text;
use WenRuns\Laravel\Admin\Form\Field\MultiList\Field\TextArea;

trait CommonMethods
{
    /**
     * @var string
     */
    private $className = '';

    /**
     * @var array
     */
    protected $events = [];

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
     * 获取当前类的名称
     * @return string
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
     * 文本输入框
     * @param $column
     * @param $label
     * @param null $callback
     * @param bool $createEmpty
     * @return BaseColumn|Field|MultiList|Row|TableList
     */
    public function text($column, $label, $callback = null)
    {
        return $this->doFilter($column, $label, $callback, 'text', Text::class);
    }


    public function decimal($column, $label, $callback = null)
    {
        return $this->doFilter($column, $label, $callback, 'decimal', Decimal::class);
    }

    /**
     * 数字编辑框
     * @param $column
     * @param $label
     * @param null $callback
     * @param bool $createEmpty
     * @return BaseColumn|Field|MultiList|Row|TableList
     */
    public function number($column, $label, $callback = null)
    {
        return $this->doFilter($column, $label, $callback, 'number', Number::class);
    }

    /**
     * 下拉选择框
     * @param $column
     * @param $label
     * @param \Closure|null $callback
     * @param bool $createEmpty
     * @return BaseColumn|Field|MultiList|Row|TableList
     */
    public function select($column, $label, \Closure $callback = null)
    {
        return $this->doFilter($column, $label, $callback, 'select', Select::class);
    }

    /**
     * 下拉多选框
     * @param $column
     * @param $label
     * @param \Closure|null $callback
     * @param bool $createEmpty
     * @return BaseColumn|Field|MultiList|Row|TableList
     */
    public function multiSelect($column, $label, \Closure $callback = null)
    {
        return $this->doFilter($column, $label, $callback, 'multiSelect', MultiSelect::class);
    }

    /**
     * 日期选择框
     * @param $column
     * @param $label
     * @param \Closure|null $callback
     * @param bool $createEmpty
     * @return BaseColumn|Field|MultiList|Row|TableList
     */
    public function date($column, $label, \Closure $callback = null)
    {
        return $this->doFilter($column, $label, $callback, 'date', Date::class);
    }

    /**
     * 开关选择框
     * @param $column
     * @param $label
     * @param \Closure|null $callback
     * @param bool $createEmpty
     * @return BaseColumn|Field|MultiList|Row|TableList
     */
    public function switch($column, $label, \Closure $callback = null)
    {
        return $this->doFilter($column, $label, $callback, 'switch', PowerSwitch::class);
    }

    /**
     * 颜色选择框
     * @param $column
     * @param $label
     * @param \Closure|null $callback
     * @param bool $createEmpty
     * @return BaseColumn|Field|MultiList|Row|TableList
     */
    public function color($column, $label, \Closure $callback = null)
    {
        return $this->doFilter($column, $label, $callback, 'color', Color::class);
    }


    /**
     * 文本区域框
     * @param $column
     * @param $label
     * @param \Closure|null $callback
     * @param bool $createEmpty
     * @return BaseColumn|Field|MultiList|Row|TableList
     */
    public function textArea($column, $label, \Closure $callback = null)
    {
        return $this->doFilter($column, $label, $callback, 'textArea', TextArea::class);
    }


    /**
     * 模态框
     * @param $column
     * @param $label
     * @param \Closure $callback
     * @param bool $createEmpty
     * @return BaseColumn|Field|MultiList|Row|TableList
     */
    public function modal($column, $label, \Closure $callback)
    {
        return $this->doFilter($column, $label, $callback, 'modal', Modal::class);
    }

    /**
     * 伸缩框
     * @param $column
     * @param $label
     * @param \Closure $callback
     * @param bool $createEmpty
     * @return BaseColumn|Field|MultiList|Row|TableList
     */
    public function expand($column, $label, \Closure $callback)
    {
        return $this->doFilter($column, $label, $callback, 'expand', Expand::class);
    }

    /**
     * 展示框
     * @param $column
     * @param $label
     * @param \Closure|null $callback
     * @param bool $createEmpty
     * @return BaseColumn|Field|MultiList|Row|TableList
     */
    public function display($column, $label, \Closure $callback = null)
    {
        return $this->doFilter($column, $label, $callback, 'display', Display::class);
    }

    /**
     * 文本隐藏框
     * @param $column
     * @param $label
     * @param null $callback
     * @param bool $createEmpty
     * @return BaseColumn|Field|MultiList|Row|TableList
     */
    public function hidden($column, $label, $callback = null)
    {
        return $this->doFilter($column, $label, $callback, 'hidden', Hidden::class);
    }

    /**
     * js事件添加
     * @param $event
     * @param $jsCallback
     * @return $this
     */
    public function eventListener($event, $jsCallback)
    {
        $this->events[$event] = $this->compressHtml($jsCallback);
        return $this;
    }

    /**
     * 移除事件
     * @param $event
     * @return $this
     */
    public function removeEvent($event)
    {
        unset($this->events[$event]);
        return $this;
    }

    /**
     * 压缩html
     * @param $string
     * @return string
     */
    protected function compressHtml($string)
    {
        return ltrim(rtrim(preg_replace(array("/> *([^ ]*) *</", "//", "'/\*[^*]*\*/'", "/\r\n/", "/\n/", "/\t/", '/>[ ]+</', '/`/'),
            array(">\\1<", '', '', '', '', '', '><', '\`'), $string)));
    }


    /**
     * 获取事件
     * @param bool $event
     * @return array|null
     */
    public function getEventListener($event = false)
    {
        return $event === false ? $this->events : ($this->events[$event] ?? null);
    }

    static public $a = 1;

    /**
     * 执行过滤功能
     * @param $column
     * @param $label
     * @param $callback
     * @param $type
     * @param null $typeClass
     * @param bool $createEmpty
     * @return $this|BaseColumn
     */
    protected function doFilter($column, $label, $callback, $type, $typeClass = null)
    {
        switch ($this->getClassName()) {
            case 'MultiList':
                $tableList = $this->getTableList()
                    ->keyIsVariable($this->keyIsVariable, $this->keyVariableName)
                    ->createEmpty($this->createEmpty)
                    ->$type($column, $label, $callback);
                return $tableList;
            case 'TableList':
                $subItems = $this->value['subs'] ?? null;
                if (!empty($subItems)) {
                    $subItems = $subItems[$column] ?? null;
                }
                $columnInstance = new BaseColumn($this->multiList, $this, $column, $label, $type, $this->parentColumn, $callback);
                $this->columns[] = $columnInstance->hideIcon()
                    ->keyIsVariable($this->keyIsVariable, $this->keyVariableName)
                    ->createEmpty($this->createEmpty)
                    ->subItems($subItems);
                return $columnInstance;
            case 'BaseColumn':
                $instance = new $typeClass($this, $this->defaultValue, $column, $label, $this->parentColumn, $callback);
                $this->withCallback($instance);
                $instance->options($this->optionsArr)
                    ->style($this->style)
                    ->attribute($this->attributes)
                    ->hideIcon($this->hideIconBool)
                    ->readonly($this->readonlyBool)
                    ->default($this->defaultValue)
                    ->required($this->required)
                    ->createEmpty($this->createEmpty)
                    ->keyIsVariable($this->keyIsVariable, $this->keyVariableName)
                    ->disabled($this->disabledBool);
                return $instance;
            case 'Column':
                $instance = new $typeClass($this, $this->defaultValue, $column, $label, $this->parentColumn, $callback);
                $this->withCallback($instance);
                $this->columns[] = $instance->default($this->defaultValue)
                    ->createEmpty($this->createEmpty)
                    ->keyIsVariable($this->keyIsVariable, $this->keyVariableName)
                    ->showLabel();
                return $instance;
            case 'Row':
//                dd('row', $this);
                $name = empty($this->parentColumn) ? $column : $this->parentColumn . '[' . $column . ']';
                $instance = new $typeClass($this, $this->defaultValue, $column, $label, $this->parentColumn, $callback, $name);
                $this->withCallback($instance);
                $this->columns[] = $instance->default($this->defaultValue)
                    ->createEmpty($this->createEmpty)
                    ->keyIsVariable($this->keyIsVariable, $this->keyVariableName)
                    ->showLabel();
                return $instance;
            default:
        }
        return $this;
    }

    /**
     * with回调函数
     * @param null $instance
     * @return $this
     */
    protected function withCallback($instance = null)
    {
        if ($this->callback) {
            $this->defaultValue = $this->callback->call(empty($instance) ? $this : $instance, $this->defaultValue);
        }
        return $this;
    }
}
