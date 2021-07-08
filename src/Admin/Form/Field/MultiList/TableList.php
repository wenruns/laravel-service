<?php
/**
 * Created by PhpStorm.
 * User: Administrator【wenruns】
 * Date: 2021/1/26
 * Time: 9:33
 */

namespace WenRuns\Laravel\Admin\Form\Field\MultiList;

use Encore\Admin\Facades\Admin;

/**
 * Class TableList
 * @package App\Admin\Extensions\Form\MultiList
 */
class TableList
{
    use CommonMethods;

    protected $style = '';

    /**
     * @var MultiList|null
     */
    protected $multiList = null;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var null
     */
    protected $parentColumn = null;

    /**
     * @var array
     */
    protected $defaultValues = [];

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var string
     */
    protected $unique = '';

    /**
     * @var null
     */
    protected $value = null;

    /**
     * @var int
     */
    protected $key = 0;

    /**
     * @var string
     */
    protected $buttonEventClosure = null;

    /**
     * @var bool
     */
    protected $showButton = false;

    /**
     * @var string
     */
    protected static $scripts = [];

    /**
     * @var bool
     */
    protected $enableInsertEleAfterActive = false;

    /**
     * TableList constructor.
     * @param MultiList $multiList
     * @param null $parentColumn
     */
    public function __construct(MultiList $multiList, $parentColumn = null)
    {
        $this->multiList = $multiList;
        $this->parentColumn = empty($parentColumn) ? $multiList->column() : $parentColumn;
    }

    /**
     * @return int|string
     */
    public function getUniqueKey()
    {
        if (empty($this->unique)) {
            $this->unique = mt_rand(10000, 99999);
        }
        return $this->unique;
    }

    /**
     * @param $value
     * @param int $key
     * @return $this
     */
    public function value($value, $key = 0)
    {
        $this->value = $value;
        $this->key = $key;
        return $this;
    }

    /**
     * @return int
     */
    public function getColumnLen()
    {
        return count($this->columns) + ($this->showButton ? 1 : 0);
    }

    /**
     * @param $value
     * @return $this
     */
    public function default($value)
    {
        $this->defaultValues = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->content = $this->tableListHead() . $this->tbodyStart();
        if (!empty($this->defaultValues)) {
            if (count($this->defaultValues) == count($this->defaultValues, true)) {
                $this->content .= $this->value($this->defaultValues)->tableListBody();
            } else {
                foreach ($this->defaultValues as $k => $item) {
                    $this->content .= $this->value($item, $k)->tableListBody();
                }
            }
        }
        $this->content .= $this->tableListEnd();
        return $this->content;
    }

    /**
     * @return string
     */
    public function tableListHead()
    {
        $content = $this->tableStart() . $this->theadStart() . $this->trStart();
        foreach ($this->columns as $key => $column) {
            if ($column->type == 'hidden') {
                continue;
            }
            $content .= $this->thStart($column) . $column->render() . $this->thEnd();
        }
        $content .= $this->addButton();
        $content .= $this->trEnd() . $this->theadEnd();

        return $content;
    }

    /**
     * @return string
     */
    protected function addButton()
    {
        if (!$this->showButton) {
            return '';
        }
        $this->buttonEvent(true);
        $number = count($this->multiList->getOptions());
//        $base64Content = base64_encode($this->getOneRowHtml());
        return <<<HTML
<th>
    <span class="table-list-btn-add-{$this->getUniqueKey()} add-row-btn" data-type="add" data-number="{$number}" style="padding: 5px;cursor: pointer;">
        <i class="fa fa-plus-square" style="color: green"></i>
    </span>
<!--    <table style="display: none">{}</table>-->
</th>
HTML;
//        return <<<HTML
//<th >
//    <span class="btn btn-sm btn-success table-list-btn-add-{$this->getUniqueKey()}" data-type="add" data-number="{$number}">
//        <i class="fa fa-edit fa-fw"></i>新增
//    </span>
//    <table style="display: none">{$this->getOneRowHtml()}</table>
//</th>
//HTML;
    }


    /**
     * @return string
     */
    protected function removeButton()
    {
        if (!$this->showButton) {
            return '';
        }
        $this->buttonEvent();
        return <<<HTML
<td>
    <span class="table-list-btn-remove-{$this->getUniqueKey()} remove-row-btn" data-type="remove" style="padding: 5px;cursor: pointer;">
        <i class="fa fa-trash" style="color: red"></i>
    </span>
</td>
HTML;
//        return <<<HTML
//<td>
//    <span class="btn btn-sm btn-danger table-list-btn-remove-{$this->getUniqueKey()}" data-type="remove">
//        <i class="fa fa-remove fa-fw"></i>移除
//    </span>
//</td>
//HTML;
    }

