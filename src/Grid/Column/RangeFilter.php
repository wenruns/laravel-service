<?php


namespace WenRuns\Service\Grid\Column;


use Encore\Admin\Grid\Model;

class RangeFilter extends \Encore\Admin\Grid\Column\RangeFilter
{
    protected $queryClosure = null;

    protected $input = null;

    /**
     * RangeFilter constructor.
     * @param $type
     * @param null $queryClosre
     */
    public function __construct($type, $queryClosre = null)
    {
        $this->queryClosure = $queryClosre;
        parent::__construct($type);
    }

    /**
     * @param mixed $value
     * @param Model $model
     */
    public function addBinding($value, Model $model)
    {
        $value = array_filter((array)$value);

        if (empty($value)) {
            return;
        }

        if (is_callable($this->queryClosure)) {
            $this->input = $value;
            $this->queryClosure->call($this, $model);
            return;
        }

        if (!isset($value['start'])) {
            return $model->where($this->getColumnName(), '<', $value['end']);
        } elseif (!isset($value['end'])) {
            return $model->where($this->getColumnName(), '>', $value['start']);
        } else {
            return $model->whereBetween($this->getColumnName(), array_values($value));
        }
    }
}
