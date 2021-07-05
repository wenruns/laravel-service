<?php


namespace WenRuns\Service\Grid;



use Encore\Admin\Grid\Model;
use WenRuns\Service\Grid\Column\CheckFilter;
use WenRuns\Service\Grid\Column\InputFilter;
use WenRuns\Service\Grid\Column\RangeFilter;

class Column extends \Encore\Admin\Grid\Column
{
    /**
     * @param null $builder
     * @param null $formal
     * @param null $queryClosure
     *
     * @return $this|Column
     */
    public function filter($builder = null)
    {
        return $this->addFilter(...func_get_args());
    }

    /**
     * @param null $type
     * @param null $formal
     * @param null $queryClosure
     * @return $this|Column
     */
    protected function addFilter($type = null, $formal = null, $queryClosure = null)
    {
        if (is_array($type)) {
            return $this->addHeader(new CheckFilter($type, $queryClosure));
        }

        if (is_null($type)) {
            $type = 'equal';
        }

        if (in_array($type, ['equal', 'like', 'date', 'time', 'datetime'])) {
            return $this->addHeader(new InputFilter($type, $queryClosure));
        }

        if ($type === 'range') {
            if (is_null($formal)) {
                $formal = 'equal';
            }

            return $this->addHeader(new RangeFilter($formal, $queryClosure));
        }

        return $this;
    }

    public function bindFilterQuery(Model $model)
    {
        if ($this->filter) {
            $this->filter->addBinding(request($this->getName()), $model);
        }
    }
}
