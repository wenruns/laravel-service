<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/2/4
 * Time: 11:54
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList;


class Column extends BaseColumn
{
    /**
     * @var int
     */
    public $width = 12;
    /**
     * @var array
     */
    protected $columns = [];

    public function __construct(MultiList $multiList, TableList $tableList, $column, $label, $type = 'text', $parentColumn = null, $displayCallback = null)
    {
        parent::__construct($multiList, $tableList, $column, $label, $type, $parentColumn, $displayCallback);
        $this->key = false;
    }

    /**
     * @param $width
     * @return $this|BaseColumn
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return string|void
     */
    public function render()
    {
        $width = $this->width < 1 ? floor($this->width * 12) : $this->width;
        $content = '<div class="col-sm-' . $width . '">';

        foreach ($this->columns as $column) {
            $value = $this->defaultValue[$column->index()] ?? null;
            if ($value) {
                $column->default($value);
            }
            $content .= $column->render();
        }
        $content .= '</div>';
        return $content;
    }

}
