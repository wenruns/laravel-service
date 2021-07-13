<?php
/**
 * Created by PhpStorm.
 * User: wen
 * Date: 2021/7/10
 * Time: 7:28
 */

namespace WenRuns\Laravel\Admin;


use Closure;
use WenRuns\Laravel\Admin\Form\Field\ApiSelect;
use WenRuns\Laravel\Admin\Form\Field\CheckboxTree;
use WenRuns\Laravel\Admin\Form\Field\InputSelect;
use WenRuns\Laravel\Admin\Form\Field\MultiCheckbox;
use WenRuns\Laravel\Admin\Form\Field\MultiList\MultiList;
use WenRuns\Laravel\Admin\Form\Field\Tabs;

/**
 * Class Form
 * @package WenRuns\Laravel\Admin
 * @method MultiList        multiList($column, \Closure $closure, $label = '')
 * @method ApiSelect        apiSelect($column, $label = '')
 * @method CheckboxTree     checkboxTree($column, $label = '')
 * @method InputSelect      inputSelect($column, $label = '')
 * @method MultiCheckbox    multiCheckbox($column, $label = '')
 * @method Tabs             wenTab($title, \Closure $closure)
 */
class Form extends \Encore\Admin\Form
{
    public function __construct($model, Closure $callback = null)
    {
        parent::__construct($model, $callback);
    }
}
