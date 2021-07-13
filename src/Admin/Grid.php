<?php


namespace WenRuns\Laravel\Admin;


use Closure;
use Encore\Admin\Admin;
use Encore\Admin\Exception\Handler;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use WenRuns\Laravel\Admin\Grid\Column;
use WenRuns\Laravel\Admin\Grid\Tools\Header;
use WenRuns\Laravel\Admin\Grid\Tools\Selector;
use WenRuns\Laravel\Laravel;

class Grid extends \Encore\Admin\Grid
{

    protected $view = 'WenAdmin::grid.table';

    /**
     * Get the string contents of the grid view.
     *
     * @return string
     */
    public function render()
    {
        $this->handleExportRequest(true);

        try {
            $this->build();
        } catch (\Exception $e) {
            return Handler::renderException($e);
        }

        $this->callRenderingCallback();


        return Admin::component($this->view, $this->variables());
    }

    /**
     * Build the grid.
     *
     * @return void
     */
    public function build()
    {
        if ($this->builded) {
            return;
        }

        $this->applyQuery();

        $collection = $this->applyFilter(false);

        $this->addDefaultColumns();

        Column::setOriginalGridModels($collection);

        $data = $collection->toArray();

        $this->columns->map(function (\Encore\Admin\Grid\Column $column) use (&$data) {
            $data = $column->fill($data);

            $this->columnNames[] = $column->getName();
        });

        $this->buildRows($data, $collection);

        $this->builded = true;
    }

    /**
     * @return array|Collection|mixed|void
     */
    public function applyQuery()
    {
        $this->applyQuickSearch();

        $this->applyColumnFilter();

        $this->applyColumnSearch();

        $this->applySelectorQuery();
    }

    /**
     * Apply column filter to grid query.
     *
     * @return void
     */
    protected function applyColumnFilter()
    {
        $this->columns->each->bindFilterQuery($this->model());
    }

    /**
     * @param string $name
     * @param string $label
     *
     * @return bool|\Encore\Admin\Grid|\Encore\Admin\Grid\Column|Column
     */
    public function column($name, $label = '')
    {
        if (Str::contains($name, '.')) {
            return $this->addRelationColumn($name, $label);
        }

        if (Str::contains($name, '->')) {
            return $this->addJsonColumn($name, $label);
        }

        return $this->__call($name, array_filter([$label]));
    }

    /**
     * Add a relation column to grid.
     *
     * @param string $name
     * @param string $label
     *
     * @return $this|bool|\Encore\Admin\Grid\Column|Grid
     */
    protected function addRelationColumn($name, $label = '')
    {
        list($relation, $column) = explode('.', $name);

        $model = $this->model()->eloquent();

        if (!method_exists($model, $relation) || !$model->{$relation}() instanceof Relations\Relation) {
            $class = get_class($model);

            admin_error("Call to undefined relationship [{$relation}] on model [{$class}].");

            return $this;
        }

        $name = ($this->shouldSnakeAttributes() ? Str::snake($relation) : $relation) . '.' . $column;

        $this->model()->with($relation);

        return $this->addColumn($name, $label)->setRelation($relation, $column);
    }

    /**
     * Add column to grid.
     *
     * @param string $column
     * @param string $label
     *
     * @return \Encore\Admin\Grid\Column|\Illuminate\Support\HigherOrderTapProxy|mixed
     */
    protected function addColumn($column = '', $label = '')
    {
        $column = new Column($column, $label);
        $column->setGrid($this);

        return tap($column, function ($value) {
            $this->columns->push($value);
        });
    }

    /**
     * Dynamically add columns to the grid view.
     *
     * @param $method
     * @param $arguments
     *
     * @return bool|\Encore\Admin\Grid\Column|\Illuminate\Support\HigherOrderTapProxy|mixed
     */
    public function __call($method, $arguments)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $arguments);
        }

        $label = $arguments[0] ?? null;

        if ($this->model()->eloquent() instanceof MongodbModel) {
            return $this->addColumn($method, $label);
        }

        if ($column = $this->handleGetMutatorColumn($method, $label)) {
            return $column;
        }

        if ($column = $this->handleRelationColumn($method, $label)) {
            return $column;
        }

        return $this->addColumn($method, $label);
    }


    public function selector(\Closure $closure)
    {
        $this->selector = new Selector();

        call_user_func($closure, $this->selector);

        $this->header(function () {
            return $this->renderSelector();
        });

        return $this;
    }

    public function header(Closure $closure = null)
    {
        if (!$closure) {
            return $this->header;
        }

        $this->header[] = $closure;

        return $this;
    }


    public function renderHeader()
    {
        if (empty($this->header)) {
            return '';
        }

        return (new Header($this))->render();
    }

    /**
     * @param $columns
     * @param string $javascriptFn
     * @param int $mergeInput
     * @return $this
     */
    public function mergeColspan($columns, $javascriptFn = 'false', $mergeInput = 2)
    {
        $this->addMergeColspanScript(...func_get_args());
        return $this;
    }

    /**
     * @param string $columns
     * @param string $javascriptFn
     * @param int $mergeInput
     * @return $this
     */
    protected function addMergeColspanScript($columns = '*', $javascriptFn = 'false', $mergeInput = 2)
    {
        $preg = '/^function\s*\([\s\S]*?\)\s*\{[\s\S]*\}/m';
        if (is_string($columns) && preg_match($preg, $columns)) {
            $javascriptFn = $columns;
            $columns = '["*"]';
            if (is_numeric($javascriptFn)) {
                $mergeInput = $javascriptFn;
            }
        } else {
            if (is_string($columns)) {
                $columns = explode(',', $columns);
            }
            $columns = json_encode($columns);
        }
        Laravel::loadJs('grid/grid.js');
        $script = <<<SCRIPT
$(function(){
    new mergeColspan({
        tableSelector: "#{$this->tableID}",
        mergeInput: {$mergeInput},
        columns: {$columns},
        fn: {$javascriptFn},
    });
});
SCRIPT;
        Admin::script($script);
        return $this;
    }

}
