<?php


namespace WenRuns\Service\Grid\Column;


use Encore\Admin\Admin;
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

    /**
     * Add script to page.
     *
     * @return void
     */
    protected function addScript()
    {
        $options = [
            'locale' => config('app.locale'),
            'allowInputToggle' => true,
        ];

        if ($this->type == 'date') {
            $options['format'] = 'YYYY-MM-DD';
        } elseif ($this->type == 'time') {
            $options['format'] = 'HH:mm:ss';
        } elseif ($this->type == 'datetime') {
            $options['format'] = 'YYYY-MM-DD HH:mm:ss';
        } else {
            return;
        }

        $options = json_encode($options);

        Admin::script("$('.{$this->class}').datetimepicker($options);");
    }

    /**
     * Render this filter.
     *
     * @return string
     */
    public function render()
    {
        $script = <<<'SCRIPT'
$('.dropdown-menu input').click(function(e) {
    e.stopPropagation();
});
SCRIPT;
        Admin::script($script);

        $this->addScript();

        $value = $this->getFilterValue();

        $active = empty($value) ? '' : 'text-yellow';

        return <<<EOT
<span class="dropdown">
    <form action="{$this->getFormAction()}" pjax-container style="display: inline-block;">
    <a href="javascript:void(0);" class="dropdown-toggle {$active}" data-toggle="dropdown">
        <i class="fa fa-filter"></i>
    </a>
    <ul class="dropdown-menu" role="menu" style="padding: 10px;box-shadow: 0 2px 3px 0 rgba(0,0,0,.2);left: -70px;border-radius: 0;">
        <li>
            <input type="text" name="{$this->getColumnName()}" value="{$this->getFilterValue()}" class="form-control input-sm {$this->class}" placeholder="关键词" autocomplete="off"/>
        </li>
        <li class="divider"></li>
        <li class="text-right">
            <button class="btn btn-sm btn-flat btn-primary column-filter-submit pull-left" data-loading-text="{$this->trans('search')}..."><i class="fa fa-search"></i>&nbsp;&nbsp;{$this->trans('search')}</button>
            <span><a href="{$this->getFormAction()}" class="btn btn-sm btn-default btn-flat column-filter-all"><i class="fa fa-undo"></i></a></span>
        </li>
    </ul>
    </form>
</span>
EOT;
    }
}
