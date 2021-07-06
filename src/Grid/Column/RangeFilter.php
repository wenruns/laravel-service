<?php


namespace WenRuns\Service\Grid\Column;


use Encore\Admin\Admin;
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

        $this->input = $value;
        if (is_callable($this->queryClosure)) {
            $this->queryClosure->call($this, $model);
            return;
        }

        $start = $value['start'] ?? null;
        $end = $value['end'] ?? null;

        if ($this->type == 'date') {
            empty($start) ?: $start = strtotime($start);
            empty($end) ?: $end = strtotime($end . ' 23:59:59');
        } elseif ($this->type == 'time') {
            $date = date('Y-m-d');
            empty($start) ?: $start = strtotime($date . ' ' . $start);
            empty($end) ?: $end = strtotime($date . ' ' . $end);
        } elseif ($this->type == 'datetime') {
            empty($start) ?: $start = strtotime($start);
            empty($end) ?: $end = strtotime($end);
        }
        if($start){
            $model->where($this->getColumnName(),'>=', $start);
        }
        if($end){
            $model->where($this->getColumnName(), '<=', $end);
        }
        return $model;
    }

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
        $script = <<<SCRIPT
$(function(){
    var h = setTimeout(function(){
        clearTimeout(h);
        $('.{$this->class['start']},.{$this->class['end']}').datetimepicker($options);
    });
    $('body').on('click', '.dropdown-menu .range-menu li',function(e) {
        var myDate = new Date();
        var h = myDate.getHours(),
            m = myDate.getMinutes(),
            s = myDate.getSeconds(),
            year = myDate.getFullYear(),
            month = myDate.getMonth()+1,
            day = myDate.getDate(),
            end = year + '-' + (month < 10 ? '0' + month : month) + '-' + (day < 10 ? '0' + day : day),
            start = '',
            startDate = null,
            endTimestamp = Math.round(myDate.getTime()/1000),
            range = Number(e.currentTarget.dataset.range);
        switch(range){
            case 1: // 一周
                var startTimestamp = endTimestamp - (7 * 24 * 60 * 60);
                startDate = new Date(startTimestamp * 1000);
                break;
            case 2: // 一月
                var startTimestamp = endTimestamp - (30 * 24 * 60 * 60);
                startDate = new Date(startTimestamp * 1000);
                break;
            case 3: // 二月
                var startTimestamp = endTimestamp - (60 * 24 * 60 * 60);
                startDate = new Date(startTimestamp * 1000);
                break;
            case 4: // 三月
                var startTimestamp = endTimestamp - (90 * 24 * 60 * 60);
                startDate = new Date(startTimestamp * 1000);
                break;
            case 5: // 一年
                var startTimestamp = endTimestamp - (365 * 24 * 60 * 60);
                startDate = new Date(startTimestamp * 1000);
                break;
            default:
                return false;
        }
        var sh = startDate.getHours(),
            sm = startDate.getMinutes(),
            ss = startDate.getSeconds(),
            sYear = startDate.getFullYear(),
            sMonth = startDate.getMonth()+1,
            sDay = startDate.getDate();
        start = sYear + '-' + (sMonth < 10 ? '0' + sMonth : sMonth) + '-' + (sDay < 10 ? '0' + sDay : sDay);
        let parentEle = e.currentTarget.parentElement.parentElement;
        let endEle = parentEle.querySelector(".{$this->class['end']}");
        let startEle = parentEle.querySelector(".{$this->class['start']}");
        if(endEle){
            endEle.value = end;
        }
        if(startEle){
            startEle.value = start;
        }
    });
});
SCRIPT;
        Admin::script($script);
    }

    /**
     * Render this filter.
     *
     * @return string
     */
    public function render()
    {
        $script = <<<SCRIPT
$(function(){
    $('body').on('click', '.dropdown-menu input',function(e) {
        e.stopPropagation();
    });
});
SCRIPT;
        Admin::script($script);
        $this->addScript();

        $value = array_merge(['start' => '', 'end' => ''], $this->getFilterValue([]));
        $active = empty(array_filter($value)) ? '' : 'text-yellow';

        if (in_array($this->type, ['date', 'datetime', 'time'])) {
            return $this->timeRange($value, $active);
        }
        return <<<EOT
<span class="dropdown">
    <form action="{$this->getFormAction()}" pjax-container style="display: inline-block;">
        <a href="javascript:void(0);" class="dropdown-toggle {$active}" data-toggle="dropdown">
            <i class="fa fa-filter"></i>
        </a>
        <ul class="dropdown-menu" role="menu" style="padding: 10px;box-shadow: 0 2px 3px 0 rgba(0,0,0,.2);left: -70px;border-radius: 0;">
            <li style="list-style: none">
                <input type="text" class="form-control input-sm {$this->class['start']}" placeholder="最小值"  name="{$this->getColumnName()}[start]" value="{$value['start']}" autocomplete="off"/>
            </li>
            <li style="margin: 5px;list-style: none;text-align: center"></li>
            <li style="list-style: none">
                <input type="text" class="form-control input-sm {$this->class['end']}" placeholder="最大值" name="{$this->getColumnName()}[end]"  value="{$value['end']}" autocomplete="off"/>
            </li>
            <li class="divider" style="list-style: none"></li>
            <li class="text-right" style="list-style: none">
                <button class="btn btn-sm btn-primary btn-flat column-filter-submit pull-left" data-loading-text="{$this->trans('search')}..."><i class="fa fa-search"></i>&nbsp;&nbsp;{$this->trans('search')}</button>
                <span><a href="{$this->getFormAction()}" class="btn btn-sm btn-default btn-flat column-filter-all"><i class="fa fa-undo"></i></a></span>
            </li>
        </ul>
    </form>
</span>
EOT;
    }

    protected function timeRange($value, $active)
    {
        return <<<EOT
<span class="dropdown">
    <form action="{$this->getFormAction()}" pjax-container style="display: inline-block;">
        <a href="javascript:void(0);" class="dropdown-toggle {$active}" data-toggle="dropdown">
            <i class="fa fa-filter"></i>
        </a>
        <div class="dropdown-menu" role="menu" style="padding: 10px;box-shadow: 0 2px 3px 0 rgba(0,0,0,.2);left: -70px;border-radius: 0;">
            <div style="display: flex;">
                <ul class="range-menu" role="" style="padding: 0px 10px 0px 0px; margin: 0px;width:60px;">
                    <li style="list-style: none;cursor: pointer;text-align: right;" data-range="1"><a style="">一周</a></li>
                    <li style="list-style: none;cursor: pointer;text-align: right;" data-range="2"><a style="">一月</a></li>
                    <li style="list-style: none;cursor: pointer;text-align: right;" data-range="3"><a style="">二月</a></li>
                    <li style="list-style: none;cursor: pointer;text-align: right;" data-range="4"><a style="">三月</a></li>
                    <li style="list-style: none;cursor: pointer;text-align: right;" data-range="5"><a style="">一年</a></li>
                </ul>
                <ul class="" role="" style="padding: 0px;margin: 0px;min-width:80px;">
                    <li style="list-style: none">
                        <input type="text" class="form-control input-sm {$this->class['start']}" placeholder="最小值"  name="{$this->getColumnName()}[start]" value="{$value['start']}" autocomplete="off"/>
                    </li>
                    <li style="margin: 5px;list-style: none;text-align: center">至</li>
                    <li style="list-style: none">
                        <input type="text" class="form-control input-sm {$this->class['end']}" placeholder="最大值" name="{$this->getColumnName()}[end]"  value="{$value['end']}" autocomplete="off"/>
                    </li>
                </ul>

            </div>
            <div class="divider"></div>
            <div class="text-right">
                <button class="btn btn-sm btn-primary btn-flat column-filter-submit pull-left" data-loading-text="{$this->trans('search')}..."><i class="fa fa-search"></i>&nbsp;&nbsp;{$this->trans('search')}</button>
                <span><a href="{$this->getFormAction()}" class="btn btn-sm btn-default btn-flat column-filter-all"><i class="fa fa-undo"></i></a></span>
            </div>
        </div>
    </form>
</span>
EOT;
    }
}
