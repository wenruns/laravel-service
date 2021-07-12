<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/19
 * Time: 16:12
 */

namespace WenRuns\Laravel\Admin\Form\Field;


use Encore\Admin\Facades\Admin;
use Encore\Admin\Form\Field;
use WenRuns\Laravel\Laravel;


class CheckboxTree extends Field
{
    protected $view = 'WenAdmin::form.treecheckbox';

    protected $_showLabel = true;

    protected $_unique = '';

    protected $_hide_checkbox = false;

    protected $_javascript_func = '';

    protected $_spread_checked = false;

    protected $_spread = false;

    protected $_onchange_event = null;

    protected $_min_width = '100%';

    protected $_min_height = '100%';

    protected $_max_width = '100%';

    protected $_max_height = '100%';

    protected $_disabled = false;

    /**
     * CheckBox constructor.
     * @param string $column
     * @param array $arguments
     */
    public function __construct($column = '', array $arguments = [])
    {
        Laravel::loadJs('form/tree.js');
        Laravel::loadCss('form/tree.min.css');
        parent::__construct($column, $arguments);
//        if (empty($this->label)) {
//            $this->_showLabel = false;
//        }
        $this->_unique = md5($this->column . mt_rand(10000, 99999) . time());

    }

    /**
     * 追加类名
     * @param $option
     * @return string
     */
    protected function appendClass($option)
    {
        return isset($option['className']) ? $option['className'] : '';
    }

    /**
     * 是否禁用
     * @param $option
     * @return bool
     */
    protected function isDisabled($option)
    {
        return isset($option['disabled']) ? $option['disabled'] : $this->_disabled;
    }

    /**
     * 附加数据
     * @param $option
     * @return string
     */
    protected function appendData($option)
    {
        return isset($option['datas']) ? $option['datas'] : '';
    }

    /**
     * 是否选中
     * @param $option
     * @return bool
     */
    protected function isChecked($option)
    {
        return isset($option['checked']) ? $option['checked'] : $this->isDefault($option);
    }

    /**
     * 默认值检测
     * @param $option
     * @return bool
     */
    protected function isDefault($option)
    {
        $name = isset($option['name']) && $option['name'] ? $option['name'] : $this->column;
        $value = $option['value'];
        $defaults = $this->default;
        if (is_array($defaults)) {
            if (isset($defaults[$name])) {
                return in_array($value, $defaults[$name]);
            } else {
                return in_array($value, $defaults);
            }
        } else {
            return $defaults == $value;
        }
    }


    /**
     * 字段名称检测
     * @param $option
     * @return array|mixed|string
     */
    protected function checkName($option)
    {
        return isset($option['name']) && $option['name'] ? $option['name'] : $this->column;
    }

    /**
     * 值检测
     * @param $option
     * @return string
     */
    protected function checkValue($option)
    {
        return isset($option['value']) ? $option['value'] : '';
    }

    /**
     * 文本检测
     * @param $option
     * @return string
     */
    protected function checkText($option)
    {
        return isset($option['text']) ? $option['text'] : '';
    }

    /**
     * 是否显示复选框
     * @param $option
     * @return bool
     */
    protected function isShow($option)
    {
        return isset($option['isShow']) ? $option['isShow'] : !$this->_hide_checkbox;
    }

    /**
     * 格式化选项
     * @param $options
     * @return mixed
     */
    protected function formatOptions($options)
    {
        if (isset($options['text'])) {
            return [$this->makeOption($options)];
        }
        foreach ($options as $key => $option) {
            $options[$key] = $this->makeOption($option);
        }
        return $options;
    }

    protected function makeOption($option)
    {
        $option['checked'] = $this->isChecked($option);
        $option['className'] = $this->appendClass($option);
        $option['disabled'] = $this->isDisabled($option);
        $option['datas'] = $this->appendData($option);
        $option['name'] = $this->checkName($option);
        $option['value'] = $this->checkValue($option);
        $option['text'] = $this->checkText($option);
        if (isset($option['sub']) && !empty($option['sub'])) {
            $option['sub'] = $this->formatOptions($option['sub']);
        }
        return $option;
    }

    /**
     * 获取选项
     * @return false|string
     */
    protected function getOptions()
    {
        return json_encode($this->formatOptions($this->options));
    }


    /**
     * 不显示label
     * @param bool $enable
     * @return $this
     */
    public function disableLabel($enable = false)
    {
        $this->_showLabel = $enable;
        return $this;
    }

    /**
     * 选项数组
     * @param array $options
     * @return $this|Field
     */
    public function options($options = [])
    {
        $this->options = $options;
        return $this;
    }

    /**
     * 隐藏复选框
     * @param bool $hideCheckBox
     * @return $this
     */
    public function hideCheckBox($hideCheckBox = true)
    {
        $this->_hide_checkbox = $hideCheckBox;
        return $this;
    }

    /**
     * javascript函数
     * @param $jsCallback
     * @return $this
     */
    public function onReady($jsCallback)
    {
        $this->_javascript_func = $this->compressHtml($jsCallback);
        return $this;
    }

    /**
     * 设置最小宽高
     * @param $width
     * @param $height
     * @return $this
     */
    public function min($width, $height)
    {
        $this->_min_height = $height;
        $this->_min_width = $width;
        return $this;
    }

    /**
     * 设置最大宽高
     * @param $width
     * @param $height
     * @return $this
     */
    public function max($width, $height)
    {
        $this->_max_height = $height;
        $this->_max_width = $width;
        return $this;
    }

    /**
     * 展开已选选项
     * @param bool $spreadChecked
     * @return $this
     */
    public function spreadChecked($spreadChecked = true)
    {
        $this->_spread_checked = $spreadChecked;
        return $this;
    }

    /**
     * 展开所有项
     * @param bool $spread
     * @return $this
     */
    public function spread($spread = true)
    {
        $this->_spread = $spread;
        return $this;
    }

    /**
     * 改变事件
     * @param null $changeEvent
     * @return $this
     */
    public function onChange($changeEvent = null)
    {
        $this->_onchange_event = $this->compressHtml($changeEvent);
        return $this;
    }


    protected function compressHtml($string)
    {
        return ltrim(rtrim(preg_replace(array("/> *([^ ]*) *</", "//", "'/\*[^*]*\*/'", "/\r\n/", "/\n/", "/\t/", '/>[ ]+</'),
            array(">\\1<", '', '', '', '', '', '><'), $string)));
    }


    /**
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function render()
    {
        $this->addVariables([
            'showLabel' => $this->_showLabel,
            'unique' => $this->_unique,
            'options' => $this->getOptions(),
            'javascriptFunc' => $this->_javascript_func,
            'configs' => json_encode([
                'hideCheckBox' => $this->_hide_checkbox,
                'spread' => $this->_spread,
                'spreadChecked' => $this->_spread_checked,
                'uniqueKey' => $this->_unique,
            ]),
            'changeEvent' => $this->_onchange_event,
            'maxWidth' => $this->_max_width,
            'maxHeight' => $this->_max_height,
            'minWidth' => $this->_min_width,
            'minHeight' => $this->_min_height,
        ]);
        return parent::render();    // TODO: Change the autogenerated stub
    }


    /**
     * 是否可以打钩
     * @param bool $disabled
     * @return $this
     */
    public function disabled($disabled = true)
    {
        $this->_disabled = $disabled;
        return $this;
    }


}
