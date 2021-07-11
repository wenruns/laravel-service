<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/1/26
 * Time: 9:31
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList;


class BaseColumn
{
    use CommonMethods;

    /**
     * @var MultiList|null
     */
    protected $multiList = null;

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var null
     */
    protected $displayCallback = null;

    /**
     * @var string
     */
    public $column = '';

    /**
     * @var string
     */
    public $label = '';

    /**
     * @var null
     */
    public $defaultValue = null;

    /**
     * @var null
     */
    public $subItems = null;

    /**
     * key值
     * @var null
     */
    public $key = 0;
    /**
     * item(value 值)
     * @var null
     */
    public $itemData = null;

    /**
     * @var null|TableList
     */
    protected $tableList = null;

    /**
     * @var null
     */
    public $parentColumn = null;


    /**
     * @var string
     */
    public $width = 'auto';

    /**
     * @var string
     */
    public $type = 'text';

    /**
     * @var array
     */
    protected $optionsArr = [];

    /**
     * @var bool
     */
    protected $hideIconBool = false;

    /**
     * @var bool
     */
    protected $disabledBool = false;

    /**
     * @var bool
     */
    protected $readonlyBool = false;

    /**
     * @var null
     */
    protected $callback = null;

    /**
     * @var string
     */
    protected $style = '';

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var null
     */
    public $parent = null;


    /**
     * @var bool
     */
    protected $required = false;

    /**
     * BaseColumn constructor.
     * @param MultiList $multiList
     * @param TableList $tableList
     * @param $column
     * @param $label
     * @param string $type
     * @param null $parentColumn
     * @param null $displayCallback
     */
    public function __construct(MultiList $multiList,TableList $tableList, $column, $label, $type = 'text', $parentColumn = null, $displayCallback = null)
    {
        $this->multiList = $multiList;
        $this->column = $column;
        $this->label = $label;
        $this->parentColumn = empty($parentColumn) ? $multiList->column() : $parentColumn;
        $this->type = $type;
        $this->displayCallback = $displayCallback;
        $this->parent = $tableList;
    }


    public function index()
    {
        return $this->column;
    }

    /**
     * @param bool $required
     * @return $this
     */
    public function required($required = true)
    {
        $this->required = $required;
        return $this;
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


    public function attribute($attr = [])
    {
        $this->attributes = array_merge($this->attributes, $attr);
        return $this;
    }


    /**
     * @param $default
     * @return $this
     */
    public function default($default)
    {
        $this->defaultValue = $default;
        return $this;
    }

    /**
     * @param $items
     * @return $this
     */
    public function subItems($items)
    {
        $this->subItems = $items;
        return $this;
    }

    /**
     * @param bool $disabled
     * @return $this
     */
    public function disabled($disabled = true)
    {
        $this->disabledBool = $disabled;
        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function with(\Closure $closure)
    {
        $this->callback = $closure;
        return $this;
    }

    /**
     * @param bool $readonly
     * @return $this
     */
    public function readonly($readonly = true)
    {
        $this->readonlyBool = $readonly;
        return $this;
    }

    /**
     * @param bool $hide
     * @return $this
     */
    public function hideIcon($hide = true)
    {
        $this->hideIconBool = $hide;
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function options($options = [])
    {
        $this->optionsArr = $options;
        return $this;
    }

    /**
     * @param $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }


    /**
     * @return TableList|null
     */
    public function getTableList()
    {
        if (empty($this->tableList)) {
            $this->tableList = new TableList($this->multiList);
        }
        return $this->tableList;
    }

    /**
     * @return MultiList|null
     */
    public function getMultiList()
    {
        return $this->multiList;
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param $item
     * @param $key
     * @param bool $createEmpty
     * @return mixed
     */
    public function renderValue($item, $key)
    {
        if (empty($this->defaultValue)) {
            $this->defaultValue = $item[$this->column] ?? null;
        }
        $this->itemData = $item;
        $this->key = $key;
        $method = $this->type;
        return $this->$method($this->column, $this->label, $this->displayCallback);
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
    }
}