    /**
     * @param bool $addBtn
     * @return $this
     */
    protected function buttonEvent($addBtn = false)
    {
        $eventClosure = empty($this->buttonEventClosure) ? 0 : $this->buttonEventClosure;
        $addBtn ? $this->addEvent($eventClosure) : $this->removeEvent($eventClosure);
        return $this;
    }

    /**
     * @param $eventClosure
     * @return $this
     */
    protected function addEvent($eventClosure)
    {
        $btnClass = 'table-list-btn-add-' . $this->getUniqueKey();
        $replaceStr = MultiList::SYMBOL_BEGIN . $this->getKeyVariableName() . MultiList::SYMBOL_END;
        $oneRow = str_replace('</', '<\/', $this->getOneRowHtml());
        $script = str_replace('</', '<\/', self::$scripts[$this->getTableKey() . '_oneRow'] ?? '');
        $tableKey = $this->getTableKey();
        $script = <<<SCRIPT
$(function(){
    $("#{$tableKey}").on("click", "tbody tr", function(e){
        let parentEle = e.currentTarget.parentElement;
        if(parentEle){
            let activeObj = parentEle.querySelector("tr.active");
            if(activeObj){
                activeObj.classList.remove('active');
            }
        }
        e.currentTarget.classList.add('active');
    });
    $(document).click(function(e){
        let obj = e.target.querySelector("#{$tableKey} tbody");
        if(obj){
            let activeObj = obj.querySelector("tr.active");
            if(activeObj){
                activeObj.classList.remove("active");
            }
        }
    });
    $("#{$tableKey}").on("click", ".{$btnClass}", function(e){
        var appendContent = function(){
            let oneRowHtml = `{$oneRow}`.replace(/{$replaceStr}/mg, e.currentTarget.dataset.number);
            let script = `{$script}`.replace(/{$replaceStr}/mg, e.currentTarget.dataset.number);
            let trEle = null;
            e.currentTarget.dataset.number++;
            // table元素下有thead 和 tbody 两个子元素
            Array.from(e.currentTarget.parentElement.parentElement.parentElement.parentElement.children).forEach(function(item, k){
                if(item.tagName == 'TBODY'){  // 判断为tbody子元素
                    if(item.dataset.empty){ // 判断是否为空表格
                        item.innerHTML = "";
                        item.removeAttribute("data-empty")
                    }
                    var table = document.createElement("table");
                    table.innerHTML = oneRowHtml;
                    function addTd(tr){
                        trEle = tr;
                        if("{$this->enableInsertEleAfterActive}"){
                            let activeObj = item.querySelector("tr.active");
                            if(activeObj){
                                $(tr).insertAfter(activeObj);
                            }else{
                                item.append(tr);
                            }
                        }else{
                            item.append(tr);
                        }

                        if(script){
                            eval(script);
                        }
                    }
                    Array.from(table.children).forEach(function(child){
                        if(child.tagName == "TBODY"){
                            Array.from(child.children).forEach(function(vo){
                                addTd(vo);
                            });
                        }else if(child.tagName == "TR"){
                            addTd(child);
                        }
                    });
                }
            });
            return trEle;
        }
        var fn = {$eventClosure};
        if(fn){
            fn.call(this, e, appendContent);
        }else{
            appendContent();
        }
    });
});
SCRIPT;
        Admin::script($script);
        return $this;
    }

    /**
     * @param $eventClosure
     * @return $this
     */
    protected function removeEvent($eventClosure)
    {
        $emptyBody = str_replace('/', '\/', $this->compressHtml($this->multiList->emptyBody()));
        $btnClass = 'table-list-btn-remove-' . $this->getUniqueKey();
        $tableKey = $this->getTableKey();
        $script = <<<SCRIPT
$(function(){
    $("#{$tableKey}").on("click", ".{$btnClass}", function(e){
        function remove(ele){
            if(ele.nextElementSibling && ele.nextElementSibling.dataset && ele.nextElementSibling.dataset.sub == 'true'){
                remove(ele.nextElementSibling);
            }
            ele.remove();
        }
        let doRemove = function(){
            var parent = e.currentTarget.parentElement.parentElement.parentElement;
            remove(e.currentTarget.parentElement.parentElement);
            if(parent && Array.from(parent.children).length == 0){
                parent.innerHTML = `{$emptyBody}`;
                parent.setAttribute("data-empty","true");
            }
        }
        var fn = {$eventClosure};
        if(fn){
            fn.call(this, e, doRemove);
        }else{
            doRemove();
        }
    });
});
SCRIPT;
        Admin::script($script);
        return $this;
    }

