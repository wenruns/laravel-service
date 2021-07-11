<?php


namespace WenRuns\Laravel\Admin\Grid\Filter;


use Encore\Admin\Grid\Filter\Between;
use Illuminate\Support\Arr;

class TimestampBetween extends Between
{

    protected $customCondition = null;

    protected $input = [
        'start'=>null,
        'end'=>null,
    ];

    public function __construct($column, $label = '', $callback=null)
    {
        $this->customCondition = $callback;
        parent::__construct($column, $label);
    }

    public function condition($inputs)
    {
        if ($this->ignore) {
            return;
        }

        if (!Arr::has($inputs, $this->column)) {
            return;
        }

        $this->input = $this->value = Arr::get($inputs, $this->column);

        $value = array_filter($this->value, function ($val) {
            return $val !== '';
        });

        if (empty($value)) {
            return;
        }
        $callback = $this->customCondition;
        if($callback){
            return $this->buildCondition(function ($query)  use ($callback){
                $callback->call($this, $query);
            });
        }

        if (!isset($value['start'])) {
            return $this->buildCondition($this->column, '<=', $value['end']);
        }

        if (!isset($value['end'])) {
            return $this->buildCondition($this->column, '>=', $value['start']);
        }

        $this->query = 'whereBetween';

        return $this->buildCondition($this->column, $this->value);
    }
}
