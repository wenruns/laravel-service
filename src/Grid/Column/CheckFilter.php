<?php


namespace WenRuns\Service\Grid\Column;


use Encore\Admin\Grid\Model;

class CheckFilter extends \Encore\Admin\Grid\Column\CheckFilter
{
    protected $queryClosure = null;

    protected $input = null;

    public function __construct(array $options, $queryClosure = null)
    {
        $this->queryClosure = $queryClosure;
        parent::__construct($options);
    }

    /**
     * Add a binding to the query.
     *
     * @param array $value
     * @param Model $model
     */
    public function addBinding($value, Model $model)
    {
        if (empty($value)) {
            return;
        }

        if (is_callable($this->queryClosure)) {
            $this->input = $value;
            $this->queryClosure->call($this, $model);
            return;
        }

        $model->whereIn($this->getColumnName(), $value);
    }
}