    /**
     * @param $jsEventClosure
     * @return $this
     */
    public function showButton($jsEventClosure)
    {
        $this->showButton = true;
        if (is_callable($jsEventClosure)) {
            $this->buttonEventClosure = $this->compressHtml(call_user_func($jsEventClosure));
            return $this;
        }
        $this->buttonEventClosure = $this->compressHtml($jsEventClosure);
        return $this;
    }

    /**
     * @param bool $enable
     * @return $this
     */
    public function enableInsertEleAfterActive($enable = true)
    {
        $this->enableInsertEleAfterActive = $enable;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getKeyVariableName()
    {
        return $this->getUniqueKey();
    }


    /**
     * @return string
     */
    public function tableListBody()
    {
        if (empty($this->value) && empty($this->createEmpty)) {
            return '';
        }
        $expandOrModal = '';
        $bodyContent = $this->trStart();
        $script = self::$scripts[$this->getTableKey()] ?? '';
        $noScript = empty($script);
        foreach ($this->columns as $key => $column) {
            $field = $column->createEmpty($this->createEmpty)
                ->keyIsVariable($this->keyIsVariable, $this->getKeyVariableName())
                ->renderValue($this->value, $this->key);
            if ($column->type == 'hidden') {
                $bodyContent .= $field->render();
                continue;
            }
            $bodyContent .= $this->tdStart($field) . $field->render() . $this->tdEnd();
            if ($noScript) {
                $script .= $this->createEmpty ? $field->getOneRowScript() : $field->getScript();
            }
            $expandOrModal .= $field->renderSub();
        }
        self::$scripts[$this->getTableKey() . ($this->createEmpty ? '_oneRow' : '')] = $script;
        if ($noScript) {
            Admin::script($script);
        }
        $bodyContent .= $this->removeButton() . $this->trEnd() . $expandOrModal;
        return $bodyContent;
    }

    /**
     * @return string
     */
    public function getOneRowHtml()
    {
        $this->createEmpty = true;
        $content = $this->createEmpty(true)->keyIsVariable(true)->tableListBody();
//        dump(self::$scripts);
        return $content;
    }


    /**
     * @return string
     */
    public function tableListEnd()
    {
        return $this->tbodyEnd() . $this->tableEnd();
    }

    /**
     * @return string
     */
    public function trStart()
    {
        return '<tr>';
    }

    /**
     * @return string
     */
    public function trEnd()
    {
        return '</tr>';
    }

    /**
     * @param bool $empty
     * @return string
     */
    public function tbodyStart($empty = false)
    {
        if ($empty) {
            return '<tbody data-empty="true">';
        }
        return '<tbody>';
    }

    /**
     * @return string
     */
    public function tbodyEnd()
    {
        return '</tbody>';
    }

    /**
     * @return string
     */
    public function theadStart()
    {
        return '<thead>';
    }

    /**
     * @return string
     */
    public function theadEnd()
    {
        return '</thead>';
    }

    public function getTableKey()
    {
        return 'grid-table-' . $this->multiList->getUniqueKey(); // .$this->getUniqueKey()
    }

    /**
     * @return string
     */
    public function tableStart()
    {
        return '<table class="table table-hover" id="' . $this->getTableKey() . '">';
    }

    /**
     * @return string
     */
    public function tableEnd()
    {
        return '</table>';
    }

    /**
     * @param BaseColumn $column
     * @return string
     */
    public function thStart(BaseColumn $column)
    {
        return '<th style="width:' . $column->width . '" class="">';
    }

    /**
     * @return string
     */
    public function thEnd()
    {
        return '</th>';
    }


    /**
     * @param Field $field
     * @return string
     */
    public function tdStart(Field $field)
    {
        return '<td class="' . $field->getClass() . '" style="' . $this->checkStyle($this->getStyle()) . '">';
    }

    /**
     * @return string
     */
    public function tdEnd()
    {
        return '</td>';
    }


    /**
     * @return mixed
     */
    public function getStyle()
    {
        return $this->style;
    }

    protected function checkStyle($style)
    {
        if (is_array($style)) {
            $str = '';
            foreach ($style as $key => $item) {
                $str .= $key . ':' . $item . ';';
            }
            return $str;
        }
        return $style;
    }

    /**
     * @param $style
     * @return $this
     */
    public function style($style)
    {
        $this->style = $style;
        return $this;
    }

}
