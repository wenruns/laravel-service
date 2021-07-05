<?php


namespace WenRuns\Service\Grid\Column;


use Encore\Admin\Grid\Model;

class InputFilter extends \Encore\Admin\Grid\Column\InputFilter
{
    protected $queryClosure = null;

    protected $input = null;

    public function __construct($type, $queryClosure = null)
    {
        $this->queryClosure = $queryClosure;
        parent::__construct($type);
    }

    /**
     * @param string $value
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

        if ($this->type == 'like') {
            $model->where($this->getColumnName(), 'like', "%{$value}%");

            return;
        }

        if (in_array($this->type, ['date', 'time'])) {
            $method = 'where' . ucfirst($this->type);
            $model->{$method}($this->getColumnName(), $value);

            return;
        }

        $model->where($this->getColumnName(), $value);
    }

}
