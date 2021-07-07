<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2021/7/4
 * Time: 11:16
 */

namespace WenRuns\Service;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;

class GridService
{
    /**
     * @var Grid|null
     */
    protected $grid = null;

    /**
     * @var null
     */
    protected $toolsClosure = null;

    /**
     * @var null
     */
    protected $footerClosure = null;

    /**
     * @var null
     */
    protected $headerClosure = null;

    /**
     * @var null
     */
    protected $actionsClosure = [
        'closure' => null,
        'options' => ['edit' => false, 'view' => false, 'delete' => false],
    ];

    /**
     * @var bool
     */
    protected $exporter = false;


    /**
     * @var null | \Closure
     */
    protected $filterClosure = null;

    /**
     * @var null
     */
    protected $batchActionsClosure = null;

    /**
     * GridService constructor.
     * @param $modelClass
     * @param string $girdClass
     */
    public function __construct($modelClass, $girdClass = \WenRuns\Service\Grid::class)
    {
        $this->grid = new $girdClass(new $modelClass);
    }

    /**
     * @param bool $disable
     * @return $this
     */
    public function disableBatchActions($disable = true)
    {
        $this->grid->disableBatchActions($disable);
        return $this;
    }

    /**
     * @return $this
     */
    public function disableActions()
    {
        $this->grid->disableActions();
        return $this;
    }

    /**
     * @param bool $disable
     * @return $this
     */
    public function disableCreateButton($disable = true)
    {
        $this->grid->disableCreateButton($disable);
        return $this;
    }

    public function disableFilter($disable = true)
    {
        $this->grid->disableFilter($disable);
        return $this;
    }


    /**
     * @param \Closure $closure
     * @return $this
     */
    public function where(\Closure $closure)
    {
        $closure->call($this, $this->grid->model());
        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function content(\Closure $closure)
    {
        call_user_func($closure, $this->grid, $this);
        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function filter(\Closure $closure)
    {
        $this->filterClosure = $closure;
        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function tools(\Closure $closure)
    {
        $this->toolsClosure = $closure;
        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function selector(\Closure $closure)
    {
        $this->grid->selector($closure);
        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function footer(\Closure $closure)
    {
        $this->footerClosure = $closure;
        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function header(\Closure $closure)
    {
        $this->headerClosure = $closure;
        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function actions(\Closure $closure = null, $options = ['edit' => false, 'view' => false, 'delete' => false])
    {

        $this->actionsClosure = [
            'closure' => $closure,
            'options' => $options,
        ];

        return $this;
    }

    /**
     * @param $exporter
     * @return $this
     */
    public function exporter($exporter)
    {
        if (is_callable($exporter)) {
            $this->grid->exporter(call_user_func($exporter, $this));
        } else {
            $this->grid->exporter($exporter);
        }
        $this->exporter = true;
        return $this;
    }

    /**
     * @param bool $disable
     * @return $this
     */
    public function disableDefineEmptyPage($disable = true)
    {
        $this->grid->disableDefineEmptyPage($disable);
        return $this;
    }

    /**
     * @param bool $disable
     * @return $this
     */
    public function disableColumnSelector($disable = true)
    {
        $this->grid->disableColumnSelector($disable);
        return $this;
    }

    /**
     * @param bool $disable
     * @return $this
     */
    public function disablePerPageSelector($disable = true)
    {
        $this->grid->disablePerPageSelector($disable);
        return $this;
    }

    /**
     * @param bool $disable
     * @return $this
     */
    public function disablePagination($disable = true)
    {
        $this->grid->disablePagination($disable);
        return $this;
    }

    /**
     * @return Grid|mixed|null
     */
    protected function checkInit()
    {
        $this->exporter || $this->grid->disableExport();
        $filterClosure = $this->filterClosure;
        $this->grid->filter(function (Grid\Filter $filter) use ($filterClosure) {
            $filter->disableIdFilter();
            $filterClosure && $filterClosure->call($this, $filter);
        });
        return $this->grid;
    }

    /**
     * @param $script
     * @return $this
     */
    public function script($script)
    {
        if (is_callable($script)) {
            $script = $script->call($this);
        }
        Admin::script($script);
        return $this;
    }

    /**
     * @param false $disableDelete
     * @param \Closure|null $closure
     * @return $this
     */
    public function batchActions($disableDelete = false, \Closure $closure = null)
    {
        $this->grid->batchActions(function (Grid\Tools\BatchActions $batchActions) use ($disableDelete, $closure) {
            $disableDelete && $batchActions->disableDelete();
            is_callable($closure) && $closure->call($this, $batchActions);
        });
        return $this;
    }


    /**
     * @return Grid|mixed|null
     */
    public function render()
    {
        $actionsOption = $this->actionsClosure;
        $batchActionsClosure = $this->batchActionsClosure;
        $this->checkInit()
            ->header($this->headerClosure ?? function () {
                })
            ->footer($this->footerClosure ?? function () {
                })
            ->actions(function (Grid\Displayers\Actions $actions) use ($actionsOption) {
                $closure = $actionsOption['closure'] ?? null;
                $options = $actionsOption['options'] ?? [];
                $enableEdit = $options['edit'] ?? false;
                $enableView = $options['view'] ?? false;
                $enableDelete = $options['delete'] ?? false;
                $enableView || $actions->disableView();
                $enableDelete || $actions->disableDelete();
                $enableEdit || $actions->disableEdit();
                if (is_callable($closure)) {
                    $closure->call($this, $actions);
                }
            })
            ->tools($this->toolsClosure ?? function () {
                });
        return $this->grid;
    }

    /**
     * @param $modelClass
     * @param string $gridClass
     * @return static
     */
    public static function instance($modelClass, $gridClass = \WenRuns\Service\Grid::class)
    {
        return new static($modelClass, $gridClass);
    }


    public function enableButtonIframe($enable = true)
    {
        if ($enable) {
            $this->addScript();
        }
        return $this;
    }

    protected function addScript()
    {
        $pjax = Button::pJax();
        $eventPageIframe = Button::eventPageIframe('', $this->submitEvent());
        $script = <<<SCRIPT
$("a[href^='http'][target!='_blank']").click(function(event){
    var e = event || window.event;
    e.preventDefault();
    let fn = {$eventPageIframe};
    fn.call(this, e, {$pjax});
});
SCRIPT;
        Admin::script($script);
    }

    protected function submitEvent(){
        return <<<SCRIPT
function(form, pJax){
    let formData = new FormData(form);
    let url = form.getAttribute("action");
    pJax({
        url: url,
        data: formData,
        method: form.getAttribute("method"),
        callback: function(res){
            if(res.status){
                swal.close();
                $.pjax.reload("#pjax-container");
                toastr.success(res.message);
            }else{
                toastr.error(res.message);
            }
        },
    });
}
SCRIPT;

    }
}
