<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/1/27
 * Time: 10:14
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList;


class Row extends BaseColumn
{
    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var bool
     */
    protected $isRowBool = false;

    /**
     * @var int
     */
    public $width = 3;

    /**
     * @var Field|null
     */
    protected $field = null;

    /**
     * @var null
     */
    protected $value = null;


    /**
     * @var bool
     */
    protected $createEmpty = false;

    public function __construct(MultiList $multiList, TableList $tableList, $column, $label, $type = 'text', $parentColumn = null, $displayCallback = null, Field $field = null)
    {
        $this->tableList = $tableList;
        parent::__construct($multiList, $tableList, $column, $label, $this, $type, $parentColumn, $displayCallback);
        $this->field = $field;
        $this->value = $field->value;
    }

    /**
     * @return TableList|null
     */
    public function getTableList()
    {
        $tableList = new TableList($this->multiList, $this->parentColumn);
        return $tableList->createEmpty($this->createEmpty)
            ->keyIsVariable($this->keyIsVariable);
    }

    /**
     * @param $width
     * @param \Closure $closure
     * @return $this
     */
    public function column($width, \Closure $closure)
    {
        $column = new Column($this->multiList, $this->tableList, $this->column, $this->label, $this, $this->type, $this->parentColumn, $this->displayCallback);
        $column->setWidth($width)
            ->createEmpty($this->createEmpty)
            ->keyIsVariable($this->keyIsVariable);
        $closure->call($this, $column);
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @param bool $isRowBool
     * @return $this
     */
    public function isRowBool($isRowBool = true)
    {
        $this->isRowBool = $isRowBool;
        return $this;
    }

    /**
     * @param $column
     * @param \Closure $closure
     * @param string $label
     * @return MultiList
     */
    public function multiList($column, \Closure $closure, $label = '')
    {
        $index = $column;
        if ($this->parentColumn) {
            $column = $this->parentColumn . '[' . $column . ']';
        }
        $multiList = new MultiList($column, [$closure, $label, $index]);
        $multiList->setForm($this->multiList->getForm())
            ->createEmpty($this->createEmpty)
            ->keyIsVariable($this->keyIsVariable);
        $this->columns[] = $multiList;
        return $multiList;
    }

    /**
     * @return string
     */
    public function render()
    {
        $content = '';
        $hasColumn = false;
        foreach ($this->columns as $key => $column) {
            $index = $column->index();
            if (empty($index)) {
                $value = $this->subItems;
            } else {
                $value = $this->subItems[$index] ?? null;
            }
            if (!empty($value)) {
                $column->default($value);
            }
            if ($column instanceof MultiList) {
                $content .= $column->createEmpty($this->createEmpty)->getContent();
            } elseif ($column instanceof Column) {
                $hasColumn = true;
                $content .= $column->createEmpty($this->createEmpty)->render();
            } else {
                $content .= $column->isRow($this->isRowBool)->createEmpty($this->createEmpty)->render();
            }
        }
        if ($this->isRowBool) {
            $content = '<div class="form-group" style="display:flex;flex-wrap: wrap;">' . $content . '</div>';
        } else if ($hasColumn) {
            $content = '<div class="form-group">' . $content . '</div>';
        }
        return $content;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param $name
     * @return |null
     */
    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->$name ?? ($this->multiList->$name ?? null);
    }
}
