<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2021/7/4
 * Time: 11:16
 */

namespace WenRuns\Laravel\Admin;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;

class GridService
{
    /**
     * @var null | \WenRuns\Laravel\Admin\Grid |Grid
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
        'options' => ['edit' => true, 'view' => true, 'delete' => true],
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
    public function __construct($modelClass, $girdClass = \WenRuns\Laravel\Admin\Grid::class)
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
     * @param string $columns
     * @param string $javascriptFn
     * @param int $mergeInput
     * @return $this
     */
    public function mergeColspan($columns = '*', $javascriptFn = 'false', $mergeInput = 2)
    {
        $this->grid->mergeColspan(...func_get_args());
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
     * @param  \Closure|null|array $actions
     * @param array $options
     * @return $this
     */
    public function actions($actions = null, $options = [])
    {
        $this->actionsClosure = [
            'closure' => $actions,
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
        if ($this->grid->option('show_define_empty_page')) {
            self::showEmptyPage();
        }
        $actionsOption = $this->actionsClosure;
//        $batchActionsClosure = $this->batchActionsClosure;
        $this->checkInit()
            ->header($this->headerClosure ?? function () {
                })
            ->footer($this->footerClosure ?? function () {
                })
            ->actions(function (Grid\Displayers\Actions $actions) use ($actionsOption) {
                $closure = $actionsOption['closure'] ?? null;
                $options = $actionsOption['options'] ?? [];
                if (is_array($closure)) {
                    $options = $closure;
                    $closure = null;
                }
                $enableEdit = $options['edit'] ?? empty($closure) && empty($options);
                $enableView = $options['view'] ?? empty($closure) && empty($options);
                $enableDelete = $options['delete'] ?? empty($closure) && empty($options);
                $enableView || $actions->disableView();
                $enableDelete || $actions->disableDelete();
                $enableEdit || $actions->disableEdit();
                if (is_callable($closure)) {
                    $closure->call($this, $actions);
                }
            });
        if (is_callable($this->toolsClosure)) {
            $this->grid->tools($this->toolsClosure);
        }

        return $this->grid;
    }

    /**
     * @param string $selector
     */
    public static function showEmptyPage($selector = '.table-wrap.table-main')
    {
        $emptyPage = self::emptyPage();
        $script = <<<SCRIPT
$(function(){
    var mTb = document.querySelector("{$selector}");
    if(mTb){
        if(mTb.querySelector("tbody").children.length<=0){
            mTb.innerHTML += `{$emptyPage}`;
        }
    }
});
SCRIPT;
        Admin::script($script);
    }

    /**
     * @return string
     */
    protected static function emptyPage()
    {
        return <<<HTML
<table class="table table-hover grid-table">
    <tr>
        <td colspan="2" class="empty-grid" style="padding: 100px;text-align: center;color: #999999">
            <svg t="1562312016538" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2076" width="128" height="128" style="fill: #e9e9e9;">
                <path d="M512.8 198.5c12.2 0 22-9.8 22-22v-90c0-12.2-9.8-22-22-22s-22 9.8-22 22v90c0 12.2 9.9 22 22 22zM307 247.8c8.6 8.6 22.5 8.6 31.1 0 8.6-8.6 8.6-22.5 0-31.1L274.5 153c-8.6-8.6-22.5-8.6-31.1 0-8.6 8.6-8.6 22.5 0 31.1l63.6 63.7zM683.9 247.8c8.6 8.6 22.5 8.6 31.1 0l63.6-63.6c8.6-8.6 8.6-22.5 0-31.1-8.6-8.6-22.5-8.6-31.1 0l-63.6 63.6c-8.6 8.6-8.6 22.5 0 31.1zM927 679.9l-53.9-234.2c-2.8-9.9-4.9-20-6.9-30.1-3.7-18.2-19.9-31.9-39.2-31.9H197c-19.9 0-36.4 14.5-39.5 33.5-1 6.3-2.2 12.5-3.9 18.7L97 679.9v239.6c0 22.1 17.9 40 40 40h750c22.1 0 40-17.9 40-40V679.9z m-315-40c0 55.2-44.8 100-100 100s-100-44.8-100-100H149.6l42.5-193.3c2.4-8.5 3.8-16.7 4.8-22.9h630c2.2 11 4.5 21.8 7.6 32.7l39.8 183.5H612z" p-id="2077"></path>
            </svg>
        </td>
    </tr>
</table>
HTML;
    }

    /**
     * @param $modelClass
     * @param string $gridClass
     * @return static
     */
    public static function instance($modelClass, $gridClass = \WenRuns\Laravel\Admin\Grid::class)
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

    protected function submitEvent()
    {
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
